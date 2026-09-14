<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('data-induk.scheduled')) {
    // withoutOverlapping supplies a durable application lock. A shared cache
    // can later add onOneServer when multiple scheduler hosts are introduced.
    Schedule::command('data-induk:sync-changes')->everyFifteenMinutes()->withoutOverlapping();
    Schedule::command('data-induk:sync-references')->hourly()->withoutOverlapping();
}

// Push kajian harus diperiksa tiap menit agar pengingat 30 menit sebelum mulai,
// setelah selesai, dan pukul 21.00 WIB tetap tepat waktu.
Schedule::command('kajian:send-notifications')
    ->everyMinute()
    ->withoutOverlapping();
