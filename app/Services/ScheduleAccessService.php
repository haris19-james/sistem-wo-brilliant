<?php

namespace App\Services;

use App\Models\Pesanan;

class ScheduleAccessService
{
    /** @var list<string> */
    public const PARTIAL_AGENDA_TYPES = [
        'technical_meeting',
        'self_preparation',
        'internal_coordination',
    ];

    /** @var list<string> */
    public const FULL_AGENDA_TYPES = [
        'vendor_execution',
        'rundown_hari_h',
        'field_ops',
    ];

    /**
     * Apakah item jadwal boleh diakses Tim Lapangan berdasarkan status pembayaran.
     *
     * @param  array<string, mixed>  $timelineItem
     */
    public static function canAccessTimelineItem(Pesanan $pesanan, array $timelineItem): bool
    {
        $akses = $pesanan->akses_jadwal ?? self::resolveAksesFromPayment($pesanan);

        if ($akses === 'full' || $pesanan->isPembayaranLunas()) {
            return true;
        }

        if ($akses === 'none' || ! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            return false;
        }

        $required = $timelineItem['tingkat_akses'] ?? self::inferTingkatAkses($timelineItem);

        return $required === 'partial';
    }

    public static function canAccessVendorList(Pesanan $pesanan): bool
    {
        $akses = $pesanan->akses_jadwal ?? self::resolveAksesFromPayment($pesanan);

        return $akses === 'full' || $pesanan->isPembayaranLunas();
    }

    public static function canAccessRundown(Pesanan $pesanan): bool
    {
        return self::canAccessVendorList($pesanan);
    }

    /**
     * Jadwal meeting vendor: boleh saat DP terverifikasi (koordinasi awal).
     */
    public static function canAccessVendorMeeting(Pesanan $pesanan): bool
    {
        if (self::canAccessRundown($pesanan)) {
            return true;
        }

        $akses = $pesanan->akses_jadwal ?? self::resolveAksesFromPayment($pesanan);

        if ($akses === 'partial') {
            return true;
        }

        return in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)
            || $pesanan->hasMinimalDpPaid();
    }

    public static function resolveAksesFromPayment(Pesanan $pesanan): string
    {
        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return 'full';
        }

        if ($pesanan->status_pembayaran === 'dp_paid') {
            return 'partial';
        }

        return 'none';
    }

    public static function lockLabel(Pesanan $pesanan): ?string
    {
        if (self::canAccessVendorList($pesanan)) {
            return null;
        }

        if ($pesanan->status_pembayaran === 'dp_paid' || ($pesanan->akses_jadwal ?? '') === 'partial') {
            return 'Terkunci — Menunggu Pelunasan';
        }

        return 'Terkunci — Menunggu Verifikasi Pembayaran';
    }

    /**
     * @param  array<string, mixed>  $timelineItem
     */
    public static function inferTingkatAkses(array $timelineItem): string
    {
        $agendaType = (string) ($timelineItem['agenda_type'] ?? '');

        if (in_array($agendaType, self::FULL_AGENDA_TYPES, true)) {
            return 'full';
        }

        if ($agendaType === 'technical_meeting') {
            $title = strtolower((string) ($timelineItem['title'] ?? ''));

            return str_contains($title, 'vendor') || str_contains($title, 'eksternal')
                ? 'full'
                : 'partial';
        }

        return 'partial';
    }
}
