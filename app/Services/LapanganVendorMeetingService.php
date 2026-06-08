<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\VendorMeeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;

class LapanganVendorMeetingService
{
    /**
     * @param  array{tanggal?: string|null, klien?: string|null, status?: string|null}  $filters
     * @return array{
     *     groups: SupportCollection,
     *     total_meetings: int,
     *     filters: array{tanggal: ?string, klien: string, status: string, range_label: string}
     * }
     */
    public function groupedForKorlap(int $korlapId, array $filters = []): array
    {
        $tanggal = ! empty($filters['tanggal']) ? Carbon::parse($filters['tanggal'])->toDateString() : null;
        $klien = trim((string) ($filters['klien'] ?? ''));
        $statusFilter = (string) ($filters['status'] ?? 'semua');

        $query = VendorMeeting::query()
            ->forKorlap($korlapId)
            ->with([
                'booking.user:id,name,email',
                'booking.paket:id,nama_paket',
                'vendor:id,nama_vendor',
            ]);

        if ($tanggal) {
            $query->whereDate('meeting_date', $tanggal);
            $rangeLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        } else {
            $rangeLabel = 'Semua tanggal';
        }

        if ($statusFilter === 'selesai') {
            $query->where('status', 'completed');
        } elseif ($statusFilter === 'aktif') {
            $query->whereIn('status', ['scheduled', 'ongoing']);
        }
        // 'semua' = tanpa filter status tambahan

        if ($klien !== '') {
            $query->whereHas('booking', function ($booking) use ($klien) {
                $booking->where('nama_pasangan', 'like', "%{$klien}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$klien}%")
                    ->orWhereHas('user', function ($user) use ($klien) {
                        $user->where('name', 'like', "%{$klien}%")
                            ->orWhere('email', 'like', "%{$klien}%");
                    });
            });
        }

        $meetings = $query
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get();

        $deduped = $this->deduplicateMeetings($meetings);
        $groups = $this->groupByClient($deduped);
        $groups = $this->mergeEligibleBookingsWithoutMeetings($korlapId, $groups, $filters, $tanggal);

        // Debug logging untuk verifikasi booking_id klien yang di-fetch
        try {
            $bookingIds = $deduped->pluck('booking_id')->unique()->values()->all();
            $clientDetails = $groups->map(function ($group) {
                return [
                    'client_name' => $group['client_name'] ?? null,
                    'booking_id' => $group['booking']['id'] ?? null,
                    'booking_nama' => $group['booking']['nama_pasangan'] ?? null,
                    'meeting_count' => count($group['meetings'] ?? []),
                ];
            })->all();

            Log::debug('[LapanganVendorMeeting] groupedForKorlap - Full Query Debug', [
                'korlap_id' => $korlapId,
                'filters_applied' => [
                    'tanggal' => $tanggal,
                    'klien_filter' => $klien,
                    'status_filter' => $statusFilter,
                ],
                'query_results' => [
                    'raw_meetings_count' => $meetings->count(),
                    'deduped_meetings_count' => $deduped->count(),
                    'booking_ids_in_results' => $bookingIds,
                    'grouped_count' => $groups->count(),
                ],
                'client_details' => $clientDetails,
                'target_client_haris_nilam' => [
                    'found' => $groups->contains(fn ($g) => stripos($g['client_name'] ?? '', 'Haris') !== false || stripos($g['client_name'] ?? '', 'Nilam') !== false),
                    'matching_groups' => $groups->filter(fn ($g) => stripos($g['client_name'] ?? '', 'Haris') !== false || stripos($g['client_name'] ?? '', 'Nilam') !== false)->values()->all(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('[LapanganVendorMeeting] groupedForKorlap debug logging error: '.$e->getMessage());
        }

        return [
            'groups' => $groups,
            'total_meetings' => $deduped->count(),
            'filters' => [
                'tanggal' => $tanggal,
                'klien' => $klien,
                'status' => $statusFilter,
                'range_label' => $rangeLabel,
            ],
        ];
    }

    /**
     * Hapus duplikat yang benar-benar sama: booking + tanggal + waktu + judul + vendor.
     *
     * Meeting berbeda untuk vendor yang sama tanggal/waktu/judul tetap harus tampil.
     */
    public function deduplicateMeetings(Collection $meetings): Collection
    {
        $seen = [];
        $unique = new Collection;

        foreach ($meetings as $meeting) {
            $key = implode('|', [
                $meeting->booking_id ?? 'none',
                $meeting->meeting_date?->format('Y-m-d') ?? '',
                trim((string) $meeting->meeting_time),
                strtolower(trim((string) $meeting->title)),
                $meeting->vendor_id ?? 'none',
            ]);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique->push($meeting);
        }

        return $unique;
    }

    /**
     * Kelompokkan per klien (user pada booking).
     *
     * @return SupportCollection<int, array{client_key: string, client_name: string, booking: ?\App\Models\Pesanan, meetings: Collection, warnings: array}>
     */
    public function groupByClient(Collection $meetings): SupportCollection
    {
        return $meetings
            ->groupBy(function (VendorMeeting $meeting) {
                $booking = $meeting->booking;
                $userId = $booking?->user_id ?? 0;
                $clientName = trim((string) ($booking?->user?->name ?? $booking?->nama_pasangan ?? 'Klien Tanpa Nama'));

                return $userId.'::'.$clientName;
            })
            ->map(function (Collection $clientMeetings, string $key) {
                $first = $clientMeetings->first();
                $booking = $first?->booking;
                $clientName = trim((string) ($booking?->user?->name ?? $booking?->nama_pasangan ?? 'Klien Tanpa Nama'));

                return [
                    'client_key' => $key,
                    'client_name' => $clientName,
                    'booking' => $booking,
                    'meetings' => $clientMeetings->sortBy(fn ($m) => $m->meeting_date->format('Y-m-d').' '.$m->meeting_time)->values(),
                    'warnings' => $this->bookingWarnings($booking),
                    'is_lunas' => $booking?->status_pembayaran === 'fully_paid' || $booking?->isPembayaranLunas(),
                ];
            })
            ->sortBy('client_name')
            ->values();
    }

    /**
     * @return list<string>
     */
    public function bookingWarnings(?\App\Models\Pesanan $booking): array
    {
        if (! $booking) {
            return ['Booking tidak terhubung ke data pesanan.'];
        }

        $warnings = [];

        if (! in_array($booking->status_pembayaran, ['dp_paid', 'fully_paid'], true) && ! $booking->hasMinimalDpPaid()) {
            $warnings[] = 'Pembayaran belum diverifikasi admin (DP/Lunas).';
        }

        if (! $booking->korlap_id) {
            $warnings[] = 'Belum ada Koordinator Lapangan (Korlap) pada booking ini.';
        }

        if (! $booking->isConfirmedForLapangan()) {
            $warnings[] = 'Booking belum diverifikasi untuk tim lapangan.';
        }

        return $warnings;
    }

    /**
     * Meeting vendor mendatang untuk widget dashboard Korlap.
     * Terikat booking_id milik Korlap — bukan daftar global.
     *
     * @return array{
     *     upcoming: Collection,
     *     total_upcoming: int,
     *     bookings_with_meetings: \Illuminate\Database\Eloquent\Collection<int, Pesanan>
     * }
     */
    public function dashboardForKorlap(int $korlapId, int $limit = 8): array
    {
        $start = Carbon::today();
        $end = $start->copy()->addDays(14);

        $bookingsWithMeetings = Pesanan::query()
            ->where('korlap_id', $korlapId)
            ->where('status', '!=', 'Dibatalkan')
            ->whereNotIn('status_pemesanan', ['cancelled', 'canceled', 'expired', 'pending_cancellation'])
            ->with([
                'user:id,name,email',
                'paket:id,nama_paket',
                'vendorMeetings' => function ($query) use ($start, $end) {
                    $query->whereBetween('meeting_date', [$start, $end])
                        ->orderBy('meeting_date')
                        ->orderBy('meeting_time');
                },
            ])
            ->orderBy('tanggal_acara')
            ->get();

        $upcoming = VendorMeeting::query()
            ->forKorlap($korlapId)
            ->whereBetween('meeting_date', [$start, $end])
            // Keep the upcoming selection but avoid excluding other relevant
            // meetings by over-restricting the status in other queries.
            ->with([
                'booking.user:id,name,email',
                'booking.paket:id,nama_paket',
                'vendor:id,nama_vendor',
            ])
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get();

        $upcoming = $this->deduplicateMeetings($upcoming)->take($limit);

        // Debug: log session booking_id (if any) and booking_ids present in meetings
        try {
            $sessionBookingId = session()->get('booking_id');
            $meetingBookingIds = $upcoming->pluck('booking_id')->unique()->values()->all();
            \Illuminate\Support\Facades\Log::debug('[LapanganVendorMeeting] dashboardForKorlap debug', [
                'korlap_id' => $korlapId,
                'session_booking_id' => $sessionBookingId,
                'meeting_booking_ids' => $meetingBookingIds,
            ]);
        } catch (\Throwable $e) {
            // Don't break dashboard rendering for logging errors
            \Illuminate\Support\Facades\Log::warning('[LapanganVendorMeeting] debug logging failed: '.$e->getMessage());
        }

        return [
            'upcoming' => $upcoming,
            'total_upcoming' => $upcoming->count(),
            'bookings_with_meetings' => $bookingsWithMeetings,
        ];
    }

    /**
     * Sisipkan booking eligible (DP/Lunas) yang belum punya meeting agar UI tetap terhubung ke klien.
     *
     * @param  SupportCollection<int, array{client_key: string, client_name: string, booking: ?Pesanan, meetings: Collection, warnings: array}>  $groups
     * @param  array{tanggal?: string|null, klien?: string|null, status?: string|null}  $filters
     */
    protected function mergeEligibleBookingsWithoutMeetings(
        int $korlapId,
        SupportCollection $groups,
        array $filters,
        ?string $tanggal
    ): SupportCollection {
        if ($tanggal) {
            return $groups;
        }

        if (($filters['status'] ?? 'aktif') === 'selesai') {
            return $groups;
        }

        $klien = trim((string) ($filters['klien'] ?? ''));
        $existingBookingIds = $groups
            ->pluck('booking.id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $eligibleBookings = Pesanan::visibleToKorlap($korlapId)
            ->with(['user:id,name,email', 'paket:id,nama_paket'])
            ->orderBy('nama_pasangan')
            ->get();

        foreach ($eligibleBookings as $booking) {
            if (in_array((int) $booking->id, $existingBookingIds, true)) {
                continue;
            }

            if ($klien !== '' && ! $this->bookingMatchesClientFilter($booking, $klien)) {
                continue;
            }

            $clientName = trim((string) ($booking->user?->name ?? $booking->nama_pasangan ?? 'Klien Tanpa Nama'));

            $groups->push([
                'client_key' => ($booking->user_id ?? 0).'::'.$clientName,
                'client_name' => $clientName,
                'booking' => $booking,
                'meetings' => new Collection,
                'warnings' => $this->bookingWarnings($booking),
                'is_lunas' => $booking->status_pembayaran === 'fully_paid' || $booking->isPembayaranLunas(),
                'has_no_meetings' => true,
            ]);
        }

        return $groups->sortBy('client_name')->values();
    }

    protected function bookingMatchesClientFilter(Pesanan $booking, string $klien): bool
    {
        $needle = strtolower($klien);

        return str_contains(strtolower((string) $booking->nama_pasangan), $needle)
            || str_contains(strtolower((string) $booking->nomor_pesanan), $needle)
            || str_contains(strtolower((string) ($booking->user?->name ?? '')), $needle)
            || str_contains(strtolower((string) ($booking->user?->email ?? '')), $needle);
    }
}
