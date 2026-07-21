<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

/**
 * HuggingFace's Inference API doesn't have a universal streaming format
 * across the huge variety of hosted models, so stream() here degrades
 * gracefully to "fetch the whole reply, then yield it as one chunk" —
 * functionally correct (the chat UI still works end to end) but not a
 * real token-by-token stream. Swap in a specific model's native streaming
 * format here if you standardize on one.
 */
class HuggingFaceProvider implements AiProviderContract
{
    public function __construct(private AiProvider $provider) {}

    public function chat(array $messages, array $options = []): string
    {
        $response = Http::withToken($this->provider->api_key)->timeout(60)->post(
            'https://api-inference.huggingface.co/models/'.$this->provider->model_name,
            [
                'inputs' => collect($messages)->map(fn ($m) => "{$m['role']}: {$m['content']}")->implode("\n"),
                'parameters' => ['max_new_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens],
            ]
        );

        return data_get($response->json(), '0.generated_text', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        yield $this->chat($messages, $options);
    }

    public function isAvailable(): bool
    {
        return ! empty($this->provider->api_key);
    }
}
