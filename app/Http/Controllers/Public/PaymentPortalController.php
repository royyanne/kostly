<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;

class PaymentPortalController extends Controller
{
    /**
     * Tampilkan halaman publik pembayaran tagihan berdasarkan kode tagihan.
     */
    public function showPayment(string $invoiceCode): View
    {
        $invoice = Invoice::with(['property', 'tenant.room'])
            ->where('invoice_code', $invoiceCode)
            ->firstOrFail();

        return view('public.pay', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Tampilkan portal penghuni mandiri tanpa login melalui access_token unik.
     */
    public function showPortal(string $accessToken): View
    {
        $tenant = Tenant::with(['property', 'room', 'invoices' => fn ($q) => $q->latest(), 'complaints' => fn ($q) => $q->latest()])
            ->where('access_token', $accessToken)
            ->firstOrFail();

        return view('public.portal', [
            'tenant' => $tenant,
        ]);
    }
}

