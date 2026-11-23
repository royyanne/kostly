<?php

use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('anyone can view public payment page using valid invoice code', function () {
    $invoice = Invoice::where('status', InvoiceStatus::Unpaid)->first();

    $response = $this->get(route('public.pay', $invoice->invoice_code));

    $response->assertOk()
        ->assertSee($invoice->invoice_code)
        ->assertSee($invoice->property->name)
        ->assertSee($invoice->tenant->name)
        ->assertSee('QRIS Instan')
        ->assertSee('Virtual Account');
});

test('paid invoice shows payment receipt on public payment page', function () {
    $paidInvoice = Invoice::where('status', InvoiceStatus::Paid)->first();

    $response = $this->get(route('public.pay', $paidInvoice->invoice_code));

    $response->assertOk()
        ->assertSee('Pembayaran Telah Lunas')
        ->assertSee('Cetak Kuitansi');
});

test('tenant can confirm payment directly from payment page', function () {
    $unpaidInvoice = Invoice::where('status', InvoiceStatus::Unpaid)->first();

    $response = $this->post(route('public.pay.confirm', $unpaidInvoice->invoice_code), [
        'payment_method' => 'QRIS Dinamis',
    ]);

    $response->assertRedirect(route('public.pay', $unpaidInvoice->invoice_code));
    $response->assertSessionHas('success');

    expect($unpaidInvoice->fresh()->status)->toBe(InvoiceStatus::Paid)
        ->and($unpaidInvoice->fresh()->payment_method)->toBe('QRIS Dinamis')
        ->and($unpaidInvoice->fresh()->paid_at)->not->toBeNull();
});

test('tenant can access their public portal without login using unique access_token', function () {
    $tenant = Tenant::where('is_active', true)->first();

    $response = $this->get(route('public.portal', $tenant->access_token));

    $response->assertOk()
        ->assertSee($tenant->name)
        ->assertSee('Kamar '.$tenant->room->room_number)
        ->assertSee('Portal Mandiri Anak Kost')
        ->assertSee('Riwayat Tagihan')
        ->assertSee('Lapor Kerusakan')
        ->assertSee('Setiap Tgl '.$tenant->due_day);
});

test('invalid tenant access_token returns friendly 404 page', function () {
    $response = $this->get(route('public.portal', 'invalid-nonexistent-token-xyz'));

    $response->assertNotFound()
        ->assertSee('Tautan Portal Tidak Valid');
});

test('inactive tenant access_token returns friendly 404 checkout page', function () {
    $tenant = Tenant::first();
    $tenant->update(['is_active' => false]);

    $response = $this->get(route('public.portal', $tenant->access_token));

    $response->assertNotFound()
        ->assertSee('Status Sewa Sudah Tidak Aktif')
        ->assertSee($tenant->name);
});

test('tenant can view and print official receipt for paid invoice from portal', function () {
    $tenant = Tenant::where('is_active', true)->first();
    $paidInvoice = Invoice::where('tenant_id', $tenant->id)->where('status', InvoiceStatus::Paid)->first();

    if (! $paidInvoice) {
        $paidInvoice = Invoice::where('status', InvoiceStatus::Paid)->first();
        $tenant = $paidInvoice->tenant;
    }

    $response = $this->get(route('public.portal.receipt', [
        'access_token' => $tenant->access_token,
        'invoice_code' => $paidInvoice->invoice_code,
    ]));

    $response->assertOk()
        ->assertSee('KUITANSI SEWA KOST')
        ->assertSee($tenant->name)
        ->assertSee('LUNAS')
        ->assertSee('KUI-'.$paidInvoice->invoice_code)
        ->assertSee('TOTAL PEMBAYARAN LUNAS');
});

test('tenant can submit complaint with photo upload directly from portal', function () {
    Storage::fake('public');

    $tenant = Tenant::where('is_active', true)->first();
    $photo = UploadedFile::fake()->image('kran_bocor.jpg');

    $response = $this->post(route('public.portal.complaint', $tenant->access_token), [
        'title' => 'Gagang Pintu Kamar Mandi Copot',
        'description' => 'Tadi sore gagang pintu kamar mandi tiba-tiba longgar dan copot, tolong diperbaiki.',
        'photo' => $photo,
    ]);

    $response->assertRedirect(route('public.portal', $tenant->access_token));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('complaints', [
        'tenant_id' => $tenant->id,
        'property_id' => $tenant->property_id,
        'room_id' => $tenant->room_id,
        'title' => 'Gagang Pintu Kamar Mandi Copot',
        'status' => ComplaintStatus::Pending->value,
    ]);

    // Verifikasi komplain muncul di dashboard pemilik (owner)
    $owner = User::find($tenant->property->user_id);
    $this->actingAs($owner)
        ->get(route('owner.complaints.index'))
        ->assertOk()
        ->assertSee('Gagang Pintu Kamar Mandi Copot')
        ->assertSee($tenant->name);
});

test('payment webhook marks invoice as paid upon receiving settlement notification', function () {
    $unpaidInvoice = Invoice::where('status', InvoiceStatus::Unpaid)->first();

    $response = $this->postJson(route('webhook.payment'), [
        'invoice_code' => $unpaidInvoice->invoice_code,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
    ]);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
        ]);

    expect($unpaidInvoice->fresh()->status)->toBe(InvoiceStatus::Paid);
});

test('invalid invoice code returns 404', function () {
    $response = $this->get(route('public.pay', 'INVALID-CODE-999'));
    $response->assertNotFound();
});
