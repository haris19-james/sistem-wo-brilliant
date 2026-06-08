<?php

namespace App\Support;

use App\Models\Pesanan;
use App\Models\VendorMeeting;
use App\Services\CustomerVendorMeetingService;
use App\Services\ScheduleAccessService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class JadwalTerpaduService
{
    /** @var list<string> */
    public const EAGER = [
        'paket',
        'korlap',
        'user',
        'vendorMeetings.korlap',
        'kuaChecklist.updatedByUser',
    ];

    /** @return list<string> */
    public static function eagerRelations(): array
    {
        $relations = ['paket', 'korlap', 'user'];

        if (Schema::hasTable('vendor_meetings')) {
            $relations[] = 'vendorMeetings.korlap';
        }

        if (Schema::hasTable('kua_checklists')) {
            $relations[] = 'kuaChecklist.updatedByUser';
        }

        return $relations;
    }

    /**
     * @return array{
     *     pesanans: Collection<int, Pesanan>,
     *     pesanan: Pesanan|null,
     *     mainEvent: Pesanan|null,
     *     timelineItems: Collection<int, array<string, mixed>>,
     *     kuaChecklist: \App\Models\KuaChecklist|null,
     *     hasKorlap: bool,
     *     panel: string
     * }
     */
    public static function forAdmin(?int $pesananId = null): array
    {
        $pesanans = Pesanan::with(self::eagerRelations())
            ->whereNotIn('status', ['Dibatalkan'])
            ->whereIn('status', ['Menunggu', 'Sedang Berlangsung'])
            ->orderBy('tanggal_acara')
            ->get();

        $selected = self::resolveSelectedPesanan($pesanans, $pesananId);

        return self::compose($pesanans, $selected, 'admin');
    }

    /**
     * @return array{
     *     pesanans: Collection<int, Pesanan>,
     *     pesanan: Pesanan|null,
     *     mainEvent: Pesanan|null,
     *     timelineItems: Collection<int, array<string, mixed>>,
     *     kuaChecklist: \App\Models\KuaChecklist|null,
     *     hasKorlap: bool,
     *     panel: string
     * }
     */
    public static function forCustomer(int $userId, ?int $pesananId = null): array
    {
        $pesanans = Pesanan::query()
            ->where('user_id', $userId)
            ->whereNotIn('status', ['Dibatalkan'])
            ->with(self::eagerRelations())
            ->orderByDesc('tanggal_acara')
            ->get();

        $selected = self::resolveSelectedPesanan($pesanans, $pesananId, $userId);

        if ($selected) {
            $selected->loadMissing(self::eagerRelations());
        }

        return self::compose($pesanans, $selected, 'client');
    }

    /**
     * @param  Collection<int, Pesanan>  $pesanans
     * @return array{
     *     pesanans: Collection<int, Pesanan>,
     *     pesanan: Pesanan|null,
     *     mainEvent: Pesanan|null,
     *     timelineItems: Collection<int, array<string, mixed>>,
     *     kuaChecklist: \App\Models\KuaChecklist|null,
     *     hasKorlap: bool,
     *     panel: string
     * }
     */
    private static function compose(Collection $pesanans, ?Pesanan $selected, string $panel): array
    {
        $meetings = collect();
        if ($selected && Schema::hasTable('vendor_meetings')) {
            $meetings = app(CustomerVendorMeetingService::class)->forBooking($selected);
        }

        return [
            'pesanans' => $pesanans,
            'pesanan' => $selected,
            'mainEvent' => $selected,
            'timelineItems' => self::buildTimelineItems($meetings, $selected),
            'kuaChecklist' => Schema::hasTable('kua_checklists') ? $selected?->kuaChecklist : null,
            'hasKorlap' => (bool) ($selected?->korlap_id),
            'canAddVendorMeeting' => self::canAddVendorMeeting($selected),
            'panel' => $panel,
        ];
    }

    /**
     * @param  Collection<int, Pesanan>  $pesanans
     */
    public static function resolveSelectedPesanan(Collection $pesanans, ?int $pesananId, ?int $userId = null): ?Pesanan
    {
        if ($pesananId) {
            $found = $pesanans->firstWhere('id', $pesananId);

            if ($found) {
                return $found;
            }

            $query = Pesanan::with(self::eagerRelations())->where('id', $pesananId);

            if ($userId) {
                $query->where('user_id', $userId);
            }

            return $query->first();
        }

        return self::resolveMainEvent($pesanans);
    }

    /**
     * @param  Collection<int, Pesanan>  $pesanans
     */
    public static function resolveMainEvent(Collection $pesanans): ?Pesanan
    {
        if ($pesanans->isEmpty()) {
            return null;
        }

        $today = now()->startOfDay();

        $todayEvent = $pesanans->first(function (Pesanan $p) use ($today) {
            return $p->status === 'Sedang Berlangsung'
                && $p->tanggal_acara
                && $p->tanggal_acara->startOfDay()->equalTo($today);
        });

        if ($todayEvent) {
            return $todayEvent;
        }

        $ongoing = $pesanans
            ->where('status', 'Sedang Berlangsung')
            ->sortBy('tanggal_acara')
            ->first();

        if ($ongoing) {
            return $ongoing;
        }

        $future = $pesanans
            ->filter(fn (Pesanan $p) => $p->tanggal_acara && $p->tanggal_acara->startOfDay()->gte($today))
            ->sortBy('tanggal_acara')
            ->first();

        if ($future) {
            return $future;
        }

        return $pesanans->sortByDesc('tanggal_acara')->first();
    }

    /**
     * @param  Collection<int, VendorMeeting>  $meetings
     * @return Collection<int, array<string, mixed>>
     */
    public static function buildTimelineItems(Collection $meetings, ?Pesanan $booking = null): Collection
    {
        return $meetings
            ->sortBy(fn (VendorMeeting $m) => $m->meeting_date->format('Y-m-d').' '.($m->meeting_time ?? '00:00:00'))
            ->values()
            ->map(function (VendorMeeting $m) use ($booking) {
                $relatedBooking = $m->relationLoaded('booking') ? $m->booking : $booking;
                $pasangan = $relatedBooking?->nama_pasangan;

                $isTechnical = $m->agenda_type === 'technical_meeting';
                $isCompleted = $m->status === 'completed';
                $isKua = str_contains(strtolower($m->title), 'kua');

                $badge = match (true) {
                    $isCompleted => 'Selesai',
                    $m->status === 'ongoing' => 'Sedang Berlangsung',
                    default => 'Akan Datang',
                };

                $badgeClass = match ($badge) {
                    'Selesai' => 'bg-green-100 text-green-800 border border-green-200',
                    'Sedang Berlangsung' => 'bg-leafSoft text-green-700 border border-green-200',
                    default => 'bg-gray-100 text-gray-600 border border-gray-200',
                };

                $checklistStatus = null;
                if ($m->agenda_type === 'self_preparation') {
                    if ($isKua) {
                        $checklist = $relatedBooking?->kuaChecklist;
                        $checklistStatus = $checklist?->isComplete()
                            ? 'Manual Checklist: Lengkap'
                            : 'Manual Checklist: Pending';
                    } else {
                        $checklistStatus = $isCompleted
                            ? 'Checklist: Selesai'
                            : 'Manual Checklist: Pending';
                    }
                }

                $displayTitle = $m->title;
                if ($pasangan && $isTechnical) {
                    $displayTitle = $m->title.' ('.$pasangan.')';
                } elseif ($pasangan && ! $isTechnical) {
                    $displayTitle = $m->title.' ('.$pasangan.')';
                }

                $tingkatAkses = ScheduleAccessService::inferTingkatAkses([
                    'agenda_type' => $m->agenda_type,
                    'title' => $displayTitle,
                ]);

                return [
                    'id' => $m->id,
                    'sort_key' => $m->meeting_date->format('Y-m-d').' '.($m->meeting_time ?? '00:00:00'),
                    'date_label' => $m->meeting_date->translatedFormat('d M Y'),
                    'time_label' => $m->meeting_time ? substr((string) $m->meeting_time, 0, 5) : null,
                    'title' => $displayTitle,
                    'pasangan' => $pasangan,
                    'agenda_type' => $m->agenda_type,
                    'agenda_icon' => $m->agenda_type_icon,
                    'badge' => $badge,
                    'badge_class' => $badgeClass,
                    'checklist_status' => $checklistStatus,
                    'is_kua' => $isKua,
                    'tingkat_akses' => $tingkatAkses,
                ];
            });
    }

    /**
     * @return array{label: string, class: string}
     */
    public static function mainEventStatus(Pesanan $pesanan): array
    {
        $resolved = BookingDynamicStatus::resolve($pesanan);

        return [
            'label' => $resolved['label'],
            'class' => $resolved['badge_class'],
        ];
    }

    public static function canAddVendorMeeting(?Pesanan $pesanan): bool
    {
        if (! $pesanan) {
            return false;
        }

        return $pesanan->isPembayaranLunas() || (bool) $pesanan->korlap_id;
    }
}
