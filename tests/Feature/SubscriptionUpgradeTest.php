<?php

use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use App\Models\Property;
use App\Models\Room;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('owner dapat mengakses halaman paket dan langganan', function () {
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $response = $this->actingAs($owner)->get(route('owner.subscription.index'));

    $response->assertOk();
    $response->assertSee('Manajemen Langganan Owner');
    $response->assertSee('Paket Free');
    $response->assertSee('Paket Juragan PRO');
});

test('owner dapat mengajukan upgrade ke paket pro bulanan dengan mengunggah bukti bayar', function () {
    Storage::fake('public');

    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $file = UploadedFile::fake()->image('bukti_upgrade.png', 500, 500);

    $response = $this->actingAs($owner)->post(route('owner.subscription.store'), [
        'billing_cycle' => 'monthly',
        'payment_proof' => $file,
    ]);

    $response->assertRedirect(route('owner.subscription.index'));
    $response->assertSessionHas('success');

    $subscription = Subscription::where('user_id', $owner->id)->first();
    expect($subscription)->not->toBeNull();
    expect($subscription->status)->toBe('pending');
    expect($subscription->plan)->toBe('pro');
    expect($subscription->billing_cycle)->toBe('monthly');
    expect((float) $subscription->amount)->toBe(99000.0);
    expect($subscription->payment_proof)->not->toBeNull();

    Storage::disk('public')->assertExists($subscription->payment_proof);
});

test('owner dapat mengajukan upgrade ke paket pro tahunan', function () {
    Storage::fake('public');

    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $file = UploadedFile::fake()->image('bukti_tahunan.jpg', 600, 600);

    $response = $this->actingAs($owner)->post(route('owner.subscription.store'), [
        'billing_cycle' => 'yearly',
        'payment_proof' => $file,
    ]);

    $response->assertRedirect(route('owner.subscription.index'));
    $response->assertSessionHas('success');

    $subscription = Subscription::where('user_id', $owner->id)->first();
    expect($subscription->billing_cycle)->toBe('yearly');
    expect((float) $subscription->amount)->toBe(999000.0);
});

test('owner tidak dapat mengajukan upgrade baru jika masih ada yang pending', function () {
    Storage::fake('public');

    $owner = User::factory()->create(['role' => UserRole::Owner]);

    Subscription::create([
        'user_id' => $owner->id,
        'plan' => 'pro',
        'billing_cycle' => 'monthly',
        'amount' => 99000,
        'status' => 'pending',
    ]);

    $file = UploadedFile::fake()->image('bukti_kedua.jpg');

    $response = $this->actingAs($owner)->post(route('owner.subscription.store'), [
        'billing_cycle' => 'monthly',
        'payment_proof' => $file,
    ]);

    $response->assertSessionHas('error');
    expect(Subscription::where('user_id', $owner->id)->count())->toBe(1);
});

test('admin dapat menyetujui upgrade subscription dan membuka kuota pro untuk owner', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $subscription = Subscription::create([
        'user_id' => $owner->id,
        'plan' => 'pro',
        'billing_cycle' => 'monthly',
        'amount' => 99000,
        'status' => 'pending',
        'payment_proof' => 'subscription-proofs/test.png',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.subscriptions.approve', $subscription));

    $response->assertRedirect(route('admin.subscriptions.index'));
    $response->assertSessionHas('success');

    $subscription->refresh();
    $owner->refresh();

    expect($subscription->status)->toBe('active');
    expect($subscription->starts_at)->not->toBeNull();
    expect($subscription->ends_at)->not->toBeNull();

    expect($owner->subscription_plan)->toBe(SubscriptionPlan::Pro);
    expect($owner->plan_expires_at)->not->toBeNull();
    expect($owner->isPro())->toBeTrue();

    // Kuota Pro terbuka: dapat membuat lebih dari 1 properti
    Property::factory()->create(['user_id' => $owner->id]);
    expect($owner->canCreateProperty())->toBeTrue();
});

test('admin dapat menolak pengajuan subscription dengan alasan penolakan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $subscription = Subscription::create([
        'user_id' => $owner->id,
        'plan' => 'pro',
        'billing_cycle' => 'monthly',
        'amount' => 99000,
        'status' => 'pending',
        'payment_proof' => 'subscription-proofs/test.png',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.subscriptions.reject', $subscription), [
        'rejected_reason' => 'Nominal transfer tidak sesuai dengan tagihan paket.',
    ]);

    $response->assertRedirect(route('admin.subscriptions.index'));
    $response->assertSessionHas('info');

    $subscription->refresh();
    $owner->refresh();

    expect($subscription->status)->toBe('rejected');
    expect($subscription->rejected_reason)->toBe('Nominal transfer tidak sesuai dengan tagihan paket.');
    expect($owner->subscription_plan)->toBe(SubscriptionPlan::Free);
});

test('command subscriptions:check-expired menurunkan paket pro yang kedaluwarsa menjadi free', function () {
    // Owner A: Pro dan sudah kedaluwarsa kemarin
    $ownerExpired = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Pro,
        'plan_expires_at' => Carbon::now()->subDay(),
    ]);

    $subExpired = Subscription::create([
        'user_id' => $ownerExpired->id,
        'plan' => 'pro',
        'billing_cycle' => 'monthly',
        'amount' => 99000,
        'status' => 'active',
        'starts_at' => Carbon::now()->subMonth()->subDay(),
        'ends_at' => Carbon::now()->subDay(),
    ]);

    // Owner B: Pro dan masih aktif 10 hari lagi
    $ownerActive = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Pro,
        'plan_expires_at' => Carbon::now()->addDays(10),
    ]);

    $subActive = Subscription::create([
        'user_id' => $ownerActive->id,
        'plan' => 'pro',
        'billing_cycle' => 'monthly',
        'amount' => 99000,
        'status' => 'active',
        'starts_at' => Carbon::now()->subDays(20),
        'ends_at' => Carbon::now()->addDays(10),
    ]);

    $this->artisan('subscriptions:check-expired')->assertSuccessful();

    $ownerExpired->refresh();
    $subExpired->refresh();
    $ownerActive->refresh();
    $subActive->refresh();

    // Owner A turun ke Free dan subscription expired
    expect($ownerExpired->subscription_plan)->toBe(SubscriptionPlan::Free);
    expect($subExpired->status)->toBe('expired');

    // Owner B tetap Pro dan subscription active
    expect($ownerActive->subscription_plan)->toBe(SubscriptionPlan::Pro);
    expect($subActive->status)->toBe('active');
});

test('halaman properties menampilkan banner ajakan upgrade ketika kuota free penuh', function () {
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    Property::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($owner)->get(route('owner.properties.index'));
    $response->assertOk();
    $response->assertSee('Batas Kuota Properti Paket Free Telah Penuh');
    $response->assertSee(route('owner.subscription.index'));
});

test('halaman rooms menampilkan banner ajakan upgrade ketika kuota free penuh', function () {
    $owner = User::factory()->create([
        'role' => UserRole::Owner,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $property = Property::factory()->create(['user_id' => $owner->id]);
    Room::factory()->count(10)->create(['property_id' => $property->id]);

    $response = $this->actingAs($owner)->get(route('owner.rooms.index'));
    $response->assertOk();
    $response->assertSee('Batas Kuota Kamar Paket Free Telah Penuh');
    $response->assertSee(route('owner.subscription.index'));
});
