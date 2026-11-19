<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect(string $provider, SocialAuthService $oauth): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        $state = Str::random(40);
        session(["oauth_state_{$provider}" => $state]);

        return redirect()->away($oauth->authorizationUrl($provider, $state));
    }

    public function callback(string $provider, Request $request, SocialAuthService $oauth): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        $expectedState = session()->pull("oauth_state_{$provider}");
        abort_unless($expectedState && hash_equals($expectedState, (string) $request->query('state')), 403, 'Invalid OAuth state.');

        $code = (string) $request->query('code');
        if ($code === '') {
            return redirect()->route('login')->with('error', 'Social login was cancelled.');
        }

        try {
            $profile = $oauth->fetchUserFromCode($provider, $code);
            $student = $oauth->findOrCreateStudent($profile, $request);
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }

        if ($student->status === 'inactive') {
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        Auth::guard('web')->login($student, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /** Mobile API: exchange provider access token for a Sanctum Bearer token. */
    public function apiExchange(string $provider, Request $request, SocialAuthService $oauth): JsonResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        if (! $oauth->isEnabled($provider)) {
            return response()->json(['success' => false, 'message' => ucfirst($provider).' login is currently disabled.'], 422);
        }

        $validated = $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        try {
            $profile = $oauth->fetchUserFromToken($provider, $validated['access_token']);
            $student = $oauth->findOrCreateStudent($profile, $request);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        if ($student->status === 'inactive') {
            return response()->json(['success' => false, 'message' => 'Your account has been deactivated.'], 403);
        }

        $token = $student->createToken('sikhun_mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'token' => $token,
            ],
            'message' => 'Logged in successfully',
        ]);
    }
}
