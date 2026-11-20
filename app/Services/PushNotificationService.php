<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Dispatches FCM push notifications to registered student devices.
 * Supports FCM HTTP v1 (service account JSON) and Legacy/Custom endpoint fallback.
 */
class PushNotificationService
{
    public function __construct(protected SiteSettingService $settings) {}

    public function isEnabled(): bool
    {
        return filter_var($this->settings->get('fcm_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function sendToStudent(Student $student, string $title, string $body, array $data = []): int
    {
        if (! $this->isEnabled()) {
            return 0;
        }

        $subscriptions = PushSubscription::where('student_id', $student->id)
            ->where('channel', 'fcm')
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $sent = 0;
        foreach ($subscriptions as $sub) {
            $ok = $this->sendToToken($sub->token, $title, $body, $data, $sub);
            if ($ok) {
                $sent++;
            }
        }

        return $sent;
    }

    protected function sendToToken(string $token, string $title, string $body, array $data, PushSubscription $sub): bool
    {
        $projectId = trim((string) $this->settings->get('fcm_project_id', ''));
        $serviceAccountJson = trim((string) $this->settings->get('fcm_service_account_json', ''));
        $legacyServerKey = trim((string) $this->settings->get('fcm_server_key', ''));

        $stringData = collect($data)->mapWithKeys(fn ($v, $k) => [(string) $k => is_scalar($v) ? (string) $v : json_encode($v)])->all();

        try {
            if ($projectId !== '' && $serviceAccountJson !== '') {
                $accessToken = $this->getFcmV1AccessToken($serviceAccountJson);
                if (! $accessToken) {
                    return false;
                }

                $response = Http::withToken($accessToken)
                    ->timeout(8)
                    ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => $stringData,
                        ],
                    ]);

                if ($response->status() === 404 || str_contains($response->body(), 'UNREGISTERED')) {
                    $sub->delete();

                    return false;
                }

                return $response->successful();
            }

            if ($legacyServerKey !== '') {
                $response = Http::withHeaders([
                    'Authorization' => 'key='.$legacyServerKey,
                    'Content-Type' => 'application/json',
                ])->timeout(8)->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $stringData,
                ]);

                if ($response->successful()) {
                    $error = data_get($response->json(), 'results.0.error');
                    if (in_array($error, ['NotRegistered', 'InvalidRegistration'], true)) {
                        $sub->delete();

                        return false;
                    }

                    return ($response->json('success') ?? 0) > 0;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('FCM push failed', ['error' => $e->getMessage()]);
        }

        return false;
    }

    private function getFcmV1AccessToken(string $serviceAccountJson): ?string
    {
        return Cache::remember('fcm_v1_oauth_token', 3000, function () use ($serviceAccountJson) {
            $sa = json_decode($serviceAccountJson, true);
            if (! is_array($sa) || empty($sa['client_email']) || empty($sa['private_key'])) {
                return null;
            }

            $now = time();
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = $this->base64UrlEncode(json_encode([
                'iss' => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            $signingInput = "{$header}.{$claim}";
            if (! openssl_sign($signingInput, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256)) {
                return null;
            }

            $jwt = $signingInput.'.'.$this->base64UrlEncode($signature);

            $resp = Http::asForm()->timeout(8)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $resp->successful() ? $resp->json('access_token') : null;
        });
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
