<?php

namespace App\Services\Questions;

use App\Models\QuestionSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuestionSourceManagerService
{
    /**
     * Fetch and inspect robots.txt for a given source.
     */
    public function checkRobotsTxt(QuestionSource $source): array
    {
        $baseUrl = rtrim($source->base_url, '/');
        $robotsUrl = $baseUrl . '/robots.txt';

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'SikhunEducationBot/1.0 (+https://sikhun.com/bot; bot@sikhun.com)',
                    'Accept' => 'text/plain,*/*',
                ])
                ->get($robotsUrl);

            if (!$response->successful()) {
                // If 404, robots.txt doesn't exist, which standard RFC interprets as "allowed"
                $status = ($response->status() === 404) ? 'allowed' : 'unchecked';
                $notes = "HTTP {$response->status()} returned while requesting robots.txt.";

                $source->update([
                    'robots_txt_status' => $status,
                    'robots_txt_checked_at' => now(),
                    'tos_notes' => $source->tos_notes ? "{$source->tos_notes}\n[Robots.txt: {$notes}]" : "[Robots.txt: {$notes}]",
                ]);

                return [
                    'success' => true,
                    'status' => $status,
                    'message' => $notes,
                ];
            }

            $body = strtolower($response->body());
            $isDisallowedAll = (str_contains($body, 'disallow: /') && !str_contains($body, 'allow: /'));
            $isConditional = str_contains($body, 'disallow:');

            $status = 'allowed';
            if ($isDisallowedAll) {
                $status = 'disallowed';
            } elseif ($isConditional) {
                $status = 'conditional';
            }

            $notes = "Robots.txt successfully verified (Status: {$status}).";

            $source->update([
                'robots_txt_status' => $status,
                'robots_txt_checked_at' => now(),
            ]);

            return [
                'success' => true,
                'status' => $status,
                'message' => $notes,
            ];
        } catch (\Throwable $e) {
            Log::warning("Robots.txt check failed for {$source->name}: " . $e->getMessage());

            $source->update([
                'robots_txt_status' => 'unchecked',
                'robots_txt_checked_at' => now(),
            ]);

            return [
                'success' => false,
                'status' => 'unchecked',
                'message' => 'Connection timed out or failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to source base URL.
     */
    public function testConnection(QuestionSource $source): array
    {
        $start = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'SikhunAcademicBot/1.0 (Bangladesh Education Research; +https://sikhun.com)',
                ])
                ->get($source->base_url);

            $duration = round((microtime(true) - $start) * 1000, 2);

            $isSuccess = $response->successful();
            $statusStr = $isSuccess ? 'reachable' : 'http_error_' . $response->status();

            $source->update([
                'last_crawl_at' => now(),
                'last_crawl_status' => $isSuccess ? 'success' : 'failed',
            ]);

            return [
                'success' => $isSuccess,
                'http_code' => $response->status(),
                'response_time_ms' => $duration,
                'message' => "Source responded with HTTP {$response->status()} in {$duration}ms.",
            ];
        } catch (\Throwable $e) {
            $source->update([
                'last_crawl_at' => now(),
                'last_crawl_status' => 'failed',
            ]);

            return [
                'success' => false,
                'http_code' => 0,
                'response_time_ms' => 0,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }
}
