<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // MAKE SURE THIS IS HERE

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// NEW: Run your equipment restocking script every single minute
Schedule::command('bookings:complete')->everyMinute();