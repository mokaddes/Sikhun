<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderContract
{
    public function __construct(protected AiProvider $provider) {}

    protected function baseUrl(): string
    {
        return rtrim($this->provider->api_endpoint ?: 'https://generativelanguage.googleapis.com', '/');
    }

    protected function headers(): array
    {
        $headers = [];

        foreach ((array) $this->provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($header['value'] ?? '');
            }
        }

        return $headers;
    }

    /** Gemini has no separate "system" role — fold it into the first user turn. */
    protected function toGeminiContents(array $messages): array
    {
        $contents = [];
        $systemPrefix = '';

        foreach ($messages as $m) {
            if ($m['role'] === 'system') {
                $systemPrefix .= $m['content']."\n\n";
                continue;
            }
            $contents[] = [
                'role' => $m['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $contents === [] ? $systemPrefix.$m['content'] : $m['content']]],
            ];
        }

        return $contents;
    }

    public function chat(array $messages, array $options = []): string
    {
        $model = $this->provider->model_name ?: 'gemini-1.5-flash';

        $req = Http::withHeaders($this->headers())->timeout(60);

        if (! empty($this->provider->api_key)) {
            $req = $req->withOptions(['query' => ['key' => $this->provider->api_key]]);
        }

        $response = $req->post(
            "{$this->baseUrl()}/v1beta/models/{$model}:generateContent",
            ['contents' => $this->toGeminiContents($messages)]
        );

        if ($response->failed()) {
            throw new \RuntimeException('Gemini request failed: '.$response->body());
        }

        return data_get($response->json(), 'candidates.0.content.parts.0.text', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $model = $this->provider->model_name ?: 'gemini-1.5-flash';

        $optionsQuery = ['alt' => 'sse'];
        if (! empty($this->provider->api_key)) {
            $optionsQuery['key'] = $this->provider->api_key;
        }

        $response = Http::withHeaders($this->headers())->withOptions([
            'stream' => true,
            'query' => $optionsQuery,
        ])->timeout(90)->post(
            "{$this->baseUrl()}/v1beta/models/{$model}:streamGenerateContent",
            ['contents' => $this->toGeminiContents($messages)]
        );

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

                $json = json_decode(trim(substr($line, 5)), true);
                $chunk = data_get($json, 'candidates.0.content.parts.0.text');
                if ($chunk) {
                    yield $chunk;
                }
            }
        }
    }

    public function isAvailable(): bool
    {
        return ! empty($this->provider->api_key) || ! empty($this->provider->api_endpoint);
    }
}
