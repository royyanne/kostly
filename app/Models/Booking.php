<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'property_id',
    'room_id',
    'name',
    'email',
    'phone',
    'start_date',
    'duration_type',
    'duration_months',
    'total_price',
    'notes',
    'status',
    'rejection_reason',
])]
class Booking extends Model
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
            'start_date' => 'date',
            'duration_months' => 'integer',
            'total_price' => 'decimal:2',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * User/Calon penyewa yang mengajukan sewa (jika login).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Properti kost yang diajukan.
     *
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Kamar yang dipilih untuk disewa.
     *
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope pengajuan yang berstatus pending.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Pending);
    }

    /**
     * Scope pengajuan yang disetujui.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Approved);
    }

    /**
     * Scope pengajuan yang ditolak.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Rejected);
    }

    /**
     * Scope query untuk pengajuan pada properti milik user (owner) tertentu.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeForOwner(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->whereHas('property', fn (Builder $q) => $q->where('user_id', $userId));
    }
}
