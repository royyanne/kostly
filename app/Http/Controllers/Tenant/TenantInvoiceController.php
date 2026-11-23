<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantInvoiceController extends Controller
{
    /**
     * Tampilkan riwayat tagihan sewa kamar milik penyewa.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $tenant = Tenant::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone);
            })
            ->where('is_active', true)
            ->with(['property.user', 'room', 'invoices' => fn ($q) => $q->latest('due_date')])
            ->first();

        $invoices = $tenant?->invoices ?? collect();

        return view('tenant.invoices', [
            'user' => $user,
            'tenant' => $tenant,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Unggah bukti transfer pembayaran tagihan oleh penyewa.
     */
    public function uploadProof(Request $request, Invoice $invoice): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tenant = Tenant::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone);
            })
            ->where('is_active', true)
            ->first();

        if (! $tenant || $invoice->tenant_id !== $tenant->id) {
            abort(403, 'Akses ditolak. Tagihan ini bukan milik Anda.');
        }

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ], [
            'payment_proof.required' => 'Foto bukti transfer pembayaran wajib diunggah.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar (JPG, PNG, atau WEBP).',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 2MB.',
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $invoice->update([
            'payment_proof' => $path,
            'payment_method' => $validated['payment_method'] ?? 'Transfer Bank',
            'status' => InvoiceStatus::PendingVerification,
            'rejected_reason' => null,
        ]);

        return redirect()->route('tenant.invoices.index')
            ->with('success', "Bukti pembayaran untuk tagihan #{$invoice->invoice_code} berhasil diunggah! Menunggu verifikasi pemilik kost.");
    }
}
