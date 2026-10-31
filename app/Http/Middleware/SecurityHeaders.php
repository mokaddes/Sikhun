<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security headers for every web response.
 *
 * Deliberately does NOT set a Content-Security-Policy: the app relies on
 * inline bootstrap scripts (theme flash prevention, GTM, Meta Pixel), so a
 * strict CSP would break analytics and dark mode. A nonce-based CSP is a
 * separate, planned piece of work.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Don't advertise the PHP version — it is free reconnaissance for
        // an attacker matching a known CVE to this host.
        $response->headers->remove('X-Powered-By');

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        // HSTS only over HTTPS, and only in production — sending it from
        // localhost would pin http://localhost to https in the developer's
        // browser for months.
        if ($request->isSecure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
