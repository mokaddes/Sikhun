<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Base for any provider that speaks the OpenAI /v1/chat/completions
 * wire format — which in practice is OpenAI itself, Groq, DeepSeek, and
 * any self-hosted vLLM server. Saves reimplementing SSE parsing 4 times.
 */
abstract class AbstractOpenAiCompatibleProvider implements AiProviderContract
{
    public function __construct(protected AiProvider $provider) {}

    abstract protected function baseUrl(): string;

    /**
     * The full chat/completions URL. Defaults to the standard OpenAI-style
     * base + '/chat/completions'; a custom provider may override this to
     * return its complete endpoint when the admin pastes a full URL.
     */
    protected function chatEndpoint(): string
    {
        return $this->baseUrl().'/chat/completions';
    }

    protected function headers(): array
    {
        return ['Authorization' => 'Bearer '.$this->provider->api_key];
    }

    public function chat(array $messages, array $options = []): string
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(60)
            ->post($this->chatEndpoint(), [
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
            ->post($this->chatEndpoint(), [
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
        return ! empty($this->provider->api_key) || ! empty($this->provider->api_endpoint);
    }
}
