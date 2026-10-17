<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Models\BlogScrapeLog;
use App\Models\BlogSource;
use App\Services\Blog\ArticleExtractor;
use App\Services\Blog\BlogDeduplicator;
use App\Services\Blog\BlogRewriter;
use App\Services\Blog\BlogTranslator;
use App\Services\SiteSettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessBlogArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    public function __construct(
        public int $sourceId,
        public int $logId,
        public string $url,
        public string $fallbackTitle = '',
        public string $fallbackContent = ''
    ) {
        $this->onQueue('ai');
    }

    public function handle(
        ArticleExtractor $extractor,
        BlogDeduplicator $deduplicator,
        BlogTranslator $translator,
        BlogRewriter $rewriter,
        SiteSettingService $settings
    ): void {
        $source = BlogSource::find($this->sourceId);
        $log = BlogScrapeLog::find($this->logId);

        if (! $source) {
            return;
        }

        // Enforce daily AI cap across all sources
        $dailyCap = max(1, (int) $settings->get('blog_daily_ai_limit', 25));
        $draftedToday = BlogPost::withTrashed()
            ->where('origin', 'auto')
            ->whereDate('created_at', today())
            ->count();

        if ($draftedToday >= $dailyCap) {
            return;
        }

        try {
            $extracted = $extractor->extract(
                $this->url,
                $source->title_selector,
                $source->content_selector,
                $this->fallbackTitle,
                $this->fallbackContent
            );

            $rawTitle = trim($extracted['title'] ?: $this->fallbackTitle);
            $rawContent = trim($extracted['content']);

            if ($rawTitle === '' || mb_strlen($rawContent, 'UTF-8') < 100) {
                $log?->increment('failed');
                $this->appendLogError($log, "Insufficient content extracted from {$this->url}");

                return;
            }

            // Second-stage check using extracted title + body content hash
            if ($deduplicator->isBlocked($this->url, $rawTitle, $rawContent)) {
                $log?->increment('blocked');

                return;
            }

            if ($deduplicator->isDuplicate($this->url, $rawTitle, $rawContent)) {
                $log?->increment('duplicates');

                return;
            }

            // Translate to Bengali if needed
            $translated = $translator->translateToBengali(
                $rawTitle,
                $rawContent,
                $extracted['language']
            );

            // Rewrite into original, scalable Bengali blog post
            $rewritten = $rewriter->rewrite(
                $translated['title_bn'],
                $translated['content_bn'],
                $rawTitle,
                $this->url,
                $source->name
            );

            // Final check on rewritten Bengali title similarity
            if ($deduplicator->isBlocked(null, $rewritten['title'], null)) {
                $log?->increment('blocked');

                return;
            }

            if ($deduplicator->isDuplicate(null, $rewritten['title'], null)) {
                $log?->increment('duplicates');

                return;
            }

            $slug = $this->uniqueSlug($rewritten['slug'], $rewritten['title']);

            BlogPost::create([
                'slug' => $slug,
                'title' => $rewritten['title'],
                'excerpt' => $rewritten['excerpt'],
                'content' => $rewritten['content'],
                'blog_category_id' => $source->default_category_id,
                'meta_title' => $rewritten['meta_title'],
                'meta_description' => $rewritten['meta_description'],
                'status' => 'draft',
                'published_at' => null,
                'origin' => 'auto',
                'blog_source_id' => $source->id,
                'source_url' => $deduplicator->normalizeUrl($this->url),
                'source_url_hash' => $deduplicator->hashUrl($this->url),
                'source_title' => Str::limit($rawTitle, 480, ''),
                'content_hash' => $deduplicator->hashContent($rawContent),
                'title_fingerprint' => $deduplicator->fingerprintTitle($rawTitle),
                'original_language' => $extracted['language'],
                'ai_meta' => [
                    'translated' => $translated['translated'],
                    'scraped_at' => now()->toIso8601String(),
                ],
            ]);

            $log?->increment('drafted');
        } catch (\Throwable $e) {
            $log?->increment('failed');
            $this->appendLogError($log, "{$this->url}: {$e->getMessage()}");
        }
    }

    private function uniqueSlug(string $preferredSlug, string $title): string
    {
        $base = Str::slug($preferredSlug);
        if ($base === '') {
            $base = 'post-' . Str::lower(Str::random(6));
        }
        $base = Str::limit($base, 180, '');

        $slug = $base;
        $counter = 2;

        while (BlogPost::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function appendLogError(?BlogScrapeLog $log, string $message): void
    {
        if (! $log) {
            return;
        }

        $existing = $log->error ? ($log->error . "\n") : '';
        $log->update([
            'error' => Str::limit($existing . $message, 4000, '...'),
        ]);
    }
}
