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

// Remove abandoned chunked-upload staging folders every 30 minutes.
// Each folder is small while uploading, but left behind uploads waste disk space.
Schedule::call(function () {
    $storage = Storage::disk('private');
    $base = 'books/temp';
    if (! $storage->exists($base)) {
        return;
    }
    $cutoff = now()->subHour();
    foreach ($storage->directories($base) as $dir) {
        $meta = $storage->exists("{$dir}/meta.json")
            ? json_decode((string) $storage->get("{$dir}/meta.json"), true)
            : null;
        $updatedAt = $meta['updated_at'] ?? null;
        if (! $updatedAt || \Carbon\Carbon::parse($updatedAt)->lessThan($cutoff)) {
            $storage->deleteDirectory($dir);
        }
    }
})->everyThirtyMinutes();
