<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks students whose account has been deactivated by an admin,
 * even if their session is still technically valid.
 */
class EnsureStudentIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $student = $request->user('web');

        if ($student && $student->status === 'inactive') {
            auth('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        return $next($request);
    }
}
