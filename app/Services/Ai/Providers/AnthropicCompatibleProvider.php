<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

class AnthropicCompatibleProvider implements AiProviderContract
{
    public function __construct(protected AiProvider $provider) {}

    protected function endpoint(): string
    {
        $endpoint = trim((string) ($this->provider->api_endpoint ?: ''));

        if ($endpoint === '') {
            return 'https://api.anthropic.com/v1/messages';
        }

        if (str_ends_with($endpoint, '/messages')) {
            return $endpoint;
        }

        if (str_ends_with($endpoint, '/v1')) {
            return $endpoint.'/messages';
        }

        return rtrim($endpoint, '/').'/v1/messages';
    }

    protected function headers(): array
    {
        $headers = [
            'anthropic-version' => '2023-06-01',
        ];

        foreach ((array) $this->provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($header['value'] ?? '');
            }
        }

        if (! isset($headers['x-api-key']) && ! empty($this->provider->api_key)) {
            $headers['x-api-key'] = $this->provider->api_key;
        }

        return $headers;
    }

    /** Anthropic takes `system` as a top-level param, not a message in the array. */
    protected function splitSystem(array $messages): array
    {
        $system = '';
        $rest = [];

        foreach ($messages as $m) {
            if ($m['role'] === 'system') {
                $system .= $m['content']."\n";
            } else {
                $rest[] = $m;
            }
        }

        return [$system ?: null, $rest];
    }

    public function chat(array $messages, array $options = []): string
    {
        [$system, $rest] = $this->splitSystem($messages);

        $response = Http::withHeaders($this->headers())
            ->timeout(60)
            ->post($this->endpoint(), array_filter([
                'model' => $this->provider->model_name,
                'system' => $system,
                'messages' => $rest,
                'max_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens,
                'temperature' => $options['temperature'] ?? (float) $this->provider->temperature,
            ]));

        if ($response->failed()) {
            throw new \RuntimeException('Anthropic request failed: '.$response->body());
        }

        return data_get($response->json(), 'content.0.text', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        [$system, $rest] = $this->splitSystem($messages);

        $response = Http::withHeaders($this->headers())
            ->withOptions(['stream' => true])
            ->timeout(90)
            ->post($this->endpoint(), array_filter([
                'model' => $this->provider->model_name,
                'system' => $system,
                'messages' => $rest,
                'max_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens,
                'stream' => true,
            ]));

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
                if (($json['type'] ?? null) === 'content_block_delta') {
                    $chunk = $json['delta']['text'] ?? null;
                    if ($chunk) {
                        yield $chunk;
                    }
                }
            }
        }
    }

    public function isAvailable(): bool
    {
        return ! empty($this->provider->api_key) || ! empty($this->provider->api_endpoint);
    }
}
