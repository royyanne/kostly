<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::InProgress => 'Diproses',
            self::Resolved => 'Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::InProgress => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            self::Resolved => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        };
    }
}
