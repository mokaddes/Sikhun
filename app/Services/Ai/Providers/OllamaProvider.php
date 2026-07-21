<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

class OllamaProvider implements AiProviderContract
{
    public function __construct(private AiProvider $provider) {}

    private function baseUrl(): string
    {
        return rtrim($this->provider->api_endpoint ?: 'http://localhost:11434', '/');
    }

    public function chat(array $messages, array $options = []): string
    {
        $response = Http::timeout(90)->post($this->baseUrl().'/api/chat', [
            'model' => $this->provider->model_name,
            'messages' => $messages,
            'stream' => false,
            'options' => ['temperature' => $options['temperature'] ?? (float) $this->provider->temperature],
        ]);

        return $response->json('message.content', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $response = Http::withOptions(['stream' => true])->timeout(120)->post($this->baseUrl().'/api/chat', [
            'model' => $this->provider->model_name,
            'messages' => $messages,
            'stream' => true,
        ]);

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';

        // Ollama streams newline-delimited JSON objects (not SSE "data:" lines).
        while (! $body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($line === '') {
                    continue;
                }

                $json = json_decode($line, true);
                $chunk = $json['message']['content'] ?? null;
                if ($chunk) {
                    yield $chunk;
                }
                if (($json['done'] ?? false) === true) {
                    return;
                }
            }
        }
    }

    public function isAvailable(): bool
    {
        return (bool) $this->provider->api_endpoint;
    }
}
