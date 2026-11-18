<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Occupied => 'Terisi',
            self::Maintenance => 'Perbaikan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Occupied => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            self::Maintenance => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-500',
            self::Occupied => 'bg-blue-500',
            self::Maintenance => 'bg-amber-500',
        };
    }
}

