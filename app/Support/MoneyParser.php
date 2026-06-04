<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Normalisasi nominal Rupiah dari input form / database (hindari concat string & format ID).
 */
class MoneyParser
{
    /**
     * Parse input uang ke float (Rupiah, tanpa desimal kecuali koma desimal).
     * Contoh: "10.000.000" → 10000000, "10000000" → 10000000, "10,5 juta" → 10.5
     */
    public static function parse(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_numeric($value) && ! is_string($value)) {
            return (float) $value;
        }

        $s = trim((string) $value);
        $s = preg_replace('/\s+/u', '', $s);
        $s = preg_replace('/Rp\.?/iu', '', $s);

        if ($s === '' || $s === '-') {
            return 0.0;
        }

        // Hanya angka, titik, koma, minus
        $s = preg_replace('/[^\d,.-]/', '', $s) ?? '';

        if ($s === '' || $s === '-') {
            return 0.0;
        }

        $hasComma = str_contains($s, ',');
        $hasDot = str_contains($s, '.');

        if ($hasComma && $hasDot) {
            // Format ID: 10.000.000,50 → desimal koma di akhir
            if (preg_match('/,\d{1,2}$/', $s)) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
                $s = str_replace('.', '', $s);
            }
        } elseif ($hasComma) {
            if (preg_match('/,\d{1,2}$/', $s)) {
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
            }
        } elseif ($hasDot) {
            // Satu titik + 3 digit grup → pemisah ribuan (10.000.000)
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $s)) {
                $s = str_replace('.', '', $s);
            }
            // else: desimal internasional 10.5
        }

        if (! is_numeric($s)) {
            return 0.0;
        }

        return (float) $s;
    }

    public static function toFloat(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_string($value) && ! is_numeric($value)) {
            return self::parse($value);
        }

        return (float) $value;
    }

    /**
     * Format tampilan Indonesia: 1000000 → "1.000.000" (tanpa prefix Rp).
     */
    public static function formatId(float|int|string|null $value, int $decimals = 0): string
    {
        return number_format(self::toFloat($value ?? 0), $decimals, ',', '.');
    }

    /**
     * Validasi: hanya digit (setelah normalisasi).
     */
    public static function isValidDigitsOnly(mixed $value): bool
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits !== '' && ctype_digit($digits);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function debugLog(string $label, array $context): void
    {
        if (! config('app.debug')) {
            return;
        }

        Log::debug('[MoneyParser] '.$label, $context);
    }
}
