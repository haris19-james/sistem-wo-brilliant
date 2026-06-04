<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Countdown Hari-H untuk kartu acara mendatang (client).
 * Sumber tanggal: pesanans.tanggal_acara (event date).
 */
class EventCountdown
{
    /**
     * Selisih hari dari hari ini ke tanggal acara (pembulatan ke atas untuk partial days).
     * H-5 = acara 5 hari lagi; H-0 = hari ini; nilai negatif = acara sudah lewat.
     */
    public static function getDaysRemaining(CarbonInterface|string|null $eventDate, ?CarbonInterface $today = null): ?int
    {
        if ($eventDate === null || $eventDate === '') {
            return null;
        }

        $event = $eventDate instanceof CarbonInterface
            ? $eventDate->copy()->startOfDay()
            : Carbon::parse($eventDate)->startOfDay();

        $now = ($today ?? Carbon::today())->copy()->startOfDay();

        return (int) $now->diffInDays($event, false);
    }

    /**
     * Label badge format H-X (atau H+1 jika sudah lewat).
     */
    public static function label(?int $daysRemaining): ?string
    {
        if ($daysRemaining === null) {
            return null;
        }

        if ($daysRemaining < 0) {
            return 'H+'.abs($daysRemaining);
        }

        return 'H-'.$daysRemaining;
    }

    /**
     * @return array{days: ?int, label: ?string, tier: string, class: string, title: string}|null
     */
    public static function badgeForEventDate(CarbonInterface|string|null $eventDate, ?CarbonInterface $today = null): ?array
    {
        $days = self::getDaysRemaining($eventDate, $today);
        if ($days === null) {
            return null;
        }

        $label = self::label($days);
        $tier = self::tier($days);

        return [
            'days' => $days,
            'label' => $label,
            'tier' => $tier,
            'class' => self::badgeClass($tier),
            'title' => self::title($days),
        ];
    }

    public static function tier(int $daysRemaining): string
    {
        if ($daysRemaining < 0) {
            return 'past';
        }

        if ($daysRemaining === 0) {
            return 'event_day';
        }

        if ($daysRemaining <= 7) {
            return 'urgent';
        }

        if ($daysRemaining <= 30) {
            return 'preparation';
        }

        return 'early';
    }

    public static function badgeClass(string $tier): string
    {
        return match ($tier) {
            'event_day' => 'bg-lime text-bottle border border-bottleBright shadow-sm',
            'urgent' => 'bg-red-600 text-white border border-red-700 shadow-sm',
            'preparation' => 'bg-orange-500 text-white border border-orange-600 shadow-sm',
            'past' => 'bg-gray-200 text-gray-600 border border-gray-300',
            default => 'bg-leafSoft text-bottle border border-leaf',
        };
    }

    public static function title(int $daysRemaining): string
    {
        if ($daysRemaining < 0) {
            return 'Acara telah berlangsung '.abs($daysRemaining).' hari lalu';
        }

        if ($daysRemaining === 0) {
            return 'Hari-H — selamat menjalankan acara!';
        }

        if ($daysRemaining <= 7) {
            return "Finalisasi — {$daysRemaining} hari menuju acara";
        }

        if ($daysRemaining <= 30) {
            return "Persiapan — {$daysRemaining} hari menuju acara";
        }

        return "{$daysRemaining} hari menuju acara";
    }
}
