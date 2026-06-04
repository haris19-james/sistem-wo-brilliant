<?php

namespace App\Support;

class RupiahInput
{
    public const DP_MINIMAL_DEFAULT = 1_000_000;

    public const DP_MINIMAL_MIN = 1_000_000;

    /**
     * Ubah input (angka, string berformat, atau null) menjadi integer Rupiah bersih.
     */
    public static function parse(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value)) {
            return max(0, $value);
        }

        if (is_float($value)) {
            return max(0, (int) round($value));
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        if ($digits === '' || $digits === null) {
            return 0;
        }

        return (int) $digits;
    }

    public static function format(int|float|string|null $amount): string
    {
        $n = max(0, (int) self::parse($amount));

        return number_format($n, 0, ',', '.');
    }
}
