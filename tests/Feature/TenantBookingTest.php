<?php

use App\Enums\BookingStatus;
use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================================
// 1. Halaman Publik (Landing Page & Detail Kost)
// ============================================================

test('guest dapat melihat daftar kost di landing page beserta kamar kosong', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Kost Megah Ciumbuleuit',
        'city' => 'Bandung',
    ]);

    Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '101',
        'status' => RoomStatus::Available,
        'price' => 1500000,
    ]);

    Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '102',
        'status' => RoomStatus::Occupied,
        'price' => 1500000,
    ]);

    $response = $this->get(route('landing'));

    $response->assertOk();
    $response->assertSee('Kost Megah Ciumbuleuit');
    $response->assertSee('1 Kamar Kosong');
    $response->assertSee('1.500.000');
});

test('guest dapat memfilter daftar kost berdasarkan kota dan harga', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);

    $propertyBandung = Property::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Kost Asri Bandung',
        'city' => 'Bandung',
    ]);
    Room::factory()->create([
        'property_id' => $propertyBandung->id,
        'status' => RoomStatus::Available,
        'price' => 1200000,
    ]);

    $propertyJakarta = Property::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Kost Mewah Jakarta',
        'city' => 'Jakarta',
    ]);
    Room::factory()->create([
        'property_id' => $propertyJakarta->id,
        'status' => RoomStatus::Available,
        'price' => 3000000,
    ]);

    // Filter kota Bandung
    $resCity = $this->get(route('landing', ['city' => 'Bandung']));
    $resCity->assertOk();
    $resCity->assertSee('Kost Asri Bandung');
    $resCity->assertDontSee('Kost Mewah Jakarta');

    // Filter harga maksimal 2.000.000
    $resPrice = $this->get(route('landing', ['max_price' => 2000000]));
    $resPrice->assertOk();
    $resPrice->assertSee('Kost Asri Bandung');
    $resPrice->assertDontSee('Kost Mewah Jakarta');
});

test('guest dapat melihat detail kost dan daftar kamar yang available', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Kost Harmoni Dago',
        'slug' => 'kost-harmoni-dago',
        'address' => 'Jl. Dago Asri No. 12',
    ]);

    $roomAvail = Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '101',
        'type' => 'Deluxe AC',
        'price' => 1800000,
        'status' => RoomStatus::Available,
    ]);

    $roomOcc = Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '102',
        'type' => 'Standard Fan',
        'price' => 1200000,
        'status' => RoomStatus::Occupied,
    ]);

    // Mengakses via slug
    $response = $this->get(route('public.kost.show', 'kost-harmoni-dago'));

    $response->assertOk();
    $response->assertSee('Kost Harmoni Dago');
    $response->assertSee('Kamar 101');
    $response->assertSee('Deluxe AC');
    $response->assertSee('1.800.000');
    $response->assertSee('Form Pengajuan Sewa');

    // Kamar occupied tidak boleh muncul di daftar kamar kosong yang bisa dipilih
    $response->assertDontSee('Kamar 102 (Standard Fan)');
});

// ============================================================
// 2. Form Pengajuan Sewa (Booking Request)
// ============================================================

test('calon penyewa atau guest dapat mengajukan sewa kamar dengan status pending', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '201',
        'price' => 1500000,
        'status' => RoomStatus::Available,
    ]);

    $tenant = User::factory()->tenant()->create([
        'email' => 'calon@tenant.id',
        'name' => 'Calon Anak Kost',
        'phone' => '081233445566',
    ]);

    $response = $this->actingAs($tenant)->post(route('public.kost.book', $property), [
        'room_id' => $room->id,
        'start_date' => now()->addDays(2)->toDateString(),
        'duration_type' => 'monthly',
        'duration_months' => 3,
        'name' => 'Calon Anak Kost',
        'email' => 'calon@tenant.id',
        'phone' => '081233445566',
        'notes' => 'Tolong sediakan kasur tambahan jika ada.',
    ]);

    $response->assertRedirect(route('tenant.dashboard'));
    $response->assertSessionHas('success');

    // Cek record di tabel bookings
    $this->assertDatabaseHas('bookings', [
        'property_id' => $property->id,
        'room_id' => $room->id,
        'user_id' => $tenant->id,
        'email' => 'calon@tenant.id',
        'duration_months' => 3,
        'total_price' => 4500000, // 1.500.000 * 3
        'status' => BookingStatus::Pending->value,
    ]);

    // Status kamar belum berubah sebelum di-approve
    expect($room->fresh()->status)->toBe(RoomStatus::Available);
});

test('owner dapat menyetujui pengajuan sewa dan otomatis mengubah status kamar jadi occupied', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create([
        'property_id' => $property->id,
        'room_number' => '301',
        'status' => RoomStatus::Available,
        'price' => 2000000,
    ]);

    $tenantUser = User::factory()->tenant()->create(['email' => 'penyewa@test.id']);

    $booking = Booking::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'name' => 'Penyewa Resmi',
        'phone' => '081299887766',
        'start_date' => now()->toDateString(),
        'total_price' => 2000000,
        'status' => BookingStatus::Pending,
    ]);

    // Owner menyetujui (Approve)
    $response = $this->actingAs($owner)
        ->patch(route('owner.bookings.approve', $booking));

    $response->assertRedirect(route('owner.bookings.index'));
    $response->assertSessionHas('success');

    // 1. Status booking menjadi Approved
    expect($booking->fresh()->status)->toBe(BookingStatus::Approved);

    // 2. Status kamar otomatis menjadi Occupied
    expect($room->fresh()->status)->toBe(RoomStatus::Occupied);

    // 3. Terdaftar di tabel tenants sebagai penghuni aktif
    $this->assertDatabaseHas('tenants', [
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'name' => 'Penyewa Resmi',
        'is_active' => true,
    ]);

    // 4. Terbit tagihan awal (Invoice)
    $this->assertDatabaseHas('invoices', [
        'property_id' => $property->id,
        'total_amount' => 2000000,
        'status' => InvoiceStatus::Unpaid->value,
    ]);
});

test('owner dapat menolak pengajuan sewa', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create([
        'property_id' => $property->id,
        'status' => RoomStatus::Available,
    ]);

    $booking = Booking::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'status' => BookingStatus::Pending,
    ]);

    $response = $this->actingAs($owner)
        ->patch(route('owner.bookings.reject', $booking), [
            'rejection_reason' => 'Kamar sedang dalam proses renovasi.',
        ]);

    $response->assertRedirect(route('owner.bookings.index'));
    $response->assertSessionHas('info');

    expect($booking->fresh()->status)->toBe(BookingStatus::Rejected);
    expect($booking->fresh()->rejection_reason)->toBe('Kamar sedang dalam proses renovasi.');
    expect($room->fresh()->status)->toBe(RoomStatus::Available);
});

// ============================================================
// 3. Isolasi Akses (Role-Based Access Control)
// ============================================================

test('tenant tidak dapat mengakses route owner dan mendapat 403 forbidden', function () {
    $tenant = User::factory()->tenant()->create();

    $this->actingAs($tenant)->get(route('owner.dashboard'))->assertForbidden();
    $this->actingAs($tenant)->get(route('owner.properties.index'))->assertForbidden();
    $this->actingAs($tenant)->get(route('owner.rooms.index'))->assertForbidden();
    $this->actingAs($tenant)->get(route('owner.bookings.index'))->assertForbidden();
    $this->actingAs($tenant)->get(route('owner.invoices.index'))->assertForbidden();
});

test('owner tidak dapat mengakses route tenant dan mendapat 403 forbidden', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);

    $this->actingAs($owner)->get(route('tenant.dashboard'))->assertForbidden();
    $this->actingAs($owner)->get(route('tenant.invoices.index'))->assertForbidden();
    $this->actingAs($owner)->get(route('tenant.complaints.index'))->assertForbidden();
});

// ============================================================
// 4. Integrasi Dashboard & Komplain Tenant ke Owner
// ============================================================

test('tenant aktif dapat mengakses dashboard dan melihat info kamarnya', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id, 'name' => 'Kost Anggrek']);
    $room = Room::factory()->create(['property_id' => $property->id, 'room_number' => '404']);
    $tenantUser = User::factory()->tenant()->create();

    $tenant = Tenant::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'name' => $tenantUser->name,
        'is_active' => true,
    ]);

    $response = $this->actingAs($tenantUser)->get(route('tenant.dashboard'));

    $response->assertOk();
    $response->assertSee('Kost Anggrek');
    $response->assertSee('Kamar 404');
    $response->assertSee('Aktif Terisi');
});

test('tenant dapat mengirim komplain dan otomatis muncul di daftar komplain owner', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id, 'room_number' => '105']);
    $tenantUser = User::factory()->tenant()->create();

    $tenant = Tenant::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    // 1. Tenant mengirim laporan komplain
    $response = $this->actingAs($tenantUser)->post(route('tenant.complaints.store'), [
        'title' => 'Kran Kamar Mandi Patah',
        'description' => 'Kran air di wastafel kamar mandi patah saat diputar tadi pagi.',
    ]);

    $response->assertRedirect(route('tenant.complaints.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('complaints', [
        'property_id' => $property->id,
        'room_id' => $room->id,
        'tenant_id' => $tenant->id,
        'title' => 'Kran Kamar Mandi Patah',
        'status' => ComplaintStatus::Pending->value,
    ]);

    // 2. Owner melihat komplain tersebut di dashboard owner
    $ownerResponse = $this->actingAs($owner)->get(route('owner.complaints.index'));
    $ownerResponse->assertOk();
    $ownerResponse->assertSee('Kran Kamar Mandi Patah');
});
