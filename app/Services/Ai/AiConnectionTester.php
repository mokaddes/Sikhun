<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Lightweight "is this provider reachable" check used by the admin
 * "Test Connection" button.
 */
class AiConnectionTester
{
    public function test(AiProvider $provider): array
    {
        Log::info('Testing AI provider connection:', [
            'id' => $provider->id,
            'type' => $provider->type,
            'url' => $provider->api_endpoint,
            'model' => $provider->model_name,
        ]);

        try {
            return match ($provider->type) {
                'openai_compatible', 'openai', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface' => $this->checkOpenAiCompatible($provider),
                'anthropic_compatible', 'claude' => $this->checkAnthropic($provider),
                'google_gemini', 'gemini' => $this->checkGemini($provider),
                'custom' => $this->checkCustom($provider),
                default => ['success' => false, 'message' => "Unknown provider type: {$provider->type}"],
            };
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Connection error: '.$e->getMessage()];
        }
    }

    private function checkOpenAiCompatible(AiProvider $provider): array
    {
        $endpoint = trim((string) ($provider->api_endpoint ?: ''));

        // If no endpoint specified, check OpenAI official API
        if ($endpoint === '') {
            if (empty($provider->api_key)) {
                return ['success' => false, 'message' => 'No API key configured for OpenAI.'];
            }
            $response = Http::withToken($provider->api_key)->timeout(8)->get('https://api.openai.com/v1/models');
            return $response->successful()
                ? ['success' => true, 'message' => 'Connected successfully to OpenAI.']
                : ['success' => false, 'message' => "OpenAI responded with HTTP status {$response->status()}."];
        }

        // Build target URL for completions check
        $targetUrl = str_ends_with($endpoint, '/chat/completions')
            ? $endpoint
            : rtrim($endpoint, '/').'/chat/completions';

        $headers = [];
        foreach ((array) $provider->custom_headers as $h) {
            $name = trim((string) ($h['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($h['value'] ?? '');
            }
        }

        if (! isset($headers['Authorization']) && ! empty($provider->api_key)) {
            $headers['Authorization'] = 'Bearer '.$provider->api_key;
        }

        $payload = [
            'model' => $provider->model_name ?: 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => 'ping']],
            'max_tokens' => 1,
        ];

        $response = Http::withHeaders($headers)->timeout(12)->post($targetUrl, $payload);

        if ($response->successful()) {
            return ['success' => true, 'message' => 'Connected successfully to OpenAI-compatible endpoint.'];
        }

        // If 404 on /chat/completions, try a GET on models/tags endpoint
        $modelsUrl = str_ends_with($endpoint, '/v1') ? $endpoint.'/models' : rtrim($endpoint, '/').'/models';
        $fallback = Http::withHeaders($headers)->timeout(6)->get($modelsUrl);
        if ($fallback->successful()) {
            return ['success' => true, 'message' => 'Connected to host (models endpoint verified).'];
        }

        return [
            'success' => false,
            'message' => "Endpoint returned HTTP {$response->status()}: ".mb_substr($response->body(), 0, 150),
        ];
    }

    private function checkAnthropic(AiProvider $provider): array
    {
        if (empty($provider->api_key)) {
            return ['success' => false, 'message' => 'No API key configured for Anthropic.'];
        }

        $endpoint = trim((string) ($provider->api_endpoint ?: ''));
        $targetUrl = 'https://api.anthropic.com/v1/messages';
        if ($endpoint !== '') {
            $targetUrl = str_ends_with($endpoint, '/messages')
                ? $endpoint
                : (str_ends_with($endpoint, '/v1') ? $endpoint.'/messages' : rtrim($endpoint, '/').'/v1/messages');
        }

        $headers = [
            'x-api-key' => $provider->api_key,
            'anthropic-version' => '2023-06-01',
        ];

        foreach ((array) $provider->custom_headers as $h) {
            $name = trim((string) ($h['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($h['value'] ?? '');
            }
        }

        $response = Http::withHeaders($headers)->timeout(10)->post($targetUrl, [
            'model' => $provider->model_name ?: 'claude-3-haiku-20240307',
            'max_tokens' => 1,
            'messages' => [['role' => 'user', 'content' => 'ping']],
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Connected successfully to Anthropic.']
            : ['success' => false, 'message' => "Anthropic responded with HTTP {$response->status()}: ".mb_substr($response->body(), 0, 150)];
    }

    private function checkGemini(AiProvider $provider): array
    {
        if (empty($provider->api_key)) {
            return ['success' => false, 'message' => 'No API key configured for Google Gemini.'];
        }

        $baseUrl = rtrim($provider->api_endpoint ?: 'https://generativelanguage.googleapis.com', '/');

        $response = Http::timeout(8)->get("{$baseUrl}/v1beta/models", [
            'key' => $provider->api_key,
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Connected successfully to Google Gemini API.']
            : ['success' => false, 'message' => "Gemini API responded with HTTP {$response->status()}: ".mb_substr($response->body(), 0, 150)];
    }

    private function checkCustom(AiProvider $provider): array
    {
        $url = trim((string) ($provider->api_endpoint ?: ''));

        if ($url === '') {
            return ['success' => false, 'message' => 'No custom API endpoint URL configured.'];
        }

        $headers = [];
        foreach ((array) $provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($header['value'] ?? '');
            }
        }

        if (! isset($headers['Authorization']) && ! empty($provider->api_key)) {
            $headers['Authorization'] = 'Bearer '.$provider->api_key;
        }

        $payload = [
            'model' => $provider->model_name ?: 'default',
            'messages' => [['role' => 'user', 'content' => 'ping']],
            'max_tokens' => 1,
        ];

        $response = Http::withHeaders($headers)->timeout(12)->post($url, $payload);

        return $response->successful()
            ? ['success' => true, 'message' => 'Custom API endpoint reachable and response accepted.']
            : ['success' => false, 'message' => "Endpoint responded with HTTP {$response->status()}: ".mb_substr($response->body(), 0, 150)];
    }
}
