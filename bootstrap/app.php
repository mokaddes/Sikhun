<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin routes run through the 'web' middleware group (sessions, CSRF)
            // but live in their own file to keep guards cleanly separated.
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CRITICAL for production behind any reverse proxy (Nginx, Cloudflare,
        // a load balancer, cPanel's proxy, etc). Without this, Laravel can't
        // reliably tell if the original request was HTTPS, which causes
        // exactly this symptom: session/CSRF cookies silently fail to
        // persist on some requests, making a link look like it "reloads"
        // once before working — because the browser had to re-establish a
        // valid session on the retry. If you are NOT behind any proxy
        // (e.g. plain `php artisan serve` or a bare VPS with no Nginx in
        // front of PHP-FPM), remove this block; trusting proxies you don't
        // have is a security downgrade, not just a no-op.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'student.active' => \App\Http\Middleware\EnsureStudentIsActive::class,
            'admin.role' => \App\Http\Middleware\EnsureAdminHasRole::class,
            'ai.access' => \App\Http\Middleware\EnsureHasAiAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Renders resources/js/Pages/Error/Show.vue for standard HTTP error
        // statuses instead of Laravel's default whitescreen — but only
        // outside local/testing, where you want the real stack trace.
        // Doesn't apply to /api/* requests, which always get JSON errors.
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return $response;
            }

            $status = $response->getStatusCode();

            if (in_array($status, [403, 404, 419, 429, 500, 503], true)
                && ! app()->hasDebugModeEnabled()) {
                return \Inertia\Inertia::render('Error/Show', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return $response;
        });
    })->create();
