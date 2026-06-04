<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AdminPerformanceCache
{
    public const BOOKING_STATS = 'admin.booking.stats';

    public const PAKETS_LIST = 'admin.pakets.list';

    public static function forgetBookingStats(): void
    {
        Cache::forget(self::BOOKING_STATS);
    }

    public static function forgetPakets(): void
    {
        Cache::forget(self::PAKETS_LIST);
    }

    public static function korlapMetricsKey(int $korlapId, ?int $pesananId = null): string
    {
        return 'korlap.metrics.'.$korlapId.'.'.($pesananId ?? 'all');
    }

    public static function forgetKorlapMetrics(int $korlapId, ?int $pesananId = null): void
    {
        Cache::forget(self::korlapMetricsKey($korlapId, null));
        if ($pesananId !== null) {
            Cache::forget(self::korlapMetricsKey($korlapId, $pesananId));
        }
    }
}
