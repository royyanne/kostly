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
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    });
});
