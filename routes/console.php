<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Schedule::command('test:cron')->everyFiveMinutes();

Schedule::command('invoice:status-update')->daily();
Schedule::command('order:release-status-check')->daily();
Schedule::command('otp:expire')->everyMinute();
Schedule::command('project-status:generate-weekly')->weekly()->sundays()->at('02:00');