<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Book reader page images — 5 requests per 10 seconds per student
        // (or per IP for the rare unauthenticated edge case). Deliberately
        // tight: a real reader flips pages far slower than this, so this
        // mainly exists to make bulk-scraping a book impractical.
        RateLimiter::for('reader-pages', function ($request) {
            return Limit::perSeconds(10, 5)->by($request->user()?->id ?: $request->ip());
        });

        // REST API — general vs AI-generation endpoints get different budgets
        // (AI calls are expensive and slow; general reads are cheap).
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('api-ai', function ($request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Registers POST /broadcasting/auth (web-guard session auth, since
        // students log in via session not Sanctum tokens) and loads the
        // private-channel authorization rules from routes/channels.php.
        Broadcast::routes(['middleware' => ['web', 'auth:web']]);
        require base_path('routes/channels.php');
    }
}
