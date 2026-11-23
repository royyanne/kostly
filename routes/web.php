<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\ComplaintController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ExpenseController;
use App\Http\Controllers\Owner\FinancialReportController;
use App\Http\Controllers\Owner\InvoiceController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\SettingController as OwnerSettingController;
use App\Http\Controllers\Owner\SubscriptionController as OwnerSubscriptionController;
use App\Http\Controllers\Owner\TenantController;
use App\Http\Controllers\Public\LandingPageController;
use App\Http\Controllers\Public\PaymentPortalController;
use App\Http\Controllers\Public\PublicKostController;
use App\Http\Controllers\Tenant\TenantComplaintController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\TenantInvoiceController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', LandingPageController::class)->name('landing');

// Detail Kost & Pengajuan Sewa Publik
Route::get('/kost/{property}', [PublicKostController::class, 'show'])->name('public.kost.show');
Route::post('/kost/{property}/book', [PublicKostController::class, 'storeBooking'])->name('public.kost.book');

// Halaman Pembayaran Publik
Route::get('/pay/{invoice_code}', [PaymentPortalController::class, 'showPayment'])->name('public.pay');
Route::post('/pay/{invoice_code}/confirm', [PaymentPortalController::class, 'confirmPayment'])->name('public.pay.confirm');

// Portal Mandiri Penghuni (Tanpa Login)
Route::get('/portal/{access_token}', [PaymentPortalController::class, 'showPortal'])->name('public.portal');
Route::post('/portal/{access_token}/complaint', [PaymentPortalController::class, 'storeComplaint'])->name('public.portal.complaint');
Route::get('/portal/{access_token}/receipt/{invoice_code}', [PaymentPortalController::class, 'showReceipt'])->name('public.portal.receipt');

/*
|--------------------------------------------------------------------------
| Webhook Routes (Payment Gateway Callback)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/payment', [PaymentWebhookController::class, 'handle'])->name('webhook.payment');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Owner Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifikasi In-App (Universal untuk User yang Terautentikasi)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    });

    Route::prefix('dashboard')->name('owner.')->middleware('owner')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD Properti
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

        // CRUD Kamar Kost & Visual Grid
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.updateStatus');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        // Pengajuan Sewa (Bookings)
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

        // CRUD Manajemen Penghuni
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::patch('/tenants/{tenant}/checkout', [TenantController::class, 'checkout'])->name('tenants.checkout');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

        // CRUD Tagihan & Invoice Bulanan
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('/invoices/generate-monthly', [InvoiceController::class, 'generateMonthly'])->name('invoices.generateMonthly');
        Route::patch('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');
        Route::patch('/invoices/{invoice}/verify', [InvoiceController::class, 'verifyPayment'])->name('invoices.verify');
        Route::patch('/invoices/{invoice}/reject-payment', [InvoiceController::class, 'rejectPayment'])->name('invoices.rejectPayment');
        Route::post('/invoices/{invoice}/send-reminder', [InvoiceController::class, 'sendReminder'])->name('invoices.sendReminder');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // CRUD Pengeluaran Operasional
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        // CRUD Komplain Fasilitas
        Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
        Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
        Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');

        // Laporan Keuangan Bulanan
        Route::get('/reports', [FinancialReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [FinancialReportController::class, 'exportCsv'])->name('reports.exportCsv');
        Route::get('/reports/print', [FinancialReportController::class, 'printReport'])->name('reports.print');

        // Upgrade Paket & Langganan Owner
        Route::get('/subscription', [OwnerSubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/upgrade', [OwnerSubscriptionController::class, 'store'])->name('subscription.store');

        // Pengaturan Rekening & QRIS Owner
        Route::get('/settings', [OwnerSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [OwnerSettingController::class, 'updatePaymentSettings'])->name('settings.update');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Tenant Routes (Portal Anak Kost)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', TenantDashboardController::class)->name('dashboard');
    Route::get('/invoices', [TenantInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{invoice}/upload-proof', [TenantInvoiceController::class, 'uploadProof'])->name('invoices.uploadProof');
    Route::get('/complaints', [TenantComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/complaints', [TenantComplaintController::class, 'store'])->name('complaints.store');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/plan', [AdminUserController::class, 'updatePlan'])->name('users.updatePlan');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // Verifikasi Langganan Owner
    Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::patch('/subscriptions/{subscription}/approve', [AdminSubscriptionController::class, 'approve'])->name('subscriptions.approve');
    Route::patch('/subscriptions/{subscription}/reject', [AdminSubscriptionController::class, 'reject'])->name('subscriptions.reject');
});
