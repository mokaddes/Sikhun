<?php

namespace App\Console\Commands;

use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Models\JobCircular;
use App\Models\QuestionSubject;
use App\Services\IndexNowService;
use App\Services\PublicQuestionCatalog;
use Illuminate\Console\Command;

class SubmitIndexNowCommand extends Command
{
    protected $signature = 'seo:indexnow {--limit=200 : Max dynamic URLs per category}';

    protected $description = 'Submit Sikhun.com public URLs to IndexNow (Bing / ChatGPT Search / Copilot)';

    public function handle(IndexNowService $indexNow): int
    {
        $base = rtrim(config('app.url', 'https://sikhun.com'), '/');
        $limit = max(10, (int) $this->option('limit'));

        $urls = [
            "{$base}/",
            "{$base}/questions",
            "{$base}/library",
            "{$base}/courses",
            "{$base}/admission-notice",
            "{$base}/job-news",
            "{$base}/blog",
            "{$base}/plans",
            "{$base}/tools",
            "{$base}/results",
        ];

        foreach (\App\Http\Controllers\Public\ToolController::SLUG_MAP as $toolSlug => $_) {
            $urls[] = "{$base}/tools/{$toolSlug}";
        }

        foreach (PublicQuestionCatalog::collections() as $slug => $meta) {
            $urls[] = "{$base}/questions/{$slug}";
        }

        QuestionSubject::where('is_active', true)
            ->whereHas('questions', fn ($q) => $q->published())
            ->pluck('slug')
            ->take(50)
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/questions/subject/{$slug}";
            });

        AdmissionNotice::published()
            ->latest('updated_at')
            ->take($limit)
            ->pluck('slug')
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/admission-notice/{$slug}";
            });

        JobCircular::where('title', 'not like', '%admission%')
            ->where('title', 'not like', '%ভর্তি%')
            ->latest('updated_at')
            ->take($limit)
            ->pluck('slug')
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/job-news/{$slug}";
            });

        Book::published()
            ->latest('updated_at')
            ->take($limit)
            ->pluck('slug')
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/library/{$slug}";
            });

        Course::active()
            ->latest('updated_at')
            ->take($limit)
            ->pluck('slug')
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/courses/{$slug}";
            });

        BlogPost::published()
            ->latest('published_at')
            ->take($limit)
            ->pluck('slug')
            ->each(function ($slug) use (&$urls, $base) {
                $urls[] = "{$base}/blog/{$slug}";
            });

        $result = $indexNow->submit($urls);

        if ($result['ok']) {
            $this->info("IndexNow accepted {$result['count']} URLs (HTTP {$result['status']}).");

            return self::SUCCESS;
        }

        $this->error("IndexNow failed (HTTP {$result['status']}): ".($result['message'] ?? $result['body'] ?? 'Unknown error'));

        return self::FAILURE;
    }
}
