<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialAuthService
{
    public function __construct(protected SiteSettingService $settings) {}

    public function isEnabled(string $provider): bool
    {
        return match ($provider) {
            'google' => filter_var($this->settings->get('oauth_google_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'facebook' => filter_var($this->settings->get('oauth_facebook_enabled', false), FILTER_VALIDATE_BOOLEAN),
            default => false,
        };
    }

    public function authorizationUrl(string $provider, string $state): string
    {
        if (! $this->isEnabled($provider)) {
            throw new \RuntimeException(ucfirst($provider).' login is currently disabled.');
        }

        if ($provider === 'google') {
            $clientId = trim((string) $this->settings->get('oauth_google_client_id', config('services.google.client_id')));
            abort_unless($clientId !== '', 422, 'Google OAuth Client ID is not configured.');

            return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => route('oauth.callback', 'google'),
                'response_type' => 'code',
                'scope' => 'openid email profile',
                'state' => $state,
                'prompt' => 'select_account',
            ]);
        }

        if ($provider === 'facebook') {
            $clientId = trim((string) $this->settings->get('oauth_facebook_client_id', config('services.facebook.client_id')));
            abort_unless($clientId !== '', 422, 'Facebook App ID is not configured.');

            return 'https://www.facebook.com/v19.0/dialog/oauth?'.http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => route('oauth.callback', 'facebook'),
                'scope' => 'email,public_profile',
                'state' => $state,
            ]);
        }

        abort(404);
    }

    public function fetchUserFromCode(string $provider, string $code): array
    {
        if ($provider === 'google') {
            $tokenResp = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => trim((string) $this->settings->get('oauth_google_client_id', config('services.google.client_id'))),
                'client_secret' => trim((string) $this->settings->get('oauth_google_client_secret', config('services.google.client_secret'))),
                'redirect_uri' => route('oauth.callback', 'google'),
                'grant_type' => 'authorization_code',
            ]);
            if (! $tokenResp->successful() || ! $tokenResp->json('access_token')) {
                throw new \RuntimeException('Failed to exchange Google authorization code.');
            }

            return $this->fetchUserFromToken('google', (string) $tokenResp->json('access_token'));
        }

        if ($provider === 'facebook') {
            $tokenResp = Http::timeout(10)->get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id' => trim((string) $this->settings->get('oauth_facebook_client_id', config('services.facebook.client_id'))),
                'client_secret' => trim((string) $this->settings->get('oauth_facebook_client_secret', config('services.facebook.client_secret'))),
                'redirect_uri' => route('oauth.callback', 'facebook'),
                'code' => $code,
            ]);
            if (! $tokenResp->successful() || ! $tokenResp->json('access_token')) {
                throw new \RuntimeException('Failed to exchange Facebook authorization code.');
            }

            return $this->fetchUserFromToken('facebook', (string) $tokenResp->json('access_token'));
        }

        abort(404);
    }

    public function fetchUserFromToken(string $provider, string $accessToken): array
    {
        if ($provider === 'google') {
            $resp = Http::withToken($accessToken)->timeout(10)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if (! $resp->successful() || ! $resp->json('email')) {
                throw new \RuntimeException('Could not retrieve Google user profile.');
            }

            return [
                'email' => (string) $resp->json('email'),
                'name' => (string) ($resp->json('name') ?: 'Student'),
                'avatar' => $resp->json('picture'),
            ];
        }

        if ($provider === 'facebook') {
            $resp = Http::timeout(10)->get('https://graph.facebook.com/me', [
                'fields' => 'id,name,email,picture.type(large)',
                'access_token' => $accessToken,
            ]);
            if (! $resp->successful() || ! $resp->json('email')) {
                throw new \RuntimeException('Could not retrieve Facebook user profile (email permission required).');
            }

            return [
                'email' => (string) $resp->json('email'),
                'name' => (string) ($resp->json('name') ?: 'Student'),
                'avatar' => data_get($resp->json(), 'picture.data.url'),
            ];
        }

        abort(404);
    }

    public function findOrCreateStudent(array $profile, Request $request): Student
    {
        $student = Student::where('email', $profile['email'])->first();
        if ($student) {
            if (! $student->email_verified_at) {
                $student->forceFill(['email_verified_at' => now()])->save();
            }

            return $student;
        }

        return DB::transaction(function () use ($profile, $request) {
            $student = Student::create([
                'name' => $profile['name'],
                'email' => $profile['email'],
                'email_verified_at' => now(),
                'password' => Str::random(24),
                'type' => 'hsc',
                'avatar' => $profile['avatar'] ?? null,
                'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                'referred_by_student_id' => $request->query('ref')
                    ? Student::where('referral_code', $request->query('ref'))->value('id')
                    : null,
            ]);

            $freePlan = Plan::where('is_free', true)->orWhere('price_monthly', 0)->first();
            if ($freePlan) {
                app(SubscriptionService::class)->assign($student, $freePlan, 12);
            }

            app(ReferralService::class)->recordRegistration($student, $request);

            return $student;
        });
    }
}
