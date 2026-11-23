<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'plan',
    'billing_cycle',
    'amount',
    'status',
    'payment_proof',
    'rejected_reason',
    'starts_at',
    'ends_at',
])]
class Subscription extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * User pemilik langganan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cek apakah langganan aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && (! $this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Label status langganan.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'active' => 'Aktif',
            'rejected' => 'Ditolak',
            'expired' => 'Kedaluwarsa',
            default => ucfirst($this->status),
        };
    }

    /**
     * Badge class CSS Tailwind untuk status.
     */
    public function badgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 border border-amber-200',
            'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border border-rose-200',
            'expired' => 'bg-slate-100 text-slate-700 border border-slate-300',
            default => 'bg-slate-50 text-slate-700',
        };
    }
}
