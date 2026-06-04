<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('payment:sync-deadlines')->dailyAt('00:05');
Schedule::command('notifications:event-reminders')->dailyAt('08:00');
Schedule::command('reminders:send-schedule')->hourly();
Schedule::call(fn () => \App\Models\Pesanan::expireOverdueBookings())->everyFifteenMinutes();
