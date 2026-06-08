<?php

namespace App\Support;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class BookingDynamicStatus
{
    public const KEY_SELESAI = 'selesai';

    public const KEY_SEDANG = 'sedang_berlangsung';

    public const KEY_MENDESAK = 'mendesak';

    public const KEY_EXPIRED = 'expired';

    public const KEY_MENUNGGU = 'menunggu';

    public const KEY_DIBATALKAN = 'dibatalkan';

    /** Nilai kolom DB `pesanans.status` (ENUM) */
    public const DB_MENUNGGU = 'Menunggu';

    public const DB_SEDANG = 'Sedang Berlangsung';

    public const DB_MENDESAK = 'Mendesak';

    public const DB_EXPIRED = 'Expired';

    public const DB_SELESAI = 'Selesai';

    public const DB_DIBATALKAN = 'Dibatalkan';

    /**
     * @return array{key: string, label: string, badge_class: string, is_completed: bool}
     */
    public static function resolve(Pesanan $pesanan, ?Carbon $today = null): array
    {
        if (self::isCancelled($pesanan)) {
            return self::pack(self::KEY_DIBATALKAN, 'Dibatalkan', 'bg-red-50 text-red-600 border border-red-200', false);
        }

        if (self::isWaitingPayment($pesanan)) {
            return self::pack(self::KEY_MENUNGGU, 'Menunggu', 'bg-yellow-50 text-yellow-700 border border-yellow-200', false);
        }

        $progress = self::progressPercent($pesanan);
        $eventDate = self::eventDate($pesanan);
        $today = ($today ?? now())->copy()->startOfDay();

        if ($progress >= 100) {
            return self::pack(self::KEY_SELESAI, 'Selesai', 'bg-blue-50 text-blue-700 border border-blue-200', true);
        }

        if ($eventDate && $today->gt($eventDate)) {
            return self::pack(self::KEY_EXPIRED, 'Expired/Incomplete', 'bg-gray-900 text-white border border-gray-800', false);
        }

        if ($eventDate && $today->equalTo($eventDate)) {
            return self::pack(self::KEY_MENDESAK, 'Mendesak (Hari H)', 'bg-red-50 text-red-700 border border-red-300', false);
        }

        return self::pack(self::KEY_SEDANG, 'Sedang Berlangsung', 'bg-green-50 text-green-700 border border-green-200', false);
    }

    public static function progressPercent(Pesanan $pesanan): int
    {
        if ($pesanan->relationLoaded('progress') && $pesanan->progress) {
            return (int) $pesanan->progress->persentase;
        }

        if ($pesanan->progress) {
            return (int) $pesanan->progress->persentase;
        }

        return 0;
    }

    public static function dbStatusFromResolved(array $resolved): string
    {
        return match ($resolved['key']) {
            self::KEY_SELESAI => self::DB_SELESAI,
            self::KEY_EXPIRED => self::DB_EXPIRED,
            self::KEY_MENDESAK => self::DB_MENDESAK,
            self::KEY_MENUNGGU => self::DB_MENUNGGU,
            self::KEY_DIBATALKAN => self::DB_DIBATALKAN,
            default => self::DB_SEDANG,
        };
    }

    public static function labelForDbStatus(?string $status): string
    {
        return match ($status) {
            self::DB_SELESAI => 'Selesai',
            self::DB_EXPIRED => 'Expired/Incomplete',
            self::DB_MENDESAK => 'Mendesak (Hari H)',
            self::DB_MENUNGGU => 'Menunggu',
            self::DB_DIBATALKAN => 'Dibatalkan',
            self::DB_SEDANG => 'Sedang Berlangsung',
            default => $status ?? '—',
        };
    }

    public static function sync(Pesanan $pesanan): bool
    {
        if (self::isCancelled($pesanan) || self::isWaitingPayment($pesanan)) {
            return false;
        }

        $resolved = self::resolve($pesanan);
        $newStatus = self::dbStatusFromResolved($resolved);

        $updates = [];

        if ($pesanan->status !== $newStatus) {
            $updates['status'] = $newStatus;
        }

        if ($resolved['key'] === self::KEY_SELESAI && ! in_array($pesanan->status_pemesanan, ['completed', 'success'], true)) {
            $updates['status_pemesanan'] = 'completed';
        }

        if ($resolved['key'] === self::KEY_EXPIRED && ! in_array($pesanan->status_pemesanan, ['expired', 'cancelled', 'canceled'], true)) {
            $updates['status_pemesanan'] = 'on_progress';
        }

        if ($updates === []) {
            return false;
        }

        $pesanan->update($updates);

        return true;
    }

    public static function syncDueBookingsIfNeeded(): void
    {
        Cache::remember('pesanan.sync_dynamic_status_ran', now()->addMinutes(5), function () {
            Pesanan::query()
                ->whereNotIn('status', [self::DB_DIBATALKAN, self::DB_MENUNGGU])
                ->whereNotIn('status_pemesanan', ['cancelled', 'canceled', 'pending_cancellation', 'expired'])
                ->with('progress')
                ->chunkById(100, function ($pesanans) {
                    foreach ($pesanans as $pesanan) {
                        self::sync($pesanan);
                    }
                });

            return true;
        });
    }

    public static function syncOne(Pesanan $pesanan): Pesanan
    {
        if (! $pesanan->relationLoaded('progress')) {
            $pesanan->load('progress');
        }

        self::sync($pesanan);

        return $pesanan->fresh(['progress']);
    }

    protected static function eventDate(Pesanan $pesanan): ?Carbon
    {
        if (! $pesanan->tanggal_acara) {
            return null;
        }

        return Carbon::parse($pesanan->tanggal_acara)->startOfDay();
    }

    protected static function isCancelled(Pesanan $pesanan): bool
    {
        return $pesanan->status === self::DB_DIBATALKAN
            || in_array($pesanan->status_pemesanan, ['cancelled', 'canceled', 'pending_cancellation', 'expired'], true)
            || ($pesanan->status_booking ?? null) === 'cancelled';
    }

    protected static function isWaitingPayment(Pesanan $pesanan): bool
    {
        if (in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            return false;
        }

        if ($pesanan->hasMinimalDpPaid() || $pesanan->isPembayaranLunas()) {
            return false;
        }

        return $pesanan->status === self::DB_MENUNGGU
            || $pesanan->status_pembayaran === 'unpaid'
            || $pesanan->status_pemesanan === 'pending';
    }

    /**
     * @return array{key: string, label: string, badge_class: string, is_completed: bool}
     */
    protected static function pack(string $key, string $label, string $badgeClass, bool $isCompleted): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'badge_class' => $badgeClass,
            'is_completed' => $isCompleted,
        ];
    }
}
