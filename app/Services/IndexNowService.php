<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * IndexNow client for instant URL indexing in Bing, Yandex, Seznam,
 * and AI search engines that rely on Bing's index (ChatGPT Search, Copilot).
 */
class IndexNowService
{
    public const KEY = 'sikhunbd2026indexnowkey8842a1f9c';

    public function key(): string
    {
        return (string) config('services.indexnow.key', self::KEY);
    }

    public function keyLocation(): string
    {
        return rtrim(config('app.url', 'https://sikhun.com'), '/').'/'.$this->key().'.txt';
    }

    /**
     * Submit one or more URLs (up to 10,000) to IndexNow.
     *
     * @param  array<int, string>  $urls
     */
    public function submit(array $urls): array
    {
        $baseUrl = rtrim(config('app.url', 'https://sikhun.com'), '/');
        $host = parse_url($baseUrl, PHP_URL_HOST) ?: 'sikhun.com';

        $cleanUrls = collect($urls)
            ->filter(fn ($u) => is_string($u) && str_starts_with($u, 'http'))
            ->unique()
            ->values()
            ->take(10000)
            ->all();

        if (empty($cleanUrls)) {
            return ['ok' => false, 'status' => 0, 'count' => 0, 'message' => 'No valid URLs provided'];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json; charset=utf-8'])
                ->post('https://api.indexnow.org/indexnow', [
                    'host' => $host,
                    'key' => $this->key(),
                    'keyLocation' => $this->keyLocation(),
                    'urlList' => $cleanUrls,
                ]);

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'count' => count($cleanUrls),
                'body' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::warning('IndexNow submission failed: '.$e->getMessage());

            return [
                'ok' => false,
                'status' => 0,
                'count' => count($cleanUrls),
                'message' => $e->getMessage(),
            ];
        }
    }
}
