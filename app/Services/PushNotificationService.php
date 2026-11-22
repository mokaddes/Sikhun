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

    /**
     * Public Firebase Web SDK configuration (safe to expose to browsers).
     */
    public function webConfig(): array
    {
        $all = $this->settings->all();

        $enabled = filter_var($all['fcm_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $apiKey = trim((string) ($all['fcm_api_key'] ?? ''));
        $authDomain = trim((string) ($all['fcm_auth_domain'] ?? ''));
        $projectId = trim((string) ($all['fcm_project_id'] ?? ''));
        $storageBucket = trim((string) ($all['fcm_storage_bucket'] ?? ''));
        $messagingSenderId = trim((string) ($all['fcm_messaging_sender_id'] ?? ''));
        $appId = trim((string) ($all['fcm_app_id'] ?? ''));
        $vapidKey = trim((string) ($all['fcm_vapid_key'] ?? ''));

        $configured = $enabled
            && $apiKey !== ''
            && $projectId !== ''
            && $messagingSenderId !== ''
            && $appId !== ''
            && $vapidKey !== '';

        return [
            'enabled' => $enabled,
            'configured' => $configured,
            'api_key' => $apiKey,
            'auth_domain' => $authDomain !== '' ? $authDomain : ($projectId !== '' ? "{$projectId}.firebaseapp.com" : ''),
            'project_id' => $projectId,
            'storage_bucket' => $storageBucket,
            'messaging_sender_id' => $messagingSenderId,
            'app_id' => $appId,
            'vapid_key' => $vapidKey,
        ];
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
            $result = $this->sendToToken($sub->token, $title, $body, $data, $sub);
            if ($result['ok']) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Send a test push notification directly to a specific FCM token and return diagnostic info.
     *
     * @return array{ok: bool, error: ?string}
     */
    public function sendTestToToken(string $token, string $title, string $body, array $data = []): array
    {
        return $this->sendToToken($token, $title, $body, $data, null);
    }

    /**
     * @return array{ok: bool, error: ?string}
     */
    protected function sendToToken(string $token, string $title, string $body, array $data, ?PushSubscription $sub): array
    {
        $projectId = trim((string) $this->settings->get('fcm_project_id', ''));
        $serviceAccountJson = trim((string) $this->settings->get('fcm_service_account_json', ''));
        $legacyServerKey = trim((string) $this->settings->get('fcm_server_key', ''));

        $clickUrl = (string) ($data['url'] ?? $data['action_url'] ?? url('/dashboard'));
        if (str_starts_with($clickUrl, '/')) {
            $clickUrl = url($clickUrl);
        }

        $faviconPath = $this->settings->get('site_favicon');
        $iconUrl = $faviconPath ? asset('storage/'.$faviconPath) : asset('favicon.svg');

        $payloadData = array_merge(['url' => $clickUrl], $data);
        $stringData = collect($payloadData)
            ->mapWithKeys(fn ($v, $k) => [(string) $k => is_scalar($v) ? (string) $v : json_encode($v)])
            ->all();

        try {
            if ($projectId !== '' && $serviceAccountJson !== '') {
                $accessToken = $this->getFcmV1AccessToken($serviceAccountJson);
                if (! $accessToken) {
                    return ['ok' => false, 'error' => 'Failed to obtain OAuth2 access token from Firebase Service Account JSON.'];
                }

                $response = Http::withToken($accessToken)
                    ->timeout(10)
                    ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => $stringData,
                            'webpush' => [
                                'headers' => [
                                    'Urgency' => 'high',
                                ],
                                'notification' => [
                                    'title' => $title,
                                    'body' => $body,
                                    'icon' => $iconUrl,
                                    'badge' => $iconUrl,
                                ],
                                'fcm_options' => [
                                    'link' => $clickUrl,
                                ],
                            ],
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'title' => $title,
                                    'body' => $body,
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ]);

                if ($response->status() === 404 || str_contains($response->body(), 'UNREGISTERED')) {
                    $sub?->delete();

                    return ['ok' => false, 'error' => 'Token is unregistered or expired (removed).'];
                }

                if ($response->successful()) {
                    return ['ok' => true, 'error' => null];
                }

                $errMsg = data_get($response->json(), 'error.message') ?? $response->body();
                Log::warning('FCM v1 push error', ['status' => $response->status(), 'error' => $errMsg]);

                return ['ok' => false, 'error' => "FCM HTTP {$response->status()}: {$errMsg}"];
            }

            if ($legacyServerKey !== '') {
                $response = Http::withHeaders([
                    'Authorization' => 'key='.$legacyServerKey,
                    'Content-Type' => 'application/json',
                ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'icon' => $iconUrl,
                        'click_action' => $clickUrl,
                    ],
                    'data' => $stringData,
                ]);

                if ($response->successful()) {
                    $error = data_get($response->json(), 'results.0.error');
                    if (in_array($error, ['NotRegistered', 'InvalidRegistration'], true)) {
                        $sub?->delete();

                        return ['ok' => false, 'error' => "Legacy FCM error: {$error}"];
                    }

                    $ok = ($response->json('success') ?? 0) > 0;

                    return ['ok' => $ok, 'error' => $ok ? null : ($error ?? 'Unknown Legacy FCM failure')];
                }

                return ['ok' => false, 'error' => "Legacy FCM HTTP {$response->status()}"];
            }

            return ['ok' => false, 'error' => 'Firebase Project ID and Service Account JSON are not configured.'];
        } catch (\Throwable $e) {
            Log::warning('FCM push failed', ['error' => $e->getMessage()]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
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
