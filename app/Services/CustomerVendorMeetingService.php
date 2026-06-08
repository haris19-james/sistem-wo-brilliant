<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\VendorMeeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Schema;

class CustomerVendorMeetingService
{
    /**
     * Query dasar: meeting vendor terikat booking_id milik user (bukan daftar global).
     */
    public function baseQueryForUser(int $userId)
    {
        return VendorMeeting::query()
            ->whereHas('booking', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', '!=', 'Dibatalkan');
            })
            ->with([
                'booking:id,user_id,nomor_pesanan,nama_pasangan,status_pembayaran,status_pemesanan,akses_jadwal,korlap_id',
                'booking.user:id,name,email',
                'booking.paket:id,nama_paket',
                'korlap:id,name',
                'vendor:id,nama_vendor',
            ]);
    }

    /**
     * Meeting yang boleh ditampilkan ke klien (booking DP Terverifikasi / Lunas).
     */
    public function visibleForUser(int $userId): Collection
    {
        if (! Schema::hasTable('vendor_meetings')) {
            return new Collection;
        }

        return $this->baseQueryForUser($userId)
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get()
            ->filter(fn (VendorMeeting $meeting) => $this->meetingVisibleToCustomer($meeting))
            ->values();
    }

    /**
     * Meeting mendatang untuk widget dashboard klien.
     */
    public function upcomingForDashboard(int $userId, int $limit = 8): Collection
    {
        if (! Schema::hasTable('vendor_meetings')) {
            return new Collection;
        }

        return $this->baseQueryForUser($userId)
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereDate('meeting_date', '>=', Carbon::today())
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->limit($limit * 2)
            ->get()
            ->filter(fn (VendorMeeting $meeting) => $this->meetingVisibleToCustomer($meeting))
            ->take($limit)
            ->values();
    }

    /**
     * Semua meeting untuk satu booking (by booking_id / pesanan id).
     */
    public function forBooking(Pesanan $pesanan): Collection
    {
        if (! Schema::hasTable('vendor_meetings')) {
            return new Collection;
        }

        // NOTE: Return all vendor meetings attached to the given booking_id so
        // the client UI can always see schedules related to their booking.
        // Access checks (visibility) are handled elsewhere when needed.
        return $pesanan->vendorMeetings()
            ->with(['korlap:id,name', 'vendor:id,nama_vendor'])
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get();
    }

    /**
     * Kelompokkan per booking — termasuk booking tanpa meeting (LEFT JOIN style).
     *
     * @return SupportCollection<int, array{pesanan: Pesanan, meetings: Collection, has_no_meetings: bool}>
     */
    public function groupedByBooking(int $userId): SupportCollection
    {
        $pesanans = Pesanan::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'Dibatalkan')
            ->with([
                'paket:id,nama_paket',
                'user:id,name,email',
                'vendorMeetings' => fn ($q) => $q->orderBy('meeting_date')->orderBy('meeting_time'),
                'vendorMeetings.korlap:id,name',
                'vendorMeetings.vendor:id,nama_vendor',
            ])
            ->orderByDesc('tanggal_acara')
            ->get()
            ->filter(fn (Pesanan $p) => $this->bookingCanViewMeetings($p));

        return $pesanans->map(function (Pesanan $pesanan) {
            $meetings = $pesanan->vendorMeetings;

            return [
                'pesanan' => $pesanan,
                'meetings' => $meetings,
                'has_no_meetings' => $meetings->isEmpty(),
            ];
        })->values();
    }

    public function bookingCanViewMeetings(Pesanan $pesanan): bool
    {
        return ScheduleAccessService::canAccessVendorMeeting($pesanan);
    }

    public function meetingVisibleToCustomer(VendorMeeting $meeting): bool
    {
        $booking = $meeting->booking;

        if (! $booking) {
            return false;
        }

        return $this->bookingCanViewMeetings($booking);
    }
}
