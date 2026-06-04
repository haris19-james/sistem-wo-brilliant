<?php

namespace App\Support;

final class Role
{
    public const CLIENT = 'client';

    public const ADMIN = 'admin';

    public const LAPANGAN = 'lapangan';

    /** @deprecated Gunakan Role::CLIENT */
    public const CUSTOMER = 'client';

    public static function isClient(?string $role): bool
    {
        return in_array($role, [self::CLIENT, 'customer'], true);
    }
}
