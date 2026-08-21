<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Course;
use Illuminate\Support\Str;

/**
 * Single source of truth for meta tags + JSON-LD structured data across
 * the public site. A page controller calls one of these methods and
 * passes the whole array straight through as an Inertia prop; the Vue
 * page then spreads it into <Head> + <JsonLd> (see resources/js/Components/Seo).
 */
class SeoService
{
    private string $siteName;

    private string $siteUrl;

    public function __construct(private readonly SiteSettingService $settings)
    {
        $this->siteName = $settings->get('site_name', 'Sikhun.com') ?: 'Sikhun.com';
        $this->siteUrl = rtrim(config('app.url'), '/');
    }

    /**
     * The admin-configured social share image, falling back to the bundled
     * default until one is uploaded in Admin → Settings.
     */
    public function socialImage(string $fallback = 'images/og-default.png'): string
    {
        $seoImage = $this->settings->get('seo_image');

        return $seoImage ? asset('storage/'.$seoImage) : asset($fallback);
    }

    public function forHome(): array
    {
        return [
            'title' => "{$this->siteName} — বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম",
            'description' => 'ডিজিটালি বই পড়ুন, AI-এর সাথে চ্যাট করুন, পরীক্ষা দিন এবং ফ্ল্যাশকার্ড তৈরি করুন। HSC, SSC, বিশ্ববিদ্যালয় ও চাকরির প্রস্তুতির জন্য।',
            'og_image' => $this->socialImage(),
            'canonical' => $this->siteUrl.'/',
            'keywords' => 'sikhun, শিখুন, HSC, SSC, Bangladesh education, AI learning, digital books',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => $this->siteName,
                'description' => 'AI-powered education platform for Bangladeshi students',
                'url' => $this->siteUrl,
                'inLanguage' => ['bn', 'en'],
                'areaServed' => 'BD',
            ],
        ];
    }

    public function forBook(Book $book): array
    {
        return [
            'title' => "{$book->title} — {$this->levelLabel($book->level)} | {$this->siteName}",
            'description' => Str::limit(strip_tags($book->description ?? ''), 155),
            'og_image' => $book->cover_image_url ?? $this->socialImage(),
            'canonical' => $this->siteUrl.'/library/'.$book->slug,
            'keywords' => implode(', ', array_filter([$book->title, $book->subject, $book->level, 'বই', 'Sikhun'])),
            'json_ld' => array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Book',
                'name' => $book->title,
                'description' => Str::limit(strip_tags($book->description ?? ''), 200),
                'author' => $book->author ? ['@type' => 'Person', 'name' => $book->author->name] : null,
                'publisher' => $book->publication ? ['@type' => 'Organization', 'name' => $book->publication->name] : null,
                'inLanguage' => 'bn',
                'educationalLevel' => $this->levelLabel($book->level),
                'url' => $this->siteUrl.'/library/'.$book->slug,
            ]),
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Library', 'url' => $this->siteUrl.'/library'],
                ['name' => $book->title, 'url' => $this->siteUrl.'/library/'.$book->slug],
            ]),
        ];
    }

    public function forCourse(Course $course): array
    {
        return [
            'title' => "{$course->title} | {$this->siteName}",
            'description' => Str::limit(strip_tags($course->description ?? ''), 155),
            'og_image' => $course->cover_image_url ?? $this->socialImage(),
            'canonical' => $this->siteUrl.'/courses/'.$course->slug,
            'keywords' => implode(', ', [$course->title, 'কোর্স', 'শিখুন', 'Sikhun']),
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Course',
                'name' => $course->title,
                'description' => Str::limit(strip_tags($course->description ?? ''), 200),
                'provider' => ['@type' => 'Organization', 'name' => $this->siteName, 'url' => $this->siteUrl],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => (string) $course->price,
                    'priceCurrency' => 'BDT',
                    'availability' => 'https://schema.org/InStock',
                ],
                'url' => $this->siteUrl.'/courses/'.$course->slug,
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Courses', 'url' => $this->siteUrl.'/courses'],
                ['name' => $course->title, 'url' => $this->siteUrl.'/courses/'.$course->slug],
            ]),
        ];
    }

    public function forLibrary(): array
    {
        return [
            'title' => "ডিজিটাল লাইব্রেরি — {$this->siteName}",
            'description' => 'HSC, SSC ও বিশ্ববিদ্যালয়ের ডিজিটাল বই — সরাসরি ব্রাউজারে পড়ুন, AI-এর সাথে বই নিয়ে আলোচনা করুন, ফ্ল্যাশকার্ড বানান।',
            'og_image' => $this->socialImage(),
            'canonical' => $this->siteUrl.'/library',
            'keywords' => 'ডিজিটাল লাইব্রেরি, অনলাইন বই, HSC বই, SSC বই, পড়ার বই, sikhun library',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "ডিজিটাল লাইব্রেরি | {$this->siteName}",
                'url' => $this->siteUrl.'/library',
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Library', 'url' => $this->siteUrl.'/library'],
            ]),
        ];
    }

    public function forCourses(): array
    {
        return [
            'title' => "লাইভ কোর্সসমূহ — {$this->siteName}",
            'description' => 'অভিজ্ঞ মেন্টরদের সাথে লাইভ কোর্স: HSC, SSC, বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতি। ঘরে বসেই সম্পূর্ণ কোর্স শেষ করুন।',
            'og_image' => $this->socialImage(),
            'canonical' => $this->siteUrl.'/courses',
            'keywords' => 'অনলাইন কোর্স, লাইভ ক্লাস, HSC কোর্স, SSC কোর্স, ভর্তি প্রস্তুতি, sikhun courses',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "লাইভ কোর্সসমূহ | {$this->siteName}",
                'url' => $this->siteUrl.'/courses',
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Courses', 'url' => $this->siteUrl.'/courses'],
            ]),
        ];
    }

    public function forFaq(array $qaPairs): array
    {
        return [
            'title' => "প্রশ্নোত্তর | {$this->siteName}",
            'description' => 'Sikhun.com সম্পর্কিত সচরাচর জিজ্ঞাসিত প্রশ্নের উত্তর।',
            'canonical' => $this->siteUrl.'/p/faq',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($qaPairs)->map(fn ($qa) => [
                    '@type' => 'Question',
                    'name' => $qa['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['answer']],
                ])->all(),
            ],
        ];
    }

    private function breadcrumb(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    private function levelLabel(?string $level): string
    {
        return match ($level) {
            'hsc' => 'HSC',
            'ssc' => 'SSC',
            'university' => 'বিশ্ববিদ্যালয়',
            'job' => 'চাকরির প্রস্তুতি',
            default => 'সাধারণ',
        };
    }
}
