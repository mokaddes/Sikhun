<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

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
Schedule::command('blog:scrape')->dailyAt('03:00');

// Invoicing: renewal invoices a week ahead, overdue sweep, then reminders.
Schedule::command('invoices:generate-recurring')->dailyAt('06:00');
Schedule::command('invoices:mark-overdue')->dailyAt('06:15');
Schedule::command('invoices:send-reminders')->dailyAt('06:30');
Schedule::command('orders:fail-stale-zinipay --hours=24')->hourlyAt(10);

// Remove abandoned chunked-upload staging folders every 30 minutes.
// Each folder is small while uploading, but left behind uploads waste disk
// space — and an abandoned lesson video can be gigabytes.
Schedule::call(function () {
    $storage = Storage::disk('private');
    $cutoff = now()->subHour();

    foreach (['books/temp', 'courses/temp'] as $base) {
        if (! $storage->exists($base)) {
            continue;
        }

        foreach ($storage->directories($base) as $dir) {
            $meta = $storage->exists("{$dir}/meta.json")
                ? json_decode((string) $storage->get("{$dir}/meta.json"), true)
                : null;
            $updatedAt = $meta['updated_at'] ?? null;
            if (! $updatedAt || \Carbon\Carbon::parse($updatedAt)->lessThan($cutoff)) {
                $storage->deleteDirectory($dir);
            }
        }
    }
})->everyThirtyMinutes();
