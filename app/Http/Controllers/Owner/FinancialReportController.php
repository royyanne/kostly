<?php

namespace App\Http\Controllers\Owner;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller
{
    /**
     * Tampilkan halaman rekap laporan keuangan bulanan.
     */
    public function index(Request $request): View
    {
        $reportData = $this->getMonthlyFinancialData($request);

        return view('owner.reports.index', $reportData);
    }

    /**
     * Unduh laporan keuangan bulanan dalam format CSV/Excel dengan UTF-8 BOM.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $reportData = $this->getMonthlyFinancialData($request);

        $monthName = Carbon::create($reportData['year'], $reportData['month'], 1)->translatedFormat('F');
        $propertyLabel = $reportData['selectedProperty'] ? str_replace(' ', '-', strtolower($reportData['selectedProperty']->name)) : 'semua-properti';
        $fileName = "laporan-keuangan-{$propertyLabel}-{$monthName}-{$reportData['year']}.csv";

        return response()->streamDownload(function () use ($reportData, $monthName) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel agar aksen & format karakter tidak rusak
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Metadata Header Dokumen
            fputcsv($handle, ['LAPORAN KEUANGAN BULANAN - KOSTLY']);
            fputcsv($handle, ['Properti', $reportData['selectedProperty'] ? $reportData['selectedProperty']->name : 'Semua Properti Kost']);
            fputcsv($handle, ['Periode', "{$monthName} {$reportData['year']}"]);
            fputcsv($handle, ['Tanggal Ekspor', now()->translatedFormat('d F Y, H:i').' WIB']);
            fputcsv($handle, []);

            // Ringkasan Keuangan
            fputcsv($handle, ['RINGKASAN KEUANGAN']);
            fputcsv($handle, ['Total Pemasukan (Lunas)', 'Rp '.number_format($reportData['totalIncome'], 0, ',', '.')]);
            fputcsv($handle, ['Total Pengeluaran Operasional', 'Rp '.number_format($reportData['totalExpense'], 0, ',', '.')]);
            fputcsv($handle, ['Laba Bersih (Net Profit)', 'Rp '.number_format($reportData['netProfit'], 0, ',', '.')]);
            fputcsv($handle, []);

            // Baris Judul Kolom Tabel
            fputcsv($handle, [
                'Tanggal',
                'Jenis Transaksi',
                'Uraian Transaksi',
                'Kategori',
                'Properti',
                'Metode Pembayaran',
                'Pemasukan (IDR)',
                'Pengeluaran (IDR)',
                'Keterangan',
            ]);

            // Data Baris Transaksi
            foreach ($reportData['transactions'] as $tx) {
                fputcsv($handle, [
                    $tx['date']->format('d/m/Y'),
                    $tx['type_label'],
                    $tx['title'],
                    $tx['category'],
                    $tx['property_name'],
                    $tx['payment_method'],
                    $tx['income'] > 0 ? $tx['income'] : '0',
                    $tx['expense'] > 0 ? $tx['expense'] : '0',
                    $tx['notes'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                $reportData['totalIncome'],
                $reportData['totalExpense'],
                'Laba Bersih: '.$reportData['netProfit'],
            ]);

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Tampilkan halaman struk/laporan keuangan siap cetak (Print PDF).
     */
    public function printReport(Request $request): View
    {
        $reportData = $this->getMonthlyFinancialData($request);

        return view('owner.reports.print', $reportData);
    }

    /**
     * Ambil dan hitung seluruh data transaksi keuangan bulanan.
     *
     * @return array{
     *     user: User,
     *     properties: Collection,
     *     selectedPropertyId: ?int,
     *     selectedProperty: ?Property,
     *     month: int,
     *     year: int,
     *     monthName: string,
     *     totalIncome: float,
     *     totalExpense: float,
     *     netProfit: float,
     *     transactions: Collection
     * }
     */
    private function getMonthlyFinancialData(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->filled('property_id') ? (int) $request->input('property_id') : null;
        $selectedProperty = $selectedPropertyId ? $properties->firstWhere('id', $selectedPropertyId) : null;

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // 1. Ambil Tagihan Lunas (Pemasukan)
        $invoicesQuery = $user->invoices()
            ->with(['property', 'tenant.room'])
            ->where('status', InvoiceStatus::Paid)
            ->where(function ($q) use ($year, $month) {
                $q->where(function ($sub) use ($year, $month) {
                    $sub->whereNotNull('paid_at')
                        ->whereYear('paid_at', $year)
                        ->whereMonth('paid_at', $month);
                })->orWhere(function ($sub) use ($year, $month) {
                    $sub->whereNull('paid_at')
                        ->whereYear('due_date', $year)
                        ->whereMonth('due_date', $month);
                });
            });

        if ($selectedPropertyId) {
            $invoicesQuery->where('invoices.property_id', $selectedPropertyId);
        }

        $paidInvoices = $invoicesQuery->get();

        // 2. Ambil Pengeluaran Operasional
        $expensesQuery = $user->expenses()
            ->with('property')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);

        if ($selectedPropertyId) {
            $expensesQuery->where('expenses.property_id', $selectedPropertyId);
        }

        $expenses = $expensesQuery->get();

        // 3. Hitung Agregasi Keuangan
        $totalIncome = (float) $paidInvoices->sum('total_amount');
        $totalExpense = (float) $expenses->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        // 4. Gabungkan dan urutkan transaksi secara kronologis
        $transactions = collect();

        foreach ($paidInvoices as $inv) {
            $date = $inv->paid_at ? Carbon::parse($inv->paid_at) : Carbon::parse($inv->due_date);
            $roomNumber = $inv->tenant?->room?->room_number ? 'Kamar '.$inv->tenant->room->room_number : 'Kamar -';
            $tenantName = $inv->tenant?->name ?? 'Penghuni';

            $transactions->push([
                'date' => $date,
                'type' => 'income',
                'type_label' => 'Pemasukan',
                'title' => "Tagihan #{$inv->invoice_code} ({$tenantName})",
                'category' => "Sewa {$roomNumber}",
                'property_name' => $inv->property->name ?? '-',
                'payment_method' => $inv->payment_method ?? 'Metode Digital',
                'income' => (float) $inv->total_amount,
                'expense' => 0.0,
                'notes' => 'Invoice #'.$inv->invoice_code,
            ]);
        }

        foreach ($expenses as $exp) {
            $date = Carbon::parse($exp->expense_date);

            $transactions->push([
                'date' => $date,
                'type' => 'expense',
                'type_label' => 'Pengeluaran',
                'title' => $exp->title,
                'category' => $exp->category,
                'property_name' => $exp->property->name ?? '-',
                'payment_method' => 'Kas Operasional',
                'income' => 0.0,
                'expense' => (float) $exp->amount,
                'notes' => $exp->notes ?? '-',
            ]);
        }

        // Urutkan transaksi berdasarkan tanggal descending
        $transactions = $transactions->sortByDesc(fn ($item) => $item['date']->timestamp)->values();

        $monthName = Carbon::create($year, $month, 1)->translatedFormat('F');

        return [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'selectedProperty' => $selectedProperty,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'transactions' => $transactions,
        ];
    }
}
