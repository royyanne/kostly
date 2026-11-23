<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'phone',
    'role',
    'is_active',
    'subscription_plan',
    'plan_expires_at',
    'bank_name',
    'bank_account_number',
    'bank_account_name',
    'qris_image_path',
    'password',
])]
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
            'plan_expires_at' => 'datetime',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cek apakah user adalah super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin || $this->role === UserRole::Admin;
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin || $this->role === UserRole::SuperAdmin;
    }

    /**
     * Cek apakah user adalah pemilik kost (owner).
     */
    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner;
    }

    /**
     * Cek apakah user adalah penyewa (tenant).
     */
    public function isTenant(): bool
    {
        return $this->role === UserRole::Tenant;
    }

    /**
     * Cek apakah user berlangganan paket Pro.
     */
    public function isPro(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::Pro;
    }

    /**
     * Cek apakah user berada pada paket Free.
     */
    public function isFree(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::Free;
    }

    /**
     * Batas maksimal properti untuk paket user (null = unlimited).
     */
    public function maxProperties(): ?int
    {
        return $this->isPro() ? null : 1;
    }

    /**
     * Batas maksimal kamar untuk paket user (null = unlimited).
     */
    public function maxRooms(): ?int
    {
        return $this->isPro() ? null : 10;
    }

    /**
     * Cek apakah user diizinkan membuat properti baru sesuai kuota paket.
     */
    public function canCreateProperty(): bool
    {
        if ($this->isPro()) {
            return true;
        }

        return $this->properties()->count() < 1;
    }

    /**
     * Cek apakah user diizinkan membuat kamar baru sesuai kuota paket.
     */
    public function canCreateRoom(): bool
    {
        if ($this->isPro()) {
            return true;
        }

        return $this->rooms()->count() < 10;
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

    /**
     * Profil penghuni (tenant) untuk user ini jika berperan sebagai penyewa.
     *
     * @return HasOne<Tenant, $this>
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * Daftar pengajuan sewa (bookings) yang diajukan user ini.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Riwayat transaksi langganan user.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Langganan aktif terbaru user.
     *
     * @return HasOne<Subscription, $this>
     */
    public function latestActiveSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }
}
