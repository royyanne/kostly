<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly {--month= : Periode bulan target tagihan (format: YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan sewa bulanan otomatis bagi seluruh penghuni kost yang berstatus aktif';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService): int
    {
        $monthOption = $this->option('month');
        $targetDate = $monthOption ? Carbon::parse($monthOption.'-01') : now();

        $monthName = $targetDate->translatedFormat('F Y');
        $this->info("Memulai pembuatan tagihan bulanan periode: {$monthName}...");

        $properties = Property::with(['tenants' => fn ($q) => $q->where('is_active', true)])->get();

        $totalGenerated = 0;
        $propertiesProcessed = 0;

        foreach ($properties as $property) {
            $created = $invoiceService->generateMonthlyInvoicesForProperty($property, $targetDate);
            $count = $created->count();

            if ($count > 0) {
                $this->line("Properti [{$property->name}]: Berhasil membuat {$count} tagihan baru.");
                $totalGenerated += $count;
            }

            $propertiesProcessed++;
        }

        if ($totalGenerated === 0) {
            $this->comment('Tidak ada tagihan baru yang perlu dibuat (semua penghuni aktif sudah memiliki tagihan untuk periode ini).');
        } else {
            $this->info("Selesai! Berhasil membuat {$totalGenerated} tagihan bulanan baru dari {$propertiesProcessed} properti.");
        }

        return Command::SUCCESS;
    }
}
