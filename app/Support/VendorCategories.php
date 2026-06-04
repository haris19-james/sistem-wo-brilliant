<?php

namespace App\Support;

use App\Models\Vendor;
use Illuminate\Support\Str;

class VendorCategories
{
    public static function labels(): array
    {
        return config('brilliant.vendor_categories', []);
    }

    public static function resolve(?string $slug): ?string
    {
        if (! $slug || $slug === 'semua') {
            return null;
        }

        foreach (self::labels() as $label) {
            if (Str::slug($label) === $slug) {
                return $label;
            }
        }

        return null;
    }

    public static function slug(string $label): string
    {
        return Str::slug($label);
    }

    /**
     * Kategori untuk filter tab: gabungan config + yang ada di DB.
     *
     * @return array<int, array{label: string, slug: string, count: int}>
     */
    public static function forFilter(): array
    {
        $counts = Vendor::where('status', 'Aktif')
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $items = [];
        $seen = [];

        foreach (self::labels() as $label) {
            $items[] = [
                'label' => $label,
                'slug' => self::slug($label),
                'count' => (int) ($counts[$label] ?? 0),
            ];
            $seen[$label] = true;
        }

        foreach ($counts as $label => $total) {
            if (! isset($seen[$label])) {
                $items[] = [
                    'label' => $label,
                    'slug' => self::slug($label),
                    'count' => (int) $total,
                ];
            }
        }

        return $items;
    }
}
