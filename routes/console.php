<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:generate-ai')->dailyAt('07:00');
Schedule::command('notifications:send-scheduled')->everyFiveMinutes();
Schedule::command('leaderboard:reset-weekly')->weekly()->mondays()->at('00:00');
Schedule::command('leaderboard:reset-monthly')->monthlyOn(1, '00:00');
Schedule::command('sitemap:generate')->daily();
Schedule::command('subscriptions:expiry-check')->dailyAt('09:00');
Schedule::command('subscriptions:expire-lapsed')->dailyAt('00:30');
