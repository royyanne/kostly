<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kostly:send-reminders {--dry-run : Simulasi tanpa mengirimkan pesan WhatsApp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pemeriksaan harian tagihan kost untuk mengirim pengingat WhatsApp H-3 dan Hari-H';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $today = now()->toDateString();
        $inThreeDays = now()->addDays(3)->toDateString();

        $this->info("Memulai pengecekan tagihan jatuh tempo (Hari ini: {$today}, H-3: {$inThreeDays})...");

        // 1. Tagihan H-3
        $upcomingInvoices = Invoice::with(['tenant.room', 'property'])
            ->where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', $inThreeDays)
            ->get();

        $this->info("Ditemukan {$upcomingInvoices->count()} tagihan H-3.");

        foreach ($upcomingInvoices as $invoice) {
            $this->line("Mengirim pengingat H-3 ke: {$invoice->tenant->name} ({$invoice->invoice_code})");
            if (! $isDryRun) {
                $whatsAppService->sendInvoiceReminder($invoice, 'upcoming');
            }
        }

        // 2. Tagihan Hari-H
        $dueTodayInvoices = Invoice::with(['tenant.room', 'property'])
            ->where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', $today)
            ->get();

        $this->info("Ditemukan {$dueTodayInvoices->count()} tagihan Hari-H.");

        foreach ($dueTodayInvoices as $invoice) {
            $this->line("Mengirim pengingat Hari-H ke: {$invoice->tenant->name} ({$invoice->invoice_code})");
            if (! $isDryRun) {
                $whatsAppService->sendInvoiceReminder($invoice, 'due_today');
            }
        }

        // 3. Update status tagihan yang sudah lewat jatuh tempo menjadi Overdue
        $overdueUpdated = Invoice::where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', '<', $today)
            ->update(['status' => InvoiceStatus::Overdue]);

        if ($overdueUpdated > 0) {
            $this->warn("Telah memperbarui {$overdueUpdated} tagihan yang melewati jatuh tempo menjadi 'overdue'.");
        }

        $this->info('Selesai memproses pengingat tagihan.');

        return Command::SUCCESS;
    }
}

