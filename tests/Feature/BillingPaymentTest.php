<?php

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('tenant dapat mengunggah bukti pembayaran dan status berubah menjadi pending_verification', function () {
    Storage::fake('public');

    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id, 'price' => 1500000]);

    $tenantUser = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant = Tenant::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Unpaid,
        'total_amount' => 1500000,
        'payment_proof' => null,
    ]);

    $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

    $response = $this->actingAs($tenantUser)->post(route('tenant.invoices.uploadProof', $invoice), [
        'payment_proof' => $file,
        'payment_method' => 'Transfer Bank BCA',
    ]);

    $response->assertRedirect(route('tenant.invoices.index'));
    $response->assertSessionHas('success');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PendingVerification);
    expect($invoice->payment_proof)->not->toBeNull();
    expect($invoice->payment_method)->toBe('Transfer Bank BCA');
    expect($invoice->rejected_reason)->toBeNull();

    Storage::disk('public')->assertExists($invoice->payment_proof);
});

test('tenant lain tidak dapat mengunggah bukti pembayaran tagihan yang bukan miliknya', function () {
    Storage::fake('public');

    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);

    $tenant1 = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);
    $tenantUser2 = User::factory()->create(['role' => UserRole::Tenant]);
    Tenant::factory()->create([
        'user_id' => $tenantUser2->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant1->id,
        'status' => InvoiceStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $response = $this->actingAs($tenantUser2)->post(route('tenant.invoices.uploadProof', $invoice), [
        'payment_proof' => $file,
    ]);

    $response->assertForbidden();
});

test('owner dapat memverifikasi pembayaran pending menjadi paid', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);
    $tenant = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::PendingVerification,
        'payment_proof' => 'payment-proofs/sample.jpg',
        'paid_at' => null,
    ]);

    $response = $this->actingAs($owner)->patch(route('owner.invoices.verify', $invoice));

    $response->assertRedirect(route('owner.invoices.index'));
    $response->assertSessionHas('success');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Paid);
    expect($invoice->paid_at)->not->toBeNull();
    expect($invoice->rejected_reason)->toBeNull();
});

test('owner dapat menolak bukti bayar yang tidak valid dengan alasan penolakan', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);
    $tenant = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::PendingVerification,
        'payment_proof' => 'payment-proofs/sample.jpg',
    ]);

    $response = $this->actingAs($owner)->patch(route('owner.invoices.rejectPayment', $invoice), [
        'rejected_reason' => 'Foto bukti buram dan nominal tidak sesuai.',
    ]);

    $response->assertRedirect(route('owner.invoices.index'));
    $response->assertSessionHas('info');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Unpaid);
    expect($invoice->rejected_reason)->toBe('Foto bukti buram dan nominal tidak sesuai.');
});

test('invoice berstatus paid otomatis menambah total pemasukan pada laporan keuangan owner', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);
    $tenant = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    // Tagihan 1: Lunas di bulan ini (Rp 2.000.000)
    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Paid,
        'total_amount' => 2000000,
        'paid_at' => now()->startOfMonth()->addDays(2),
    ]);

    // Tagihan 2: Belum lunas (Rp 1.500.000) - tidak boleh masuk pemasukan
    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Unpaid,
        'total_amount' => 1500000,
        'paid_at' => null,
    ]);

    // Tagihan 3: Pending verification (Rp 1.000.000) - tidak boleh masuk pemasukan
    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::PendingVerification,
        'total_amount' => 1000000,
        'paid_at' => null,
    ]);

    $response = $this->actingAs($owner)->get(route('owner.reports.index', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();
    // Hanya tagihan lunas (2.000.000) yang terhitung
    $response->assertSee('2.000.000');
    $response->assertDontSee('4.500.000');
});

test('command generate-monthly membuat tagihan baru untuk seluruh tenant aktif tanpa duplikasi', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room1 = Room::factory()->create(['property_id' => $property->id, 'price' => 1200000]);
    $room2 = Room::factory()->create(['property_id' => $property->id, 'price' => 1800000]);

    // Tenant 1 & 2 aktif
    $tenant1 = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room1->id,
        'is_active' => true,
        'due_day' => 10,
    ]);
    $tenant2 = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room2->id,
        'is_active' => true,
        'due_day' => 15,
    ]);

    // Tenant 3 nonaktif (tidak boleh dibuatkan tagihan)
    $tenant3 = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room1->id,
        'is_active' => false,
        'due_day' => 5,
    ]);

    // Jalankan artisan command untuk bulan sekarang
    $this->artisan('invoices:generate-monthly')
        ->assertSuccessful();

    // Verifikasi tagihan dibuat untuk tenant 1 & 2
    expect(Invoice::where('tenant_id', $tenant1->id)->count())->toBe(1);
    expect(Invoice::where('tenant_id', $tenant2->id)->count())->toBe(1);
    expect(Invoice::where('tenant_id', $tenant3->id)->count())->toBe(0);

    // Jalankan command sekali lagi (anti-duplikasi di bulan yang sama)
    $this->artisan('invoices:generate-monthly')
        ->assertSuccessful();

    expect(Invoice::where('tenant_id', $tenant1->id)->count())->toBe(1);
    expect(Invoice::where('tenant_id', $tenant2->id)->count())->toBe(1);
});

test('owner dapat men-generate tagihan bulanan melalui tombol di web dashboard', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id, 'price' => 1000000]);

    $tenant = Tenant::factory()->create([
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
        'due_day' => 5,
    ]);

    $response = $this->actingAs($owner)->post(route('owner.invoices.generateMonthly'));

    $response->assertRedirect(route('owner.invoices.index'));
    $response->assertSessionHas('success');

    expect(Invoice::where('tenant_id', $tenant->id)->count())->toBe(1);
});
