<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class SettingsMenu
{
    /**
     * Kunci role untuk menu (lapangan → korlap).
     */
    public static function roleKey(?User $user): string
    {
        if (! $user) {
            return 'guest';
        }

        return match ($user->role) {
            'admin' => 'admin',
            'client', 'customer' => 'client',
            'lapangan' => 'korlap',
            default => $user->role,
        };
    }

    public static function subtitle(string $roleKey): string
    {
        return match ($roleKey) {
            'client' => 'Kelola akun dan pesanan Anda.',
            'korlap' => 'Kelola profil koordinator, notifikasi, dan keamanan akun.',
            'admin' => 'Kelola akun, sistem operasional, dan konfigurasi vendor.',
            default => 'Kelola akun, preferensi, dan pengaturan aplikasi.',
        };
    }

    /**
     * @return array<int, array{key: string, label: string, icon: string, abbr: string, roles: array<int, string>}>
     */
    public static function definitions(): array
    {
        return [
            ['key' => 'pengaturan_akun', 'label' => 'Profil Akun', 'icon' => 'user', 'abbr' => 'A', 'roles' => ['client', 'korlap', 'admin']],
            ['key' => 'profil_korlap', 'label' => 'Profil Korlap', 'icon' => 'id-card', 'abbr' => 'K', 'roles' => ['korlap']],
            ['key' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'bell', 'abbr' => 'N', 'roles' => ['client', 'korlap', 'admin']],
            ['key' => 'preferensi', 'label' => 'Preferensi Aplikasi', 'icon' => 'sliders', 'abbr' => 'PA', 'roles' => ['admin']],
            ['key' => 'keamanan', 'label' => 'Keamanan', 'icon' => 'lock', 'abbr' => 'S', 'roles' => ['client', 'korlap', 'admin']],
            ['key' => 'tim', 'label' => 'Manajemen Tim', 'icon' => 'users', 'abbr' => 'T', 'roles' => ['admin']],
            ['key' => 'tugas', 'label' => 'Manajemen Tugas', 'icon' => 'clipboard-list', 'abbr' => 'G', 'roles' => ['admin']],
            ['key' => 'acara', 'label' => 'Manajemen Acara', 'icon' => 'calendar', 'abbr' => 'E', 'roles' => ['admin']],
            ['key' => 'vendor', 'label' => 'Manajemen Vendor', 'icon' => 'handshake', 'abbr' => 'V', 'roles' => ['admin']],
            ['key' => 'template', 'label' => 'Template Dokumen', 'icon' => 'file-text', 'abbr' => 'D', 'roles' => ['admin']],
            ['key' => 'backup', 'label' => 'Backup & Data', 'icon' => 'save', 'abbr' => 'B', 'roles' => ['admin']],
            ['key' => 'integrasi', 'label' => 'Integrasi', 'icon' => 'link', 'abbr' => 'I', 'roles' => ['admin']],
            ['key' => 'bantuan', 'label' => 'Pusat Bantuan', 'icon' => 'help-circle', 'abbr' => 'P', 'roles' => ['admin']],
            ['key' => 'about', 'label' => 'Tentang Aplikasi', 'icon' => 'info', 'abbr' => 'TA', 'roles' => ['admin']],
        ];
    }

    /**
     * @return Collection<int, array{key: string, label: string, icon: string, abbr: string, url: string}>
     */
    public static function forUser(User $user, string $baseUrl): Collection
    {
        $roleKey = self::roleKey($user);

        return collect(self::definitions())
            ->filter(fn (array $item) => in_array($roleKey, $item['roles'], true))
            ->values()
            ->map(fn (array $item) => [
                'key' => $item['key'],
                'label' => $item['label'],
                'icon' => $item['icon'],
                'abbr' => $item['abbr'],
                'url' => $baseUrl.'?tab='.$item['key'],
            ]);
    }

    public static function canAccess(User $user, string $section): bool
    {
        $roleKey = self::roleKey($user);

        foreach (self::definitions() as $item) {
            if ($item['key'] === $section) {
                return in_array($roleKey, $item['roles'], true);
            }
        }

        return false;
    }

    public static function resolveSection(User $user, ?string $requested): string
    {
        $section = $requested ?: 'pengaturan_akun';

        if (! self::canAccess($user, $section)) {
            abort(403, 'Anda tidak memiliki akses ke menu pengaturan ini.');
        }

        return $section;
    }

    public static function showsAdminPanels(string $roleKey): bool
    {
        return $roleKey === 'admin';
    }
}
