<?php

use App\Enums\SubscriptionPlan;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================================
// Paket FREE – Properti
// ============================================================

test('akun free tidak dapat membuat lebih dari 1 properti', function () {
    $owner = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);

    // Sudah punya 1 properti
    Property::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($owner)->post(route('owner.properties.store'), [
        'name' => 'Properti Ke-2',
        'address' => 'Jl. Test No. 2, Jakarta',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseCount('properties', 1);
});

test('akun pro dapat membuat lebih dari 1 properti', function () {
    $owner = User::factory()->pro()->create();

    Property::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($owner)->post(route('owner.properties.store'), [
        'name' => 'Properti Ke-2',
        'address' => 'Jl. Test No. 2, Jakarta',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseCount('properties', 2);
});

// ============================================================
// Paket FREE – Kamar
// ============================================================

test('akun free tidak dapat membuat lebih dari 10 kamar', function () {
    $owner = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    $property = Property::factory()->create(['user_id' => $owner->id]);

    // Sudah punya 10 kamar
    Room::factory()->count(10)->create(['property_id' => $property->id]);

    $response = $this->actingAs($owner)->post(route('owner.rooms.store'), [
        'property_id' => $property->id,
        'room_number' => '999',
        'type' => 'Standard Fan',
        'price' => 1200000,
        'status' => 'available',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseCount('rooms', 10);
});

test('akun pro dapat membuat lebih dari 10 kamar', function () {
    $owner = User::factory()->pro()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);

    Room::factory()->count(10)->create(['property_id' => $property->id]);

    $response = $this->actingAs($owner)->post(route('owner.rooms.store'), [
        'property_id' => $property->id,
        'room_number' => '999',
        'type' => 'Standard Fan',
        'price' => 1200000,
        'status' => 'available',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseCount('rooms', 11);
});

// ============================================================
// Isolasi data: owner hanya bisa melihat propertinya sendiri
// ============================================================

test('owner tidak dapat melihat properti milik owner lain', function () {
    $ownerA = User::factory()->pro()->create();
    $ownerB = User::factory()->pro()->create();

    Property::factory()->create(['user_id' => $ownerA->id, 'name' => 'Kost Milik A']);
    Property::factory()->create(['user_id' => $ownerB->id, 'name' => 'Kost Milik B']);

    $response = $this->actingAs($ownerA)->get(route('owner.properties.index'));

    $response->assertOk();
    $response->assertSee('Kost Milik A');
    $response->assertDontSee('Kost Milik B');
});
