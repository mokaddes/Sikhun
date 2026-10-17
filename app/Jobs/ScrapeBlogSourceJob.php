<?php

namespace App\Jobs;

use App\Models\BlogScrapeLog;
use App\Models\BlogSource;
use App\Services\Blog\BlogDeduplicator;
use App\Services\Blog\BlogScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeBlogSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 300;

    public function __construct(public int $sourceId)
    {
        $this->onQueue('ai');
    }

    public function handle(
        BlogScraperService $scraper,
        BlogDeduplicator $deduplicator
    ): ?BlogScrapeLog {
        $source = BlogSource::find($this->sourceId);
        if (! $source) {
            return null;
        }

        $log = BlogScrapeLog::create([
            'blog_source_id' => $source->id,
            'started_at' => now(),
        ]);

        try {
            $candidates = $scraper->fetchCandidates($source);
            $log->update(['found' => count($candidates)]);

            $maxPerRun = max(1, (int) $source->max_per_run);
            $processedCount = 0;

            foreach ($candidates as $candidate) {
                if ($processedCount >= $maxPerRun) {
                    break;
                }

                $url = trim((string) ($candidate['url'] ?? ''));
                $title = trim((string) ($candidate['title'] ?? ''));
                $rawContent = trim((string) ($candidate['raw_content'] ?? ''));

                if ($url === '') {
                    continue;
                }

                // First-pass quick check by URL and feed title before fetching article HTML
                if ($deduplicator->isBlocked($url, $title ?: null)) {
                    $log->increment('blocked');
                    continue;
                }

                if ($deduplicator->isDuplicate($url, $title ?: null)) {
                    $log->increment('duplicates');
                    continue;
                }

                $beforeDrafted = $log->fresh()->drafted;

                ProcessBlogArticleJob::dispatchSync(
                    $source->id,
                    $log->id,
                    $url,
                    $title,
                    $rawContent
                );

                $afterDrafted = $log->fresh()->drafted;
                if ($afterDrafted > $beforeDrafted) {
                    $processedCount++;
                }
            }

            $log->update(['finished_at' => now()]);
            $source->update([
                'last_run_at' => now(),
                'last_status' => 'completed',
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'failed' => $log->failed + 1,
                'error' => $e->getMessage(),
                'finished_at' => now(),
            ]);
            $source->update([
                'last_run_at' => now(),
                'last_status' => 'failed',
            ]);
        }

        return $log->fresh();
    }
}
