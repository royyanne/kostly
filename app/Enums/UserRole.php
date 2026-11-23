<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Owner = 'owner';
    case Tenant = 'tenant';

    public const SUPER_ADMIN = self::SuperAdmin;

    public const ADMIN = self::Admin;

    public const OWNER = self::Owner;

    public const TENANT = self::Tenant;

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Owner => 'Pemilik Kost',
            self::Tenant => 'Penyewa Kost',
        };
    }
}
