<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Buat kode tagihan unik dengan format INV-YYYYMM-[RANDOM].
     */
    public function generateInvoiceCode(): string
    {
        $prefix = 'INV-'.now()->format('Ym').'-';

        do {
            $code = $prefix.strtoupper(Str::random(5));
        } while (Invoice::where('invoice_code', $code)->exists());

        return $code;
    }

    /**
     * Buat tagihan sewa baru untuk seorang penghuni kost.
     */
    public function createInvoiceForTenant(
        Tenant $tenant,
        ?Carbon $dueDate = null,
        float $additionalFee = 0.0,
        ?float $customAmount = null
    ): Invoice {
        $amount = $customAmount ?? (float) ($tenant->room?->price ?? 0);
        $totalAmount = $amount + $additionalFee;

        if (! $dueDate) {
            $targetDay = min((int) $tenant->due_day, 28);
            $dueDate = now()->day > $targetDay
                ? now()->addMonth()->setDay($targetDay)
                : now()->setDay($targetDay);
        }

        return Invoice::create([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'invoice_code' => $this->generateInvoiceCode(),
            'amount' => $amount,
            'additional_fee' => $additionalFee,
            'total_amount' => $totalAmount,
            'due_date' => $dueDate->toDateString(),
            'status' => InvoiceStatus::Unpaid,
            'payment_url' => url('/pay/'.$this->generateInvoiceCode()),
        ]);
    }

    /**
     * Generate tagihan bulanan untuk seluruh penghuni aktif dalam suatu properti.
     *
     * @return Collection<int, Invoice>
     */
    public function generateMonthlyInvoicesForProperty(Property $property, ?Carbon $targetMonth = null): Collection
    {
        $month = $targetMonth ?? now();
        $createdInvoices = collect();

        $activeTenants = $property->tenants()
            ->with('room')
            ->where('is_active', true)
            ->get();

        foreach ($activeTenants as $tenant) {
            $dueDay = min((int) $tenant->due_day, 28);
            $dueDate = $month->copy()->setDay($dueDay);

            // Cek apakah tagihan di bulan/tahun dan penghuni ini sudah ada
            $alreadyExists = Invoice::where('tenant_id', $tenant->id)
                ->whereYear('due_date', $dueDate->year)
                ->whereMonth('due_date', $dueDate->month)
                ->exists();

            if (! $alreadyExists) {
                $invoice = $this->createInvoiceForTenant($tenant, $dueDate);
                $createdInvoices->push($invoice);
            }
        }

        return $createdInvoices;
    }

    /**
     * Tandai tagihan sebagai lunas.
     */
    public function markAsPaid(Invoice $invoice, string $method = 'Transfer Bank'): Invoice
    {
        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'payment_method' => $method,
            'paid_at' => now(),
        ]);

        return $invoice->refresh();
    }
}
