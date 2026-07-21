<?php

namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderContract
{
    public function __construct(private AiProvider $provider) {}

    /** Gemini has no separate "system" role — fold it into the first user turn. */
    private function toGeminiContents(array $messages): array
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

        $response = Http::timeout(60)->withOptions([
            'query' => ['key' => $this->provider->api_key],
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
            ['contents' => $this->toGeminiContents($messages)]
        );

        return data_get($response->json(), 'candidates.0.content.parts.0.text', '');
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $model = $this->provider->model_name ?: 'gemini-1.5-flash';

        $response = Http::withOptions([
            'stream' => true,
            'query' => ['key' => $this->provider->api_key, 'alt' => 'sse'],
        ])->timeout(90)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent",
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
        return ! empty($this->provider->api_key);
    }
}
