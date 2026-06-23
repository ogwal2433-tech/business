<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('business:build-snapshots --months=1')->monthly()->onOneServer();
Schedule::command('subscriptions:check-expired')->dailyAt('02:00')->onOneServer();
