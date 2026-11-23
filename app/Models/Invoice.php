<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'tenant_id',
    'invoice_code',
    'amount',
    'additional_fee',
    'total_amount',
    'due_date',
    'status',
    'payment_method',
    'payment_url',
    'payment_proof',
    'paid_at',
    'rejected_reason',
])]
class Invoice extends Model
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
            'additional_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'status' => InvoiceStatus::class,
        ];
    }

    /**
     * Properti terkait tagihan ini.
     *
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Penghuni yang ditagih.
     *
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope tagihan belum lunas.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Unpaid);
    }

    /**
     * Scope tagihan menunggu verifikasi pembayaran.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopePendingVerification(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::PendingVerification);
    }

    /**
     * Scope tagihan lunas.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    /**
     * Scope tagihan jatuh tempo.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Overdue);
    }

    /**
     * Scope query untuk tagihan dari properti milik user tertentu.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->whereHas('property', fn (Builder $q) => $q->where('user_id', $userId));
    }
}
