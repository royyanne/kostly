<?php

use App\Enums\RoomStatus;
use App\Enums\SubscriptionPlan;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('owner can access their properties, rooms, tenants, invoices, expenses and complaints through relationships', function () {
    $owner = User::where('email', 'owner@kostly.id')->first();

    expect($owner)->not->toBeNull()
        ->and($owner->subscription_plan)->toBe(SubscriptionPlan::Pro)
        ->and($owner->properties()->count())->toBe(1)
        ->and($owner->rooms()->count())->toBe(10)
        ->and($owner->tenants()->count())->toBe(7)
        ->and($owner->invoices()->count())->toBe(7)
        ->and($owner->expenses()->count())->toBe(5)
        ->and($owner->complaints()->count())->toBe(3);
});

test('data scoping prevents user from seeing other users data', function () {
    $owner1 = User::where('email', 'owner@kostly.id')->first();

    $owner2 = User::factory()->create([
        'email' => 'other_owner@kostly.id',
    ]);

    $property2 = Property::create([
        'user_id' => $owner2->id,
        'name' => 'Kost Bintang Timur',
        'address' => 'Jl. Dago No. 10',
    ]);

    $room2 = Room::create([
        'property_id' => $property2->id,
        'room_number' => '301',
        'type' => 'Standard',
        'price' => 1000000,
        'status' => RoomStatus::Available,
    ]);

    // Check scope for owner 1
    expect(Property::forUser($owner1)->pluck('id'))->not->toContain($property2->id)
        ->and(Room::forUser($owner1)->pluck('id'))->not->toContain($room2->id);

    // Check scope for owner 2
    expect(Property::forUser($owner2)->count())->toBe(1)
        ->and(Room::forUser($owner2)->count())->toBe(1);
});

test('send invoice reminders command runs successfully', function () {
    $this->artisan('kostly:send-reminders --dry-run')
        ->assertSuccessful();
});
