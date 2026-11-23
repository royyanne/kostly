<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'tenant_id',
    'room_id',
    'title',
    'description',
    'photo_path',
    'status',
    'owner_response',
])]
class Complaint extends Model
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
            'status' => ComplaintStatus::class,
        ];
    }

    /**
     * Properti terkait komplain ini.
     *
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Penghuni yang melaporkan komplain.
     *
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Kamar yang dilaporkan mengalami kendala.
     *
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope komplain berstatus pending.
     *
     * @param  Builder<Complaint>  $query
     * @return Builder<Complaint>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ComplaintStatus::Pending);
    }

    /**
     * Scope komplain berstatus diproses.
     *
     * @param  Builder<Complaint>  $query
     * @return Builder<Complaint>
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', ComplaintStatus::InProgress);
    }

    /**
     * Scope komplain selesai diperbaiki.
     *
     * @param  Builder<Complaint>  $query
     * @return Builder<Complaint>
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', ComplaintStatus::Resolved);
    }

    /**
     * Scope query untuk komplain dari properti milik user tertentu.
     *
     * @param  Builder<Complaint>  $query
     * @return Builder<Complaint>
     */
    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->whereHas('property', fn (Builder $q) => $q->where('user_id', $userId));
    }
}
