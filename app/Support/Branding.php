<?php

namespace App\Support;

class Branding
{
    public static function logoUrl(): ?string
    {
        $configured = config('brilliant.logo');
        if ($configured && str_starts_with($configured, 'http')) {
            return $configured;
        }

        $candidates = array_filter([
            $configured,
            'images/branding/logo.png',
            'images/branding/logo.jpg',
            'images/branding/logo.webp',
            'images/branding/logo.svg',
        ]);

        foreach (array_unique($candidates) as $path) {
            $full = public_path($path);
            if (is_file($full)) {
                return asset($path);
            }
        }

        return null;
    }

    public static function hasLogo(): bool
    {
        return self::logoUrl() !== null;
    }

    public static function whatsappUrl(?string $message = null): string
    {
        $digits = preg_replace('/\D/', '', config('brilliant.contact.phone_digits', ''));
        $url = 'https://wa.me/'.$digits;
        if ($message) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    /**
     * Slides untuk carousel hero beranda (prioritas file WebP lokal).
     *
     * @return list<array{src: string, alt: string, caption: string}>
     */
    public static function heroGallerySlides(): array
    {
        $configured = config('brilliant.hero_gallery', []);
        $slides = [];

        foreach ($configured as $index => $item) {
            $src = self::resolveHeroSlideSrc($item, $index === 0);
            if (! $src) {
                continue;
            }

            $slides[] = [
                'src' => $src,
                'alt' => $item['alt'] ?? 'Portofolio Brilliant WO',
                'caption' => $item['caption'] ?? '',
            ];
        }

        if ($slides !== []) {
            return $slides;
        }

        return [[
            'src' => self::resolveHeroSlideSrc(['webp' => null], true),
            'alt' => 'Dekorasi pernikahan Brilliant WO',
            'caption' => 'Galeri Portofolio',
        ]];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected static function resolveHeroSlideSrc(array $item, bool $isPrimary): ?string
    {
        foreach (['webp', 'image'] as $key) {
            $path = $item[$key] ?? null;
            if (is_string($path) && $path !== '' && is_file(public_path($path))) {
                return ImageHelper::publicAssetUrl($path);
            }
        }

        $remote = $item['remote'] ?? null;
        if (is_string($remote) && $remote !== '') {
            return $remote;
        }

        if ($isPrimary) {
            $hero = config('brilliant.hero_image');
            if (is_string($hero) && $hero !== '') {
                if (str_starts_with($hero, 'http')) {
                    return $hero;
                }
                if (is_file(public_path($hero))) {
                    return ImageHelper::publicAssetUrl($hero);
                }
            }
        }

        return null;
    }
}
