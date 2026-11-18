<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Owner\ComplaintController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ExpenseController;
use App\Http\Controllers\Owner\InvoiceController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\TenantController;
use App\Http\Controllers\Public\LandingPageController;
use App\Http\Controllers\Public\PaymentPortalController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', LandingPageController::class)->name('landing');
Route::get('/pay/{invoice_code}', [PaymentPortalController::class, 'showPayment'])->name('public.pay');
Route::get('/portal/{access_token}', [PaymentPortalController::class, 'showPortal'])->name('public.portal');

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

    Route::prefix('dashboard')->name('owner.')->group(function () {
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
    });
});
