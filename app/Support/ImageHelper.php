<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public const PLACEHOLDER_PACKAGE = 'images/placeholders/package.svg';

    public const PLACEHOLDER_VENDOR = 'images/placeholders/vendor.svg';

    public static function storeUploaded(?UploadedFile $file, string $folder, ?string $oldPath = null): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        if ($oldPath && ! self::isAbsoluteUrl($oldPath)) {
            Storage::disk('public')->delete(self::normalizeStoragePath($oldPath));
        }

        Storage::disk('public')->makeDirectory($folder);

        $optimized = self::optimizeAndStore($file, $folder);

        return $optimized ?? $file->store($folder, 'public');
    }

    /**
     * Kompresi & konversi WebP (max 1200px) bila GD tersedia.
     */
    public static function optimizeAndStore(UploadedFile $file, string $folder): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = $file->getRealPath();
        if (! $path || ! is_readable($path)) {
            return null;
        }

        $mime = $file->getMimeType() ?: mime_content_type($path);
        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            'image/gif' => @imagecreatefromgif($path),
            default => false,
        };

        if (! $source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $max = 1200;

        if ($width > $max || $height > $max) {
            $ratio = min($max / $width, $max / $height);
            $newW = (int) round($width * $ratio);
            $newH = (int) round($height * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        $filename = uniqid('img_', true).'.webp';
        $relative = trim($folder, '/').'/'.$filename;
        $fullPath = Storage::disk('public')->path($relative);

        $saved = function_exists('imagewebp')
            ? @imagewebp($source, $fullPath, 82)
            : @imagejpeg($source, str_replace('.webp', '.jpg', $fullPath), 85);

        imagedestroy($source);

        if (! $saved) {
            return null;
        }

        if (! str_ends_with($relative, '.webp') && str_ends_with($fullPath, '.jpg')) {
            $relative = str_replace('.webp', '.jpg', $relative);
        }

        return $relative;
    }

    /**
     * Optimasi file gambar di public/ → WebP (untuk aset statis seperti hero gallery).
     */
    public static function convertPublicToWebp(string $sourceRelative, string $destRelative, int $max = 840, int $quality = 82): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $source = public_path($sourceRelative);
        $dest = public_path($destRelative);

        if (! is_file($source) || ! is_readable($source)) {
            return false;
        }

        $mime = mime_content_type($source) ?: '';
        $sourceImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            'image/gif' => @imagecreatefromgif($source),
            default => false,
        };

        if (! $sourceImage) {
            return false;
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width > $max || $height > $max) {
            $ratio = min($max / $width, $max / $height);
            $newW = (int) round($width * $ratio);
            $newH = (int) round($height * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($sourceImage);
            $sourceImage = $resized;
        }

        $destDir = dirname($dest);
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $saved = function_exists('imagewebp')
            ? @imagewebp($sourceImage, $dest, $quality)
            : @imagejpeg($sourceImage, preg_replace('/\.webp$/', '.jpg', $dest), 85);

        imagedestroy($sourceImage);

        return (bool) $saved;
    }

    public static function publicAssetUrl(string $relativePath): string
    {
        return asset(self::encodePublicPath($relativePath));
    }

    public static function storeBuktiTransfer(UploadedFile $file, string $folder = 'pembayaran'): string
    {
        if (! $file->isValid()) {
            throw new \RuntimeException(self::uploadErrorMessage($file));
        }

        Storage::disk('public')->makeDirectory($folder);

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'heic', 'heif'];
        if (! in_array($extension, $allowed, true)) {
            $extension = 'jpg';
        }

        $filename = uniqid('bukti_', true).'.'.$extension;

        $path = $file->storeAs($folder, $filename, 'public');

        if (! $path) {
            throw new \RuntimeException('Gagal menyimpan file ke storage. Pastikan folder storage/app/public dapat ditulis.');
        }

        return $path;
    }

    public static function uploadErrorMessage(UploadedFile $file): string
    {
        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas server (maks. '.ini_get('upload_max_filesize').'). Kompres foto atau pilih gambar lebih kecil.',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian. Coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
            default => 'Upload gagal (kode '.$file->getError().'). Coba lagi atau gunakan format JPG/PNG.',
        };
    }

    /**
     * Resolve URL untuk tampilan: file lokal di storage, URL eksternal, atau null.
     */
    public static function url(?string $path, ?string $fallbackUrl = null): ?string
    {
        if ($path) {
            $normalized = self::normalizeStoragePath($path);

            if (self::isAbsoluteUrl($normalized)) {
                return $normalized;
            }

            if (self::exists($normalized)) {
                return self::storageUrl($normalized);
            }
        }

        if ($fallbackUrl) {
            return self::resolveExternalUrl($fallbackUrl);
        }

        return null;
    }

    /**
     * Normalisasi path/URL dari database atau API (untuk Blade & JS).
     */
    public static function resolvePath(?string $value, ?string $fallbackUrl = null, string $placeholderType = 'package'): ?string
    {
        $resolved = self::url($value, $fallbackUrl);

        return $resolved ?? self::placeholderUrl($placeholderType);
    }

    public static function placeholderUrl(string $type = 'package'): string
    {
        $path = match ($type) {
            'vendor' => self::PLACEHOLDER_VENDOR,
            default => self::PLACEHOLDER_PACKAGE,
        };

        return asset($path);
    }

    public static function storageUrl(string $relativePath): string
    {
        $relativePath = self::normalizeStoragePath($relativePath);

        return asset('storage/'.self::encodePathSegments($relativePath));
    }

    /** @deprecated Use storageUrl() — kept for backward compatibility */
    public static function publicPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return self::storageUrl($path);
    }

    public static function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));

        if (self::isAbsoluteUrl($path)) {
            return $path;
        }

        return ltrim(preg_replace('#^(?:public/)?(?:storage/)?#', '', $path), '/');
    }

    public static function resolveExternalUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (! self::isAbsoluteUrl($url)) {
            return 'https://'.ltrim($url, '/');
        }

        return $url;
    }

    public static function isAbsoluteUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }

    public static function exists(?string $path): bool
    {
        if (! $path || self::isAbsoluteUrl($path)) {
            return false;
        }

        return Storage::disk('public')->exists(self::normalizeStoragePath($path));
    }

    public static function delete(?string $path): void
    {
        if ($path && ! self::isAbsoluteUrl($path)) {
            Storage::disk('public')->delete(self::normalizeStoragePath($path));
        }
    }

    protected static function encodePublicPath(string $relativePath): string
    {
        $relativePath = str_replace('\\', '/', ltrim($relativePath, '/'));

        return self::encodePathSegments($relativePath);
    }

    protected static function encodePathSegments(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }
}
