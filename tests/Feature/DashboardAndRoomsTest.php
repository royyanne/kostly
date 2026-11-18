<?php

use App\Enums\RoomStatus;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->owner = User::where('email', 'owner@kostly.id')->first();
});

test('owner can view dashboard with calculated statistics and room grid', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.dashboard'));

    $response->assertOk()
        ->assertSee('Ringkasan Bisnis Kost')
        ->assertSee('Kost Griya Nyaman Pasteur')
        ->assertSee('Okupansi Kamar')
        ->assertSee('Denah Visual Kamar Kost')
        ->assertSee('101')
        ->assertSee('Deluxe AC');
});

test('owner can view properties page and create a new property', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.properties.index'));

    $response->assertOk()
        ->assertSee('Kost Griya Nyaman Pasteur');

    $postResponse = $this->actingAs($this->owner)->post(route('owner.properties.store'), [
        'name' => 'Kost Asri Dago',
        'address' => 'Jl. Ir. H. Juanda No. 120, Bandung',
        'description' => 'Kost tenang dekat ITB.',
    ]);

    $postResponse->assertRedirect(route('owner.properties.index'));
    $this->assertDatabaseHas('properties', [
        'user_id' => $this->owner->id,
        'name' => 'Kost Asri Dago',
    ]);
});

test('owner can update their own property', function () {
    $property = $this->owner->properties()->first();

    $response = $this->actingAs($this->owner)->put(route('owner.properties.update', $property), [
        'name' => 'Kost Griya Nyaman Pasteur Updated',
        'address' => 'Jl. Pasteur No. 45 Baru',
        'description' => 'Fasilitas terupdate.',
    ]);

    $response->assertRedirect(route('owner.properties.index'));
    $this->assertDatabaseHas('properties', [
        'id' => $property->id,
        'name' => 'Kost Griya Nyaman Pasteur Updated',
    ]);
});

test('owner cannot update another owners property', function () {
    $otherOwner = User::factory()->create();
    $otherProperty = Property::create([
        'user_id' => $otherOwner->id,
        'name' => 'Kost Milik Orang Lain',
        'address' => 'Jl. Antah Berantah',
    ]);

    $response = $this->actingAs($this->owner)->put(route('owner.properties.update', $otherProperty), [
        'name' => 'Coba Ubah Properti Orang Lain',
        'address' => 'Alamat Bajakan',
    ]);

    $response->assertForbidden();
});

test('owner can view rooms page and filter by status', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.rooms.index'));

    $response->assertOk()
        ->assertSee('Visual Grid')
        ->assertSee('101')
        ->assertSee('Tersedia')
        ->assertSee('Terisi');

    // Filter available only
    $filterResponse = $this->actingAs($this->owner)->get(route('owner.rooms.index', ['status' => 'available']));
    $filterResponse->assertOk()
        ->assertSee('203'); // 203 is Available
});

test('owner can create a new room in their property', function () {
    $property = $this->owner->properties()->first();

    $response = $this->actingAs($this->owner)->post(route('owner.rooms.store'), [
        'property_id' => $property->id,
        'room_number' => '301',
        'type' => 'Studio Roof',
        'price' => 2500000,
        'status' => RoomStatus::Available->value,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('rooms', [
        'property_id' => $property->id,
        'room_number' => '301',
        'type' => 'Studio Roof',
    ]);
});

test('owner cannot create duplicate room number in the same property', function () {
    $property = $this->owner->properties()->first();

    $response = $this->actingAs($this->owner)->post(route('owner.rooms.store'), [
        'property_id' => $property->id,
        'room_number' => '101', // Already exists in seeder
        'type' => 'Deluxe',
        'price' => 1500000,
        'status' => RoomStatus::Available->value,
    ]);

    $response->assertSessionHasErrors('room_number');
});

test('owner can quick update room status from visual grid', function () {
    $room = $this->owner->rooms()->where('status', RoomStatus::Available)->first();

    $response = $this->actingAs($this->owner)->patch(route('owner.rooms.updateStatus', $room), [
        'status' => RoomStatus::Maintenance->value,
    ]);

    $response->assertSessionHas('success');
    expect($room->fresh()->status)->toBe(RoomStatus::Maintenance);
});

test('owner cannot delete room that currently has active tenant', function () {
    $occupiedRoom = $this->owner->rooms()->where('status', RoomStatus::Occupied)->first();

    $response = $this->actingAs($this->owner)->delete(route('owner.rooms.destroy', $occupiedRoom));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('rooms', ['id' => $occupiedRoom->id]);
});

test('owner can delete room that is unoccupied', function () {
    $availableRoom = $this->owner->rooms()->where('status', RoomStatus::Available)->first();

    $response = $this->actingAs($this->owner)->delete(route('owner.rooms.destroy', $availableRoom));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('rooms', ['id' => $availableRoom->id]);
});
