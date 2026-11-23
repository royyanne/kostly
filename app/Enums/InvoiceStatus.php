<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Unpaid = 'unpaid';
    case PendingVerification = 'pending_verification';
    case Paid = 'paid';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Lunas',
            self::PendingVerification => 'Perlu Verifikasi',
            self::Paid => 'Lunas',
            self::Overdue => 'Jatuh Tempo',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::PendingVerification => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Paid => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Overdue => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        };
    }
}
