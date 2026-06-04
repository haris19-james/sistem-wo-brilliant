<?php

namespace App\Support;

class GoogleMapsUrl
{
    /**
     * Normalisasi input paste customer (opsional, boleh kosong).
     */
    public static function normalize(?string $value): ?string
    {
        $url = trim((string) $value);

        if ($url === '') {
            return null;
        }

        if (! preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }

    public static function isLikelyMapsLink(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        $url = strtolower(self::normalize($value) ?? '');

        return (bool) preg_match(
            '/google\.com\/maps|maps\.google\.com|maps\.app\.goo\.gl|goo\.gl\/maps|openstreetmap/i',
            $url
        );
    }

    public static function fromCoordinates(float $lat, float $lng): string
    {
        return 'https://www.google.com/maps?q='.round($lat, 7).','.round($lng, 7);
    }
}
