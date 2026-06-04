<?php

namespace App\Services;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PaymentDeadlineService
{
    public static function daysBeforeEvent(): int
    {
        return (int) config('pembayaran.pelunasan_hari_sebelum_acara', 14);
    }

    public static function warningDays(): int
    {
        return (int) config('pembayaran.deadline_warning_hari', 7);
    }

    public static function computeDeadlineDate(Pesanan $pesanan): ?Carbon
    {
        if (! $pesanan->tanggal_acara) {
            return null;
        }

        return Carbon::parse($pesanan->tanggal_acara)
            ->startOfDay()
            ->subDays(self::daysBeforeEvent());
    }

    /**
     * Hitung & simpan tanggal_jatuh_tempo + status_deadline.
     */
    public static function syncFor(Pesanan $pesanan, bool $persist = true): Pesanan
    {
        if (! Schema::hasColumn('pesanans', 'status_deadline')) {
            return $pesanan;
        }

        $deadline = self::computeDeadlineDate($pesanan);
        $status = self::resolveStatusDeadline($pesanan, $deadline);

        $pesanan->tanggal_jatuh_tempo = $deadline?->toDateString();
        $pesanan->status_deadline = $status;

        if ($persist && $pesanan->exists) {
            $pesanan->saveQuietly();
        }

        return $pesanan;
    }

    public static function resolveStatusDeadline(Pesanan $pesanan, ?Carbon $deadline = null): string
    {
        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return 'safe';
        }

        if (! in_array($pesanan->status_pembayaran, ['dp_paid'], true)) {
            return 'safe';
        }

        $deadline ??= self::computeDeadlineDate($pesanan);
        if (! $deadline) {
            return 'safe';
        }

        $today = now()->startOfDay();
        $deadlineDay = $deadline->copy()->startOfDay();

        if ($today->gt($deadlineDay)) {
            return 'overdue';
        }

        $daysLeft = (int) $today->diffInDays($deadlineDay, false);

        if ($daysLeft <= self::warningDays()) {
            return 'warning';
        }

        return 'safe';
    }

    public static function daysUntilDeadline(Pesanan $pesanan): ?int
    {
        $deadline = $pesanan->tanggal_jatuh_tempo
            ? Carbon::parse($pesanan->tanggal_jatuh_tempo)->startOfDay()
            : self::computeDeadlineDate($pesanan)?->startOfDay();

        if (! $deadline) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($deadline, false);
    }

    public static function isKorlapFrozen(Pesanan $pesanan): bool
    {
        self::syncFor($pesanan);

        return ($pesanan->status_deadline ?? 'safe') === 'overdue'
            && ! $pesanan->isPembayaranLunas()
            && $pesanan->status_pembayaran !== 'fully_paid';
    }

    public static function syncAllActive(): int
    {
        if (! Schema::hasColumn('pesanans', 'status_deadline')) {
            return 0;
        }

        $count = 0;

        Pesanan::query()
            ->whereNotIn('status', ['Dibatalkan'])
            ->whereNotNull('tanggal_acara')
            ->whereIn('status_pembayaran', ['dp_paid', 'unpaid', 'fully_paid'])
            ->chunkById(100, function ($pesanans) use (&$count) {
                foreach ($pesanans as $pesanan) {
                    self::syncFor($pesanan);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * @return array{type: string, icon: string, message: string, submessage?: string}|null
     */
    public static function customerBanner(Pesanan $pesanan): ?array
    {
        self::syncFor($pesanan);

        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return null;
        }

        if ($pesanan->status_pembayaran !== 'dp_paid') {
            return null;
        }

        $deadline = $pesanan->tanggal_jatuh_tempo
            ? Carbon::parse($pesanan->tanggal_jatuh_tempo)
            : self::computeDeadlineDate($pesanan);

        if (! $deadline) {
            return null;
        }

        $deadlineLabel = $deadline->translatedFormat('d F Y');
        $daysLeft = self::daysUntilDeadline($pesanan);

        if (($pesanan->status_deadline ?? 'safe') === 'overdue') {
            return [
                'type' => 'overdue',
                'icon' => '🚨',
                'message' => 'PERINGATAN: Pembayaran Anda telah melewati tenggat waktu pada '.$deadlineLabel
                    .'. Akses koordinasi penuh tim lapangan ditangguhkan sementara sampai pelunasan diselesaikan.',
            ];
        }

        if (($pesanan->status_deadline ?? 'safe') === 'warning' && $daysLeft !== null) {
            return [
                'type' => 'deadline_warning',
                'icon' => '⚠️',
                'message' => 'Pengingat: Batas akhir pelunasan tagihan Anda adalah '.$deadlineLabel
                    .' ('.$daysLeft.' hari lagi). Mohon segera lunasi agar koordinasi tim lapangan berjalan lancar.',
            ];
        }

        return null;
    }
}
