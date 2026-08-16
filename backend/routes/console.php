<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('social-media:fetch')->dailyAt('06:00');
Schedule::command('fanclub:expire-memberships')->dailyAt('00:10');
Schedule::command('fanclub:prune-pending-registrations')->dailyAt('03:00');
