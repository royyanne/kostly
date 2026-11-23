<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceDueReminderNotification;
use App\Notifications\InvoiceOverdueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders {--dry-run : Simulasi tanpa menyimpan perubahan}';

    /**
     * Alternative command aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = ['kostly:send-reminders'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat jatuh tempo H-3, H-1 dan tagihan lewat tempo ke tenant & owner';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $inThreeDays = $today->copy()->addDays(3);
        $inOneDay = $today->copy()->addDays(1);

        $this->info("Memulai pemeriksaan tagihan jatuh tempo & overdue ({$today->toDateString()})...");

        // 1. Pengingat H-3 dan H-1
        $this->sendDueReminders($inThreeDays, 3);
        $this->sendDueReminders($inOneDay, 1);

        // 2. Pemrosesan Tagihan Lewat Jatuh Tempo (Overdue)
        $this->processOverdueInvoices($today);

        $this->info('Selesai memproses pengingat tagihan dan notifikasi.');

        return Command::SUCCESS;
    }

    /**
     * Kirim notifikasi pengingat jatuh tempo H-X ke tenant.
     */
    protected function sendDueReminders(Carbon $targetDate, int $daysRemaining): void
    {
        $targetDateStr = $targetDate->toDateString();

        $invoices = Invoice::with(['tenant.user', 'property'])
            ->where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', $targetDateStr)
            ->get();

        $this->line("Ditemukan {$invoices->count()} tagihan H-{$daysRemaining} ({$targetDateStr}).");

        foreach ($invoices as $invoice) {
            $tenantUser = $this->resolveTenantUser($invoice);

            if (! $tenantUser) {
                continue;
            }

            // Pengecekan idempotensi: Cek apakah notifikasi pengingat ini sudah dikirimkan hari ini
            if ($this->hasBeenNotifiedToday($tenantUser, InvoiceDueReminderNotification::class, $invoice->id)) {
                $this->comment("Invoice #{$invoice->invoice_code} sudah diingatkan hari ini ke {$tenantUser->name}. Dilewati.");

                continue;
            }

            $tenantUser->notify(new InvoiceDueReminderNotification($invoice, $daysRemaining));
            $this->info("Pengingat H-{$daysRemaining} terkirim ke {$tenantUser->name} untuk invoice #{$invoice->invoice_code}.");
        }
    }

    /**
     * Proses tagihan yang melewati jatuh tempo: update status menjadi overdue & kirim notifikasi ke tenant dan owner.
     */
    protected function processOverdueInvoices(Carbon $today): void
    {
        $overdueInvoices = Invoice::with(['tenant.user', 'property.user'])
            ->where(function ($q) {
                $q->where('status', InvoiceStatus::Unpaid)
                    ->orWhere('status', InvoiceStatus::Overdue);
            })
            ->whereDate('due_date', '<', $today->toDateString())
            ->get();

        $this->line("Ditemukan {$overdueInvoices->count()} tagihan yang melewati jatuh tempo.");

        foreach ($overdueInvoices as $invoice) {
            // Update status menjadi Overdue jika masih Unpaid
            if ($invoice->status === InvoiceStatus::Unpaid) {
                $invoice->update(['status' => InvoiceStatus::Overdue]);
            }

            // 1. Notifikasi ke Tenant
            $tenantUser = $this->resolveTenantUser($invoice);
            if ($tenantUser) {
                if (! $this->hasBeenNotifiedToday($tenantUser, InvoiceOverdueNotification::class, $invoice->id)) {
                    $tenantUser->notify(new InvoiceOverdueNotification($invoice, 'tenant'));
                    $this->warn("Peringatan overdue terkirim ke tenant: {$tenantUser->name} (#{$invoice->invoice_code}).");
                }
            }

            // 2. Notifikasi ke Owner Properti
            $owner = $invoice->property?->user;
            if ($owner) {
                if (! $this->hasBeenNotifiedToday($owner, InvoiceOverdueNotification::class, $invoice->id)) {
                    $owner->notify(new InvoiceOverdueNotification($invoice, 'owner'));
                    $this->warn("Pemberitahuan tunggakan terkirim ke owner: {$owner->name} (#{$invoice->invoice_code}).");
                }
            }
        }
    }

    /**
     * Resolve User model untuk penyewa (dari relasi user_id atau pencocokan nomor telepon).
     */
    protected function resolveTenantUser(Invoice $invoice): ?User
    {
        if ($invoice->tenant?->user) {
            return $invoice->tenant->user;
        }

        if ($invoice->tenant?->user_id) {
            return User::find($invoice->tenant->user_id);
        }

        if ($invoice->tenant?->phone) {
            return User::where('phone', $invoice->tenant->phone)->first();
        }

        return null;
    }

    /**
     * Cek apakah notifikasi tipe tertentu untuk invoice terkait sudah dikirim ke user hari ini (Idempotency).
     */
    protected function hasBeenNotifiedToday(User $user, string $notificationClass, int $invoiceId): bool
    {
        return DB::table('notifications')
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->where('type', $notificationClass)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->where('data', 'like', '%"invoice_id":'.$invoiceId.'%')
            ->exists();
    }
}
