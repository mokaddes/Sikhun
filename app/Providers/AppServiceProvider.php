<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Ensure file cache directories/files are created with 0777 permissions
        // so CLI commands run as root never block www-data with Permission denied.
        config(['cache.stores.file.permission' => 0777]);
    }

    public function boot(): void
    {
        // Catch N+1 queries and silently-discarded attributes while
        // developing. Off by default because enabling it on an old codebase
        // throws on the first missing eager load; turn it on per-machine
        // with APP_STRICT_MODELS=true once the known offenders are fixed.
        if (config('performance.strict_models') && ! app()->isProduction()) {
            Model::preventLazyLoading();
            Model::preventSilentlyDiscardingAttributes();
        }

        // Book reader page images — 180 requests per minute per student
        // allows smooth flipping, prefetching and zooming without 429 errors.
        RateLimiter::for('reader-pages', function ($request) {
            return Limit::perMinute(180)->by($request->user()?->id ?: $request->ip());
        });

        // Login / register / password reset. Keyed by email+IP so one
        // attacker cannot lock out every account, and a single IP cannot
        // spray passwords across many accounts.
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by(
                mb_strtolower((string) $request->input('email')).'|'.$request->ip()
            );
        });
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Student-facing AI generation on the web guard (the API has its
        // own 'api-ai' bucket). Each AI action costs real money, so this is
        // intentionally tighter than the general web traffic.
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(12)->by($request->user()?->id ?: $request->ip());
        });

        // The public contact form is deliberately tight, but chat messages and
        // ticket handoff use separate buckets so normal conversation does not
        // consume the quota needed to ask a human for help.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
        RateLimiter::for('support-message', function (Request $request) {
            return Limit::perMinute(12)->by($request->user('web')?->id ?: $request->ip());
        });
        RateLimiter::for('support-handoff', function (Request $request) {
            return Limit::perMinute(5)->by($request->user('web')?->id ?: $request->ip());
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
