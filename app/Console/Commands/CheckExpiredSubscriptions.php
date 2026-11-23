<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan turunkan status user Pro yang masa aktif langganannya telah kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $this->info("Memulai pemeriksaan langganan kedaluwarsa per {$now->toDateTimeString()}...");

        // 1. Cari user yang memiliki paket Pro dengan plan_expires_at <= now()
        $expiredUsers = User::query()
            ->where('subscription_plan', SubscriptionPlan::Pro)
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<=', $now)
            ->get();

        $count = $expiredUsers->count();
        $this->line("Ditemukan {$count} akun Pro yang telah kedaluwarsa.");

        foreach ($expiredUsers as $user) {
            // Ubah status subscription aktif terakhir menjadi expired
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where(function ($q) use ($now) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '<=', $now);
                })
                ->update(['status' => 'expired']);

            // Turunkan paket user kembali ke Free
            $user->update([
                'subscription_plan' => SubscriptionPlan::Free,
            ]);

            $this->warn("User [{$user->name}] ({$user->email}) telah diturunkan ke paket Free.");
        }

        // 2. Tandai juga setiap record Subscription active yang ends_at <= now()
        Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->update(['status' => 'expired']);

        $this->info('Pemeriksaan langganan kedaluwarsa selesai.');

        return Command::SUCCESS;
    }
}
