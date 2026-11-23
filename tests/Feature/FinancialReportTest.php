<?php

use App\Enums\InvoiceStatus;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================================
// Halaman Laporan Keuangan
// ============================================================

test('owner dapat mengakses halaman laporan keuangan', function () {
    $owner = User::factory()->create();

    $response = $this->actingAs($owner)->get(route('owner.reports.index'));

    $response->assertOk();
    $response->assertSee('Rekap Laporan Keuangan Bulanan');
    $response->assertSee('Tampilkan');
});

test('halaman laporan menampilkan filter bulan dan tahun', function () {
    $owner = User::factory()->create();

    $response = $this->actingAs($owner)->get(route('owner.reports.index'));

    $response->assertOk();
    $response->assertSee('name="month"', false);
    $response->assertSee('name="year"', false);
});

test('halaman laporan menampilkan ringkasan keuangan yang benar', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);
    $tenant = Tenant::factory()->create(['room_id' => $room->id, 'property_id' => $property->id]);

    // Buat 1 invoice lunas bulan ini
    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Paid,
        'total_amount' => 1500000,
        'paid_at' => now()->startOfMonth()->addDays(5),
    ]);

    // Buat 1 pengeluaran bulan ini
    Expense::factory()->create([
        'property_id' => $property->id,
        'amount' => 300000,
        'expense_date' => now()->startOfMonth()->addDays(3),
    ]);

    $response = $this->actingAs($owner)->get(route('owner.reports.index', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();
    $response->assertSee('1.500.000');
    $response->assertSee('300.000');
    $response->assertSee('1.200.000'); // laba bersih
});

// ============================================================
// Export CSV
// ============================================================

test('endpoint export csv mengembalikan file csv yang valid', function () {
    $owner = User::factory()->create();

    $response = $this->actingAs($owner)->get(route('owner.reports.exportCsv', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('file csv mengandung utf-8 bom dan header kolom yang benar', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);
    $tenant = Tenant::factory()->create(['room_id' => $room->id, 'property_id' => $property->id]);

    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Paid,
        'total_amount' => 2000000,
        'paid_at' => now()->startOfMonth(),
    ]);

    $response = $this->actingAs($owner)->get(route('owner.reports.exportCsv', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();

    $content = $response->streamedContent();

    // Cek UTF-8 BOM
    expect(substr($content, 0, 3))->toBe("\xEF\xBB\xBF");

    // Cek header dokumen
    expect($content)->toContain('LAPORAN KEUANGAN BULANAN - KOSTLY');
    expect($content)->toContain('Pemasukan (IDR)');
    expect($content)->toContain('Pengeluaran (IDR)');
});

test('filename csv mengandung nama bulan dan tahun', function () {
    $owner = User::factory()->create();

    $response = $this->actingAs($owner)->get(route('owner.reports.exportCsv', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();

    $contentDisposition = $response->headers->get('Content-Disposition');
    expect($contentDisposition)->toContain((string) now()->year);
});

// ============================================================
// Halaman Print/PDF
// ============================================================

test('owner dapat mengakses halaman cetak laporan keuangan', function () {
    $owner = User::factory()->create();

    $response = $this->actingAs($owner)->get(route('owner.reports.print', [
        'month' => now()->month,
        'year' => now()->year,
    ]));

    $response->assertOk();
    $response->assertSee('LAPORAN KEUANGAN BULANAN');
    $response->assertSee('Kostly');
    $response->assertSee('window.print()', false);
});

// ============================================================
// Proteksi Akses
// ============================================================

test('tamu tidak dapat mengakses halaman laporan keuangan', function () {
    $response = $this->get(route('owner.reports.index'));

    $response->assertRedirect(route('login'));
});

test('tamu tidak dapat mengunduh csv laporan keuangan', function () {
    $response = $this->get(route('owner.reports.exportCsv'));

    $response->assertRedirect(route('login'));
});
