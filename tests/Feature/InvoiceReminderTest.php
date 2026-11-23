<?php

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\InvoiceDueReminderNotification;
use App\Notifications\InvoiceOverdueNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('command mengirim InvoiceDueReminderNotification tepat pada H-3 dan H-1 sebelum jatuh tempo', function () {
    Notification::fake();

    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);

    $tenantUser3 = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant3 = Tenant::factory()->create([
        'user_id' => $tenantUser3->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $tenantUser1 = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant1 = Tenant::factory()->create([
        'user_id' => $tenantUser1->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    $tenantUser5 = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant5 = Tenant::factory()->create([
        'user_id' => $tenantUser5->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    // Tagihan H-3
    $invoiceH3 = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant3->id,
        'status' => InvoiceStatus::Unpaid,
        'due_date' => Carbon::today()->addDays(3),
    ]);

    // Tagihan H-1
    $invoiceH1 = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant1->id,
        'status' => InvoiceStatus::Unpaid,
        'due_date' => Carbon::today()->addDays(1),
    ]);

    // Tagihan H-5 (tidak boleh terkirim hari ini)
    $invoiceH5 = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant5->id,
        'status' => InvoiceStatus::Unpaid,
        'due_date' => Carbon::today()->addDays(5),
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    // Verifikasi notifikasi terkirim untuk H-3 dan H-1
    Notification::assertSentTo($tenantUser3, InvoiceDueReminderNotification::class, function ($notification) use ($invoiceH3) {
        return $notification->invoice->id === $invoiceH3->id && $notification->daysRemaining === 3;
    });

    Notification::assertSentTo($tenantUser1, InvoiceDueReminderNotification::class, function ($notification) use ($invoiceH1) {
        return $notification->invoice->id === $invoiceH1->id && $notification->daysRemaining === 1;
    });

    // Verifikasi H-5 tidak mendapatkan notifikasi
    Notification::assertNotSentTo($tenantUser5, InvoiceDueReminderNotification::class);
});

test('command mengubah status unpaid menjadi overdue jika melewati due_date dan mengirim notifikasi ke tenant dan owner', function () {
    Notification::fake();

    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);

    $tenantUser = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant = Tenant::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    // Tagihan kemarin (terlambat)
    $overdueInvoice = Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Unpaid,
        'due_date' => Carbon::today()->subDays(1),
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    $overdueInvoice->refresh();
    expect($overdueInvoice->status)->toBe(InvoiceStatus::Overdue);

    // Notifikasi terkirim ke Tenant
    Notification::assertSentTo($tenantUser, InvoiceOverdueNotification::class, function ($notification) use ($overdueInvoice) {
        return $notification->invoice->id === $overdueInvoice->id && $notification->recipientType === 'tenant';
    });

    // Notifikasi terkirim ke Owner
    Notification::assertSentTo($owner, InvoiceOverdueNotification::class, function ($notification) use ($overdueInvoice) {
        return $notification->invoice->id === $overdueInvoice->id && $notification->recipientType === 'owner';
    });
});

test('command bersifat idempotent (menjalankan command 2x di hari yang sama tidak mengirim notifikasi duplikat)', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $room = Room::factory()->create(['property_id' => $property->id]);

    $tenantUser = User::factory()->create(['role' => UserRole::Tenant]);
    $tenant = Tenant::factory()->create([
        'user_id' => $tenantUser->id,
        'property_id' => $property->id,
        'room_id' => $room->id,
        'is_active' => true,
    ]);

    Invoice::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
        'status' => InvoiceStatus::Unpaid,
        'due_date' => Carbon::today()->addDays(3),
    ]);

    // Eksekusi pertama
    $this->artisan('invoices:send-reminders')->assertSuccessful();
    expect($tenantUser->notifications()->count())->toBe(1);

    // Eksekusi kedua di hari yang sama
    $this->artisan('invoices:send-reminders')->assertSuccessful();
    expect($tenantUser->notifications()->count())->toBe(1); // Tetap 1, tidak duplikasi
});

test('endpoint mark-as-read notifikasi di portal berfungsi mengurangi unreadNotifications', function () {
    $user = User::factory()->create(['role' => UserRole::Tenant]);
    $property = Property::factory()->create();
    $tenant = Tenant::factory()->create(['user_id' => $user->id, 'property_id' => $property->id]);
    $invoice = Invoice::factory()->create(['property_id' => $property->id, 'tenant_id' => $tenant->id]);

    // Kirim notifikasi nyata ke database
    $user->notify(new InvoiceDueReminderNotification($invoice, 3));
    $user->notify(new InvoiceDueReminderNotification($invoice, 1));

    expect($user->unreadNotifications()->count())->toBe(2);

    $firstNotification = $user->unreadNotifications()->first();

    // 1. Tandai satu notifikasi telah dibaca
    $response = $this->actingAs($user)->patch(route('notifications.markAsRead', $firstNotification->id));
    $response->assertRedirect(route('tenant.invoices.index'));

    expect($user->fresh()->unreadNotifications()->count())->toBe(1);

    // 2. Tandai semua notifikasi telah dibaca
    $responseAll = $this->actingAs($user)->patch(route('notifications.markAllRead'));
    $responseAll->assertRedirect();

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});

test('navbar menampilkan badge lonceng notifikasi untuk user tenant dan owner', function () {
    $owner = User::factory()->create(['role' => UserRole::Owner]);
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $tenant = Tenant::factory()->create(['property_id' => $property->id]);
    $invoice = Invoice::factory()->create(['property_id' => $property->id, 'tenant_id' => $tenant->id]);

    $owner->notify(new InvoiceOverdueNotification($invoice, 'owner'));

    $response = $this->actingAs($owner)->get(route('owner.dashboard'));
    $response->assertOk();
    $response->assertSee('notification-bell-dropdown');
    $response->assertSee('Tandai Semua Dibaca');
});
