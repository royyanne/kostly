<?php

use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Models\Expense;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->owner = User::where('email', 'owner@kostly.id')->first();
    $this->property = $this->owner->properties()->first();
});

test('owner can view tenants list and register a new tenant to an available room', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.tenants.index'));

    $response->assertOk()
        ->assertSee('Data Penghuni Kost')
        ->assertSee('Andika Pratama');

    // Pick an available room
    $availableRoom = $this->owner->rooms()->where('status', RoomStatus::Available)->first();

    $postResponse = $this->actingAs($this->owner)->post(route('owner.tenants.store'), [
        'property_id' => $this->property->id,
        'room_id' => $availableRoom->id,
        'name' => 'Bambang Pamungkas',
        'phone' => '081234567899',
        'emergency_contact' => '081234567800 (Orang Tua)',
        'start_date' => now()->toDateString(),
        'due_day' => 10,
    ]);

    $postResponse->assertRedirect(route('owner.tenants.index'));
    $this->assertDatabaseHas('tenants', [
        'name' => 'Bambang Pamungkas',
        'room_id' => $availableRoom->id,
        'is_active' => true,
    ]);

    // Room should now be marked Occupied
    expect($availableRoom->fresh()->status)->toBe(RoomStatus::Occupied);
});

test('owner can checkout tenant and room becomes available again', function () {
    $tenant = $this->owner->tenants()->where('is_active', true)->whereNotNull('room_id')->first();
    $roomId = $tenant->room_id;

    $response = $this->actingAs($this->owner)->patch(route('owner.tenants.checkout', $tenant));

    $response->assertRedirect(route('owner.tenants.index'));
    expect($tenant->fresh()->is_active)->toBeFalse();

    $room = Room::find($roomId);
    expect($room->status)->toBe(RoomStatus::Available);
});

test('owner can view invoices and filter by status', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.invoices.index'));

    $response->assertOk()
        ->assertSee('Penagihan Otomatis')
        ->assertSee('Belum Lunas')
        ->assertSee('Sudah Lunas');

    // Filter unpaid only
    $unpaidResponse = $this->actingAs($this->owner)->get(route('owner.invoices.index', ['status' => 'unpaid']));
    $unpaidResponse->assertOk();
});

test('owner can create manual invoice for tenant', function () {
    $tenant = $this->owner->tenants()->where('is_active', true)->first();

    $response = $this->actingAs($this->owner)->post(route('owner.invoices.store'), [
        'tenant_id' => $tenant->id,
        'amount' => 1500000,
        'additional_fee' => 50000,
        'due_date' => now()->addDays(5)->toDateString(),
    ]);

    $response->assertRedirect(route('owner.invoices.index'));
    $this->assertDatabaseHas('invoices', [
        'tenant_id' => $tenant->id,
        'amount' => 1500000,
        'additional_fee' => 50000,
        'total_amount' => 1550000,
        'status' => InvoiceStatus::Unpaid,
    ]);
});

test('owner can generate monthly invoices automatically for active tenants', function () {
    $response = $this->actingAs($this->owner)->post(route('owner.invoices.generateMonthly'));

    $response->assertRedirect(route('owner.invoices.index'));
    $response->assertSessionHas('success');
});

test('owner can mark an unpaid invoice as paid', function () {
    $unpaidInvoice = $this->owner->invoices()->where('status', InvoiceStatus::Unpaid)->first();

    $response = $this->actingAs($this->owner)->patch(route('owner.invoices.markPaid', $unpaidInvoice), [
        'payment_method' => 'Transfer BCA',
    ]);

    $response->assertRedirect(route('owner.invoices.index'));
    expect($unpaidInvoice->fresh()->status)->toBe(InvoiceStatus::Paid)
        ->and($unpaidInvoice->fresh()->payment_method)->toBe('Transfer BCA')
        ->and($unpaidInvoice->fresh()->paid_at)->not->toBeNull();
});

test('owner can send whatsapp reminder for invoice', function () {
    $unpaidInvoice = $this->owner->invoices()->where('status', InvoiceStatus::Unpaid)->first();

    $response = $this->actingAs($this->owner)->post(route('owner.invoices.sendReminder', $unpaidInvoice));

    $response->assertRedirect(route('owner.invoices.index'));
    $response->assertSessionHas('success');
});

test('owner can record and manage operational expenses', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.expenses.index'));

    $response->assertOk()
        ->assertSee('Pencatatan Biaya Operasional')
        ->assertSee('Tagihan Listrik PLN');

    // Create new expense
    $postResponse = $this->actingAs($this->owner)->post(route('owner.expenses.store'), [
        'property_id' => $this->property->id,
        'title' => 'Beli Pompa Air Baru',
        'category' => 'Pemeliharaan',
        'amount' => 650000,
        'expense_date' => now()->toDateString(),
        'notes' => 'Ganti pompa air sumur yang mati',
    ]);

    $postResponse->assertRedirect(route('owner.expenses.index'));
    $this->assertDatabaseHas('expenses', [
        'property_id' => $this->property->id,
        'title' => 'Beli Pompa Air Baru',
        'amount' => 650000,
    ]);
});

test('owner can view and update complaint status', function () {
    $response = $this->actingAs($this->owner)->get(route('owner.complaints.index'));

    $response->assertOk()
        ->assertSee('Laporan Komplain Penghuni')
        ->assertSee('AC Kamar 103 Kurang Dingin');

    $complaint = $this->owner->complaints()->where('status', ComplaintStatus::Pending)->first();

    $patchResponse = $this->actingAs($this->owner)->patch(route('owner.complaints.updateStatus', $complaint), [
        'status' => ComplaintStatus::InProgress->value,
    ]);

    $patchResponse->assertRedirect(route('owner.complaints.index'));
    expect($complaint->fresh()->status)->toBe(ComplaintStatus::InProgress);
});

test('data scoping prevents owner from altering another owners data', function () {
    $otherOwner = User::factory()->create();
    $otherProperty = $otherOwner->properties()->create([
        'name' => 'Kost Orang Lain',
        'address' => 'Jl. Lain',
    ]);

    $otherTenant = Tenant::create([
        'property_id' => $otherProperty->id,
        'name' => 'Penghuni Asing',
        'phone' => '08999999999',
        'start_date' => now()->toDateString(),
        'due_day' => 1,
    ]);

    $response = $this->actingAs($this->owner)->delete(route('owner.tenants.destroy', $otherTenant));
    $response->assertForbidden();
});
