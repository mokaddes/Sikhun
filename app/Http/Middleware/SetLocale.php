<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reads the student's preferred language from the session (set via
 * POST /language/{locale}) and applies it for this request. Defaults
 * to the app's configured locale (Bengali) for first-time visitors.
 */
class SetLocale
{
    private const SUPPORTED = ['bn', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (! $locale || ! in_array($locale, self::SUPPORTED, true)) {
            $configured = config('app.locale', 'bn');
            $locale = in_array($configured, self::SUPPORTED, true) ? $configured : 'bn';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
