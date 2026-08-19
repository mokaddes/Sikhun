<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Lightweight "is this provider reachable" check used by the admin
 * "Test Connection" button. Deliberately cheap (list-models / health-check
 * endpoints, not a real completion) so it's safe to click repeatedly.
 *
 * Full chat/stream/embed calls go through AiProviderFactory (Phase 4) —
 * this class only answers "is the config valid and the endpoint alive?".
 */
class AiConnectionTester
{
    public function test(AiProvider $provider): array
    {
        try {
            return match ($provider->type) {
                'openai' => $this->checkBearerEndpoint($provider, 'https://api.openai.com/v1/models'),
                'groq' => $this->checkBearerEndpoint($provider, 'https://api.groq.com/openai/v1/models'),
                'deepseek' => $this->checkBearerEndpoint($provider, 'https://api.deepseek.com/v1/models'),
                'gemini' => $this->checkGemini($provider),
                'claude' => $this->checkAnthropic($provider),
                'huggingface' => $this->checkBearerEndpoint($provider, 'https://huggingface.co/api/whoami-v2'),
                'ollama' => $this->checkLocalEndpoint($provider, '/api/tags'),
                'vllm' => $this->checkLocalEndpoint($provider, '/v1/models'),
                'custom' => $this->checkCustom($provider),
                default => ['success' => false, 'message' => 'Unknown provider type.'],
            };
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Connection error: '.$e->getMessage()];
        }
    }

    private function checkBearerEndpoint(AiProvider $provider, string $url): array
    {
        if (empty($provider->api_key)) {
            return ['success' => false, 'message' => 'No API key configured.'];
        }

        $response = Http::withToken($provider->api_key)->timeout(6)->get($url);

        return $response->successful()
            ? ['success' => true, 'message' => 'Connected successfully.']
            : ['success' => false, 'message' => "API responded with status {$response->status()}."];
    }

    private function checkGemini(AiProvider $provider): array
    {
        if (empty($provider->api_key)) {
            return ['success' => false, 'message' => 'No API key configured.'];
        }

        $response = Http::timeout(6)->get('https://generativelanguage.googleapis.com/v1beta/models', [
            'key' => $provider->api_key,
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Connected successfully.']
            : ['success' => false, 'message' => "API responded with status {$response->status()}."];
    }

    private function checkAnthropic(AiProvider $provider): array
    {
        if (empty($provider->api_key)) {
            return ['success' => false, 'message' => 'No API key configured.'];
        }

        // Anthropic has no unauthenticated "list models" ping; a minimal
        // 1-token request is the standard way to validate a key cheaply.
        $response = Http::withHeaders([
            'x-api-key' => $provider->api_key,
            'anthropic-version' => '2023-06-01',
        ])->timeout(6)->post('https://api.anthropic.com/v1/messages', [
            'model' => $provider->model_name ?: 'claude-3-haiku-20240307',
            'max_tokens' => 1,
            'messages' => [['role' => 'user', 'content' => 'ping']],
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Connected successfully.']
            : ['success' => false, 'message' => "API responded with status {$response->status()}."];
    }

    private function checkLocalEndpoint(AiProvider $provider, string $path): array
    {
        $base = rtrim($provider->api_endpoint ?: '', '/');

        if (! $base) {
            return ['success' => false, 'message' => 'No endpoint URL configured.'];
        }

        $response = Http::timeout(4)->get($base.$path);

        return $response->successful()
            ? ['success' => true, 'message' => 'Local endpoint reachable.']
            : ['success' => false, 'message' => "Endpoint responded with status {$response->status()}."];
    }

    /**
     * Custom providers expose a full chat/completions URL and any number of
     * admin-defined request headers. A cheap GET to that URL is enough to
     * confirm the endpoint is alive and the headers are accepted (an
     * unauthenticated GET usually returns 401, which still proves the host
     * is reachable — a real "works" answer needs an actual completion).
     */
    private function checkCustom(AiProvider $provider): array
    {
        $url = $provider->api_endpoint ?: '';

        if (! $url) {
            return ['success' => false, 'message' => 'No endpoint URL configured.'];
        }

        $headers = [];
        foreach ((array) $provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($header['value'] ?? '');
            }
        }

        if (! isset($headers['Authorization']) && $provider->api_key) {
            $headers['Authorization'] = 'Bearer '.$provider->api_key;
        }

        // Default payload matching your curl example
        $payload = [
            'model' => $provider->model_name ?? 'deepseek-v4-flash-free',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'hey',
                ],
            ],
        ];

        // Change .get() to .post()
        $response = Http::withHeaders($headers)
            ->timeout(10)
            ->post($url, $payload);

        Log::info('Custom provider check response:', [
            'url' => $url,
            'headers' => $headers,
            'response' => $response->json(),
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Endpoint reachable.']
            : ['success' => false, 'message' => "Endpoint responded with status {$response->status()}."];
    }
}
