<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['property_id', 'room_number', 'type', 'price', 'status'])]
class Room extends Model
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
            'price' => 'decimal:2',
            'status' => RoomStatus::class,
        ];
    }

    /**
     * Properti tempat kamar ini berada.
     *
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Riwayat seluruh penghuni kamar ini.
     *
     * @return HasMany<Tenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Penghuni aktif kamar ini saat ini.
     *
     * @return HasOne<Tenant, $this>
     */
    public function currentTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->where('is_active', true);
    }

    /**
     * Daftar komplain terkait kamar ini.
     *
     * @return HasMany<Complaint, $this>
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Scope kamar yang tersedia.
     *
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', RoomStatus::Available);
    }

    /**
     * Scope kamar yang terisi.
     *
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeOccupied(Builder $query): Builder
    {
        return $query->where('status', RoomStatus::Occupied);
    }

    /**
     * Scope query untuk membatasi kamar hanya milik user tertentu.
     *
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->whereHas('property', fn (Builder $q) => $q->where('user_id', $userId));
    }
}
