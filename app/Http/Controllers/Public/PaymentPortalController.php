<?php

namespace App\Http\Controllers\Public;

use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentPortalController extends Controller
{
    /**
     * Tampilkan halaman publik pembayaran tagihan berdasarkan kode tagihan.
     */
    public function showPayment(string $invoiceCode): View
    {
        $invoice = Invoice::with(['property.user', 'tenant.room'])
            ->where('invoice_code', $invoiceCode)
            ->firstOrFail();

        return view('public.pay', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Simulasi atau konfirmasi pembayaran tagihan oleh penyewa/pembayar.
     */
    public function confirmPayment(Request $request, string $invoiceCode): RedirectResponse
    {
        $invoice = Invoice::where('invoice_code', $invoiceCode)->firstOrFail();

        if ($invoice->status === InvoiceStatus::Paid) {
            return redirect()->route('public.pay', $invoiceCode)->with('info', 'Tagihan ini sudah berstatus lunas sebelumnya.');
        }

        $paymentMethod = $request->input('payment_method', 'QRIS Dinamis');

        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        return redirect()->route('public.pay', $invoiceCode)->with('success', 'Pembayaran sebesar Rp '.number_format((float) $invoice->total_amount, 0, ',', '.').' berhasil diverifikasi!');
    }

    /**
     * Tampilkan portal penghuni mandiri tanpa login melalui access_token unik.
     */
    public function showPortal(string $accessToken): View|Response
    {
        $tenant = Tenant::with([
            'property.user',
            'room',
            'invoices' => fn ($q) => $q->latest('due_date'),
            'complaints' => fn ($q) => $q->latest(),
        ])
            ->where('access_token', $accessToken)
            ->first();

        // Validasi: jika token tidak ditemukan atau penghuni berstatus nonaktif (checkout)
        if (! $tenant || ! $tenant->is_active) {
            return response()->view('public.portal-invalid', [
                'isInactive' => $tenant && ! $tenant->is_active,
                'tenant' => $tenant,
            ], 404);
        }

        // Tagihan aktif yang belum lunas
        $activeInvoice = $tenant->invoices
            ->first(fn ($inv) => $inv->status !== InvoiceStatus::Paid);

        return view('public.portal', [
            'tenant' => $tenant,
            'activeInvoice' => $activeInvoice,
        ]);
    }

    /**
     * Kirim laporan komplain kerusakan fasilitas langsung dari portal anak kost.
     */
    public function storeComplaint(Request $request, string $accessToken): RedirectResponse
    {
        $tenant = Tenant::where('access_token', $accessToken)
            ->where('is_active', true)
            ->first();

        if (! $tenant) {
            abort(404, 'Penghuni tidak ditemukan atau status sewa sudah tidak aktif.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'title.required' => 'Judul masalah atau kerusakan fasilitas wajib diisi.',
            'description.required' => 'Jelaskan rincian kendala yang terjadi di kamar atau area kost.',
            'photo.image' => 'File bukti harus berupa gambar foto.',
            'photo.mimes' => 'Format foto yang didukung: JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran file foto maksimal adalah 5 MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints', 'public');
        }

        Complaint::create([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'status' => ComplaintStatus::Pending,
        ]);

        return redirect()->route('public.portal', $accessToken)
            ->with('success', 'Laporan kerusakan berhasil terkirim ke pemilik kost. Kami akan segera menindaklanjuti!');
    }

    /**
     * Tampilkan struk / kuitansi resmi pembayaran tagihan lunas untuk dicetak / PDF.
     */
    public function showReceipt(string $accessToken, string $invoiceCode): View
    {
        $tenant = Tenant::where('access_token', $accessToken)
            ->where('is_active', true)
            ->firstOrFail();

        $invoice = Invoice::with(['property.user', 'tenant.room'])
            ->where('property_id', $tenant->property_id)
            ->where('tenant_id', $tenant->id)
            ->where('invoice_code', $invoiceCode)
            ->firstOrFail();

        return view('public.receipt', [
            'tenant' => $tenant,
            'invoice' => $invoice,
        ]);
    }
}
