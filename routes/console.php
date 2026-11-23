<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler Otomasi Tagihan Bulanan Setiap Awal Bulan
Schedule::command('invoices:generate-monthly')->monthlyOn(1, '00:01');
Schedule::command('invoices:send-reminders')->dailyAt('08:00');
Schedule::command('subscriptions:check-expired')->dailyAt('00:05');
