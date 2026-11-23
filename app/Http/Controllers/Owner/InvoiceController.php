<?php

namespace App\Http\Controllers\Owner;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar dan status tagihan bulanan penghuni.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->query('property_id');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $baseInvoicesQuery = $user->invoices()->with(['tenant.room', 'property']);

        if ($selectedPropertyId) {
            $baseInvoicesQuery->where('invoices.property_id', $selectedPropertyId);
        }

        // Hitung total dan statistik status
        $totalCount = (clone $baseInvoicesQuery)->count();
        $unpaidCount = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Unpaid)->count();
        $unpaidSum = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Unpaid)->sum('total_amount');
        $pendingVerificationCount = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::PendingVerification)->count();
        $pendingVerificationSum = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::PendingVerification)->sum('total_amount');
        $paidCount = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Paid)->count();
        $paidSum = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Paid)->sum('total_amount');
        $overdueCount = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Overdue)->count();
        $overdueSum = (clone $baseInvoicesQuery)->where('status', InvoiceStatus::Overdue)->sum('total_amount');

        $invoicesQuery = clone $baseInvoicesQuery;

        if ($statusFilter && in_array($statusFilter, array_column(InvoiceStatus::cases(), 'value'), true)) {
            $invoicesQuery->where('status', $statusFilter);
        }

        if ($search) {
            $invoicesQuery->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                    ->orWhereHas('tenant', fn ($tq) => $tq->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $invoicesQuery->latest('due_date')->get();

        // Penghuni aktif untuk form buat tagihan baru
        $activeTenants = $user->tenants()
            ->with(['room', 'property'])
            ->where('is_active', true)
            ->get();

        return view('owner.invoices.index', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'invoices' => $invoices,
            'activeTenants' => $activeTenants,
            'totalCount' => $totalCount,
            'unpaidCount' => $unpaidCount,
            'unpaidSum' => $unpaidSum,
            'pendingVerificationCount' => $pendingVerificationCount,
            'pendingVerificationSum' => $pendingVerificationSum,
            'paidCount' => $paidCount,
            'paidSum' => $paidSum,
            'overdueCount' => $overdueCount,
            'overdueSum' => $overdueSum,
        ]);
    }

    /**
     * Buat tagihan sewa manual.
     */
    public function store(Request $request, InvoiceService $invoiceService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $userTenantIds = $user->tenants()->pluck('tenants.id')->toArray();

        $validated = $request->validate([
            'tenant_id' => ['required', Rule::in($userTenantIds)],
            'amount' => ['required', 'numeric', 'min:0'],
            'additional_fee' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
        ], [
            'tenant_id.required' => 'Pilih penghuni yang ditagih.',
            'amount.required' => 'Biaya pokok sewa wajib diisi.',
            'due_date.required' => 'Tanggal jatuh tempo wajib ditentukan.',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $additionalFee = (float) ($validated['additional_fee'] ?? 0);
        $totalAmount = (float) $validated['amount'] + $additionalFee;
        $code = $invoiceService->generateInvoiceCode();

        Invoice::create([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'invoice_code' => $code,
            'amount' => $validated['amount'],
            'additional_fee' => $additionalFee,
            'total_amount' => $totalAmount,
            'due_date' => $validated['due_date'],
            'status' => InvoiceStatus::Unpaid,
            'payment_url' => url('/pay/'.$code),
        ]);

        return redirect()->route('owner.invoices.index')->with('success', "Tagihan baru dengan kode #{$code} berhasil dibuat.");
    }

    /**
     * Generate tagihan bulanan otomatis untuk seluruh penghuni aktif.
     */
    public function generateMonthly(Request $request, InvoiceService $invoiceService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $totalGenerated = 0;

        foreach ($properties as $property) {
            $created = $invoiceService->generateMonthlyInvoicesForProperty($property);
            $totalGenerated += $created->count();
        }

        if ($totalGenerated === 0) {
            return redirect()->route('owner.invoices.index')->with('success', 'Semua tagihan bulan ini untuk seluruh penghuni aktif sudah dibuat sebelumnya.');
        }

        return redirect()->route('owner.invoices.index')->with('success', "Berhasil men-generate {$totalGenerated} tagihan bulanan baru secara otomatis!");
    }

    /**
     * Tandai tagihan sebagai lunas secara manual.
     */
    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($invoice->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'payment_method' => $validated['payment_method'],
            'paid_at' => now(),
        ]);

        return redirect()->route('owner.invoices.index')->with('success', "Tagihan #{$invoice->invoice_code} berhasil ditandai Lunas via {$validated['payment_method']}!");
    }

    /**
     * Verifikasi bukti pembayaran dari penyewa dan tandai tagihan lunas.
     */
    public function verifyPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($invoice->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
            'rejected_reason' => null,
        ]);

        return redirect()->route('owner.invoices.index')
            ->with('success', "Pembayaran tagihan #{$invoice->invoice_code} berhasil diverifikasi Lunas!");
    }

    /**
     * Tolak bukti transfer pembayaran yang diunggah penyewa.
     */
    public function rejectPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($invoice->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'rejected_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejected_reason.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
        ]);

        $invoice->update([
            'status' => InvoiceStatus::Unpaid,
            'rejected_reason' => $validated['rejected_reason'],
        ]);

        return redirect()->route('owner.invoices.index')
            ->with('info', "Bukti transfer tagihan #{$invoice->invoice_code} telah ditolak. Penyewa dapat mengunggah bukti baru.");
    }

    /**
     * Kirim notifikasi pengingat via WhatsApp.
     */
    public function sendReminder(Request $request, Invoice $invoice, WhatsAppService $whatsAppService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($invoice->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $whatsAppService->sendInvoiceReminder($invoice, 'upcoming');

        $tenantName = $invoice->tenant->name;

        return redirect()->route('owner.invoices.index')->with('success', "Pesan pengingat WhatsApp berhasil dikirimkan ke {$tenantName}!");
    }

    /**
     * Hapus data tagihan.
     */
    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($invoice->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $code = $invoice->invoice_code;
        $invoice->delete();

        return redirect()->route('owner.invoices.index')->with('success', "Tagihan #{$code} berhasil dihapus.");
    }
}
