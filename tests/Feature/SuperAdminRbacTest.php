<?php

use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('guest cannot access admin routes and is redirected to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));

    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

test('owner is forbidden with 403 when accessing admin routes', function () {
    $owner = User::where('role', UserRole::Owner)->first();

    $this->actingAs($owner)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($owner)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('super admin can access admin dashboard and see saas metrics', function () {
    $superAdmin = User::where('role', UserRole::SuperAdmin)->first();

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Ringkasan Platform SaaS')
        ->assertSee('Total Pemilik Kost')
        ->assertSee('Distribusi Paket')
        ->assertSee('Total Properti Kost')
        ->assertSee('Total Kamar Sistem');
});

test('super admin can access admin users list', function () {
    $superAdmin = User::where('role', UserRole::SuperAdmin)->first();
    $owner = User::where('email', 'owner@kostly.id')->first();

    $this->actingAs($superAdmin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Daftar Akun Pemilik Kost')
        ->assertSee($owner->name)
        ->assertSee($owner->email);
});

test('super admin can update owner subscription plan', function () {
    $superAdmin = User::where('role', UserRole::SuperAdmin)->first();
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    // Upgrade to Pro
    $response = $this->actingAs($superAdmin)
        ->patch(route('admin.users.updatePlan', $owner), [
            'subscription_plan' => 'pro',
        ]);

    $response->assertSessionHas('success');
    expect($owner->fresh()->subscription_plan)->toBe(SubscriptionPlan::Pro);

    // Toggle back to Free
    $response = $this->actingAs($superAdmin)
        ->patch(route('admin.users.updatePlan', $owner));

    $response->assertSessionHas('success');
    expect($owner->fresh()->subscription_plan)->toBe(SubscriptionPlan::Free);
});

test('super admin can toggle owner active and suspended status', function () {
    $superAdmin = User::where('role', UserRole::SuperAdmin)->first();
    $owner = User::where('email', 'owner@kostly.id')->first();

    expect($owner->is_active)->toBeTrue();

    // Suspend owner
    $this->actingAs($superAdmin)
        ->patch(route('admin.users.toggleStatus', $owner))
        ->assertSessionHas('success');

    expect($owner->fresh()->is_active)->toBeFalse();

    // Activate owner back
    $this->actingAs($superAdmin)
        ->patch(route('admin.users.toggleStatus', $owner))
        ->assertSessionHas('success');

    expect($owner->fresh()->is_active)->toBeTrue();
});

test('suspended owner cannot log in', function () {
    $owner = User::where('email', 'owner@kostly.id')->first();
    $owner->update(['is_active' => false]);

    $response = $this->post(route('login'), [
        'email' => 'owner@kostly.id',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('super admin logging in is redirected to admin dashboard', function () {
    $response = $this->post(route('login'), [
        'email' => 'admin@kostly.id',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('super admin logging in ignores owner dashboard intended url and goes to admin dashboard', function () {
    $this->withSession(['url.intended' => route('owner.dashboard')]);

    $response = $this->post(route('login'), [
        'email' => 'admin@kostly.id',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('super admin visiting dashboard route directly is redirected to admin dashboard', function () {
    $superAdmin = User::where('role', UserRole::SuperAdmin)->first();

    $this->actingAs($superAdmin)
        ->get(route('owner.dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('database seeder can be executed repeatedly without duplicate entry error', function () {
    // Jalankan ulang seeder untuk memverifikasi idempotence
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'admin@kostly.id')->count())->toBe(1);
    expect(User::where('email', 'owner@kostly.id')->count())->toBe(1);
});

test('owner logging in is redirected to owner dashboard', function () {
    $response = $this->post(route('login'), [
        'email' => 'owner@kostly.id',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('owner.dashboard'));
    $this->assertAuthenticated();
});
