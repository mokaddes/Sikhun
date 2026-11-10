<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeBlogSourceJob;
use App\Mail\BlogDraftDigestMail;
use App\Models\BlogPost;
use App\Models\BlogSource;
use App\Services\Blog\BlogDeduplicator;
use App\Services\Blog\BlogScraperService;
use App\Services\SiteSettingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScrapeBlogs extends Command
{
    protected $signature = 'blog:scrape
                            {--source= : Run only a specific BlogSource ID}
                            {--limit=20 : Maximum drafts to produce in this cycle (default 20)}
                            {--no-digest : Do not send the draft digest email after completion}';

    protected $description = 'Scrape active blog sources, deduplicate, translate to Bengali, AI rewrite into drafts, and email a digest (capped at 15-20 per cycle)';

    public function handle(
        BlogScraperService $scraper,
        BlogDeduplicator $deduplicator,
        SiteSettingService $settings
    ): int {
        $sourceId = $this->option('source');
        $maxLimit = max(1, (int) $this->option('limit'));

        $sources = $sourceId
            ? BlogSource::where('id', $sourceId)->get()
            : BlogSource::active()->get();

        if ($sources->isEmpty()) {
            $this->info('No active blog sources to scrape.');

            return self::SUCCESS;
        }

        $newDrafts = 0;
        $duplicates = 0;
        $blocked = 0;
        $failed = 0;

        foreach ($sources as $source) {
            if ($newDrafts >= $maxLimit) {
                $this->info("Reached weekly cycle limit of {$maxLimit} drafts. Gracefully ending cycle.");
                break;
            }

            $this->info("Scraping source #{$source->id}: {$source->name} ({$source->url})");

            $job = new ScrapeBlogSourceJob($source->id);
            $log = $job->handle($scraper, $deduplicator);

            if ($log) {
                $newDrafts += $log->drafted;
                $duplicates += $log->duplicates;
                $blocked += $log->blocked;
                $failed += $log->failed;

                $this->line("  -> Found: {$log->found}, Drafted: {$log->drafted}, Duplicates: {$log->duplicates}, Blocked: {$log->blocked}, Failed: {$log->failed}");
            }
        }

        $totalDrafts = BlogPost::draft()->count();

        if (! $this->option('no-digest') && $newDrafts > 0) {
            $settingRecipient = trim((string) ($settings->get('blog_digest_email') ?: $settings->get('site_email')));
            $recipients = array_unique(array_filter(['mr.mokaddes@gmail.com', $settingRecipient]));

            if (! empty($recipients)) {
                try {
                    $recentDrafts = BlogPost::draft()
                        ->latest()
                        ->take(15)
                        ->get(['id', 'title', 'slug', 'source_url', 'created_at']);

                    Mail::to($recipients)->send(new BlogDraftDigestMail([
                        'total_drafts' => $totalDrafts,
                        'new_drafts' => $newDrafts,
                        'duplicates' => $duplicates,
                        'blocked' => $blocked,
                        'failed' => $failed,
                        'recent_drafts' => $recentDrafts,
                    ]));

                    $this->info('Digest email sent to ' . implode(', ', $recipients));
                } catch (\Throwable $e) {
                    Log::warning('Failed to send blog draft digest email: ' . $e->getMessage());
                    $this->warn('Could not send digest email: ' . $e->getMessage());
                }
            }
        }

        $this->info("Done. New drafts: {$newDrafts} (limit: {$maxLimit}) | Total drafts waiting: {$totalDrafts}");

        return self::SUCCESS;
    }
}
