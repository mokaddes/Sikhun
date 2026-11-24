<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Models\CustomPage;
use App\Models\JobCircular;
use App\Models\QuestionSubject;
use App\Services\PublicQuestionCatalog;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generates a fully dynamic, cached, standards-compliant XML sitemap.
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml_payload', 3600, function () {
            return $this->buildSitemapXml();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }

    /**
     * Clear sitemap cache on content modifications.
     */
    public static function clearCache(): void
    {
        Cache::forget('sitemap_xml_payload');
    }

    public function buildSitemapXml(): string
    {
        $baseUrl = rtrim(config('app.url', 'https://sikhun.com'), '/');
        $now = now()->toIso8601String();

        $urls = [];

        // 1. Homepage
        $urls[] = [
            'loc' => $baseUrl.'/',
            'lastmod' => $now,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // 2. Core Educational Hubs
        $coreSections = [
            ['path' => '/questions', 'priority' => '0.95', 'changefreq' => 'daily'],
            ['path' => '/library', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/courses', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/admission-notice', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/blog', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/job-news', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['path' => '/plans', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['path' => '/tools', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['path' => '/results', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['path' => '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        foreach ($coreSections as $section) {
            $urls[] = [
                'loc' => $baseUrl.$section['path'],
                'lastmod' => $now,
                'changefreq' => $section['changefreq'],
                'priority' => $section['priority'],
            ];
        }

        // 2b. Public Programmatic SEO Question Bank Collections & Subjects
        foreach (PublicQuestionCatalog::collections() as $slug => $meta) {
            $urls[] = [
                'loc' => "{$baseUrl}/questions/{$slug}",
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        $subjects = QuestionSubject::where('is_active', true)
            ->whereHas('questions', fn ($q) => $q->published())
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($subjects as $sub) {
            $urls[] = [
                'loc' => "{$baseUrl}/questions/subject/{$sub->slug}",
                'lastmod' => ($sub->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 2c. Dedicated Smart Student Tool Pages (/tools/{slug})
        foreach (array_keys(\App\Http\Controllers\Public\ToolController::SLUG_MAP) as $toolSlug) {
            $urls[] = [
                'loc' => "{$baseUrl}/tools/{$toolSlug}",
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.75',
            ];
        }

        // 3. Published Books
        $books = Book::published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($books as $book) {
            $urls[] = [
                'loc' => "{$baseUrl}/library/{$book->slug}",
                'lastmod' => ($book->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 4. Active Courses
        $courses = Course::active()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($courses as $course) {
            $urls[] = [
                'loc' => "{$baseUrl}/courses/{$course->slug}",
                'lastmod' => ($course->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 5. Published Blog Posts
        $posts = BlogPost::published()
            ->select(['slug', 'published_at', 'updated_at'])
            ->orderByDesc('published_at')
            ->get();

        foreach ($posts as $post) {
            $lastmod = ($post->updated_at ?? $post->published_at ?? now())->toIso8601String();
            $urls[] = [
                'loc' => "{$baseUrl}/blog/{$post->slug}",
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 6. Active Blog Categories (only those with at least 1 published post to avoid thin/empty pages)
        $categories = BlogCategory::where('is_active', true)
            ->whereHas('posts', fn ($q) => $q->published())
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($categories as $cat) {
            $urls[] = [
                'loc' => "{$baseUrl}/blog/category/{$cat->slug}",
                'lastmod' => ($cat->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // 7. Published University Admission Notices
        $admissions = AdmissionNotice::published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($admissions as $admission) {
            $urls[] = [
                'loc' => "{$baseUrl}/admission-notice/{$admission->slug}",
                'lastmod' => ($admission->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        }

        // 8. Active Job Circulars (exclude admission circulars, prioritize open deadlines)
        $jobs = JobCircular::select(['slug', 'updated_at'])
            ->where('title', 'not like', '%admission%')
            ->where('title', 'not like', '%ভর্তি%')
            ->where('slug', 'not like', '%admission%')
            ->where('slug', 'not like', '%coursework%')
            ->orderByRaw('CASE WHEN deadline >= CURRENT_DATE THEN 0 ELSE 1 END ASC')
            ->orderByDesc('updated_at')
            ->take(300)
            ->get();

        foreach ($jobs as $job) {
            $urls[] = [
                'loc' => "{$baseUrl}/job-news/{$job->slug}",
                'lastmod' => ($job->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '0.7',
            ];
        }

        // 9. Published Custom Informational Pages
        $pages = CustomPage::where('is_published', true)
            ->whereNotIn('slug', ['contact'])
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($pages as $page) {
            $urls[] = [
                'loc' => "{$baseUrl}/p/{$page->slug}",
                'lastmod' => ($page->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Build XML string cleanly
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $entry) {
            $loc = htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8');
            $lastmod = htmlspecialchars($entry['lastmod'], ENT_XML1, 'UTF-8');
            $changefreq = htmlspecialchars($entry['changefreq'], ENT_XML1, 'UTF-8');
            $priority = htmlspecialchars($entry['priority'], ENT_XML1, 'UTF-8');

            $xml .= "    <url>\n";
            $xml .= "        <loc>{$loc}</loc>\n";
            $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "        <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "        <priority>{$priority}</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
