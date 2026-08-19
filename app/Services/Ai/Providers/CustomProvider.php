<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Admin-defined "custom" provider: points at ANY full URL (not just a base
 * URL) that speaks the OpenAI /v1/chat/completions wire format. Because the
 * endpoint can be anything, the admin can also add arbitrary request headers
 * (custom API key / authorization header names etc.) stored on the provider
 * row as JSON in `custom_headers`:
 *
 *     [ { "name": "x-api-key", "value": "sk-..." }, ... ]
 *
 * If the admin defines an Authorization header explicitly it wins; otherwise
 * the provider falls back to the standard `api_key` → `Bearer` behavior.
 */
class CustomProvider implements AiProviderContract
{
    public function __construct(protected AiProvider $provider) {}

    public function chat(array $messages, array $options = []): string
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(60)
            ->post($this->endpoint(), [
                'model' => $this->provider->model_name,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens,
                'temperature' => $options['temperature'] ?? (float) $this->provider->temperature,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('AI provider request failed: '.$response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $response = Http::withHeaders($this->headers())
            ->withOptions(['stream' => true])
            ->timeout(90)
            ->post($this->endpoint(), [
                'model' => $this->provider->model_name,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens,
                'temperature' => $options['temperature'] ?? (float) $this->provider->temperature,
                'stream' => true,
            ]);

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';

        while (! $body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if (! str_starts_with($line, 'data:')) {
                    continue;
                }

                $data = trim(substr($line, 5));
                if ($data === '[DONE]') {
                    return;
                }

                $json = json_decode($data, true);
                $chunk = $json['choices'][0]['delta']['content'] ?? null;
                if ($chunk !== null && $chunk !== '') {
                    yield $chunk;
                }
            }
        }
    }

    public function isAvailable(): bool
    {
        return (bool) $this->provider->api_endpoint;
    }

    /**
     * The full chat/completions URL the admin pasted. No path is appended —
     * unlike the named providers, this is the complete endpoint.
     */
    private function endpoint(): string
    {
        return rtrim($this->provider->api_endpoint ?: '', '/');
    }

    /**
     * Custom headers from the provider row win over everything; only when
     * no Authorization header is defined does the standard api_key→Bearer
     * fallback apply.
     */
    private function headers(): array
    {
        $headers = [];

        foreach ((array) $this->provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $headers[$name] = (string) ($header['value'] ?? '');
        }

        if (! isset($headers['Authorization']) && $this->provider->api_key) {
            $headers['Authorization'] = 'Bearer '.$this->provider->api_key;
        }

        return $headers;
    }
}