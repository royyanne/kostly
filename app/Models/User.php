<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'subscription_plan', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_plan' => SubscriptionPlan::class,
        ];
    }

    /**
     * Daftar properti kost milik user.
     *
     * @return HasMany<Property, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Daftar kamar dari semua properti milik user.
     *
     * @return HasManyThrough<Room, Property, $this>
     */
    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(Room::class, Property::class);
    }

    /**
     * Daftar penghuni dari semua properti milik user.
     *
     * @return HasManyThrough<Tenant, Property, $this>
     */
    public function tenants(): HasManyThrough
    {
        return $this->hasManyThrough(Tenant::class, Property::class);
    }

    /**
     * Daftar tagihan dari semua properti milik user.
     *
     * @return HasManyThrough<Invoice, Property, $this>
     */
    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Property::class);
    }

    /**
     * Daftar pengeluaran dari semua properti milik user.
     *
     * @return HasManyThrough<Expense, Property, $this>
     */
    public function expenses(): HasManyThrough
    {
        return $this->hasManyThrough(Expense::class, Property::class);
    }

    /**
     * Daftar komplain dari semua properti milik user.
     *
     * @return HasManyThrough<Complaint, Property, $this>
     */
    public function complaints(): HasManyThrough
    {
        return $this->hasManyThrough(Complaint::class, Property::class);
    }
}
