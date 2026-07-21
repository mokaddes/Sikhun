<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
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
}
