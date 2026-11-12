<?php

namespace App\Services;

use App\Models\AdmissionNotice;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Models\CustomPage;
use App\Models\JobCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Single source of truth for SEO metadata + JSON-LD structured data across Sikhun.com.
 *
 * Provides authoritative metadata for both server-rendered initial HTML (app.blade.php)
 * and Inertia client-side page transitions (SeoHead.vue).
 */
class SeoService
{
    private string $siteName;

    private string $siteUrl;

    public function __construct(private readonly SiteSettingService $settings)
    {
        $this->siteName = $settings->get('site_name', 'Sikhun.com') ?: 'Sikhun.com';
        $this->siteUrl = rtrim(config('app.url', 'https://sikhun.com'), '/');
    }

    /**
     * Resolves the configured social share image URL.
     */
    public function socialImage(string $fallback = 'images/og-default.png'): string
    {
        $seoImage = $this->settings->get('seo_image');

        return $seoImage ? asset('storage/'.$seoImage) : asset($fallback);
    }

    /**
     * Resolves the official site logo URL.
     */
    public function logoUrl(): string
    {
        $logo = $this->settings->get('site_logo');

        return $logo ? asset('storage/'.$logo) : asset('images/logo.png');
    }

    /**
     * Homepage SEO metadata + WebSite & EducationalOrganization JSON-LD.
     */
    public function forHome(): array
    {
        return [
            'title' => "{$this->siteName} — বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম",
            'description' => 'ডিজিটালি বই পড়ুন, AI-এর সাথে চ্যাট করুন, পরীক্ষা দিন এবং ফ্ল্যাশকার্ড তৈরি করুন। HSC, SSC, বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতির জন্য।',
            'og_image' => $this->socialImage(),
            'canonical' => $this->siteUrl.'/',
            'keywords' => 'sikhun, শিখুন, HSC, SSC, Bangladesh education, AI learning, digital books, ভর্তি প্রস্তুতি, প্রশ্নব্যাংক',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebSite',
                        '@id' => $this->siteUrl.'/#website',
                        'url' => $this->siteUrl,
                        'name' => $this->siteName,
                        'description' => 'AI-powered learning and academic preparation platform for Bangladeshi students',
                        'inLanguage' => ['bn-BD', 'en-US'],
                        'potentialAction' => [
                            '@type' => 'SearchAction',
                            'target' => [
                                '@type' => 'EntryPoint',
                                'urlTemplate' => $this->siteUrl.'/library?search={search_term_string}',
                            ],
                            'query-input' => 'required name=search_term_string',
                        ],
                    ],
                    [
                        '@type' => 'EducationalOrganization',
                        '@id' => $this->siteUrl.'/#organization',
                        'name' => $this->siteName,
                        'url' => $this->siteUrl,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $this->logoUrl(),
                        ],
                        'description' => 'First AI-powered digital learning ecosystem in Bangladesh for HSC, SSC, Admission and Competitive Exams.',
                        'sameAs' => [
                            'https://www.facebook.com/sikhunbd',
                        ],
                        'inLanguage' => ['bn', 'en'],
                        'areaServed' => 'BD',
                    ],
                ],
            ],
        ];
    }

    /**
     * University Admission Notices Archive SEO.
     */
    public function forAdmissionIndex(?string $type = null): array
    {
        $typeLabel = match ($type) {
            'public' => 'পাবলিক বিশ্ববিদ্যালয়',
            'guccho' => 'গুচ্ছ বিশ্ববিদ্যালয়',
            'private' => 'বেসরকারি বিশ্ববিদ্যালয়',
            default => 'সকল বিশ্ববিদ্যালয়',
        };

        $title = $type
            ? "{$typeLabel} ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ — সার্কুলার ও রুটিন | {$this->siteName}"
            : "বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ — সার্কুলার, রুটিন ও প্রশ্নব্যাংক | {$this->siteName}";

        $description = 'পাবলিক, গুচ্ছ ও মেডিকেল বিশ্ববিদ্যালয়ের সর্বশেষ ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭। ঢাকা বিশ্ববিদ্যালয়, রাজশাহী বিশ্ববিদ্যালয়, বুয়েট সহ সকল সার্কুলার, আবেদন নিয়ম ও বিগত বছরের প্রশ্নব্যাংক।';
        $canonical = $this->siteUrl.'/admission-notice';

        return [
            'title' => $title,
            'description' => $description,
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি, ভর্তি সার্কুলার ২০২৬, ঢাবি ভর্তি, রাবি ভর্তি, গুচ্ছ ভর্তি, BUET admission circular, sikhun admission',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'description' => $description,
                'url' => $canonical,
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'ভর্তি বিজ্ঞপ্তি', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Individual Admission Notice SEO + SpecialAnnouncement Schema.
     */
    public function forAdmissionNotice(AdmissionNotice $notice): array
    {
        $metaTitle = "{$notice->institution_name} স্নাতক ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ | {$this->siteName}";

        $descText = strip_tags($notice->description ?? '');
        $metaDescription = Str::limit($descText ?: "{$notice->institution_name} ২০২৬-২০২৭ শিক্ষাবর্ষে স্নাতক প্রথম বর্ষে ভর্তির অফিসিয়াল বিজ্ঞপ্তি, যোগ্যতা, ইউনিট ও আবেদনের নিয়মাবলী।", 155);
        $canonical = $this->siteUrl.'/admission-notice/'.$notice->slug;
        $image = $notice->notice_image_url ?: $this->socialImage();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SpecialAnnouncement',
            'name' => $notice->title,
            'text' => Str::limit($descText, 300),
            'url' => $canonical,
            'datePosted' => ($notice->created_at ?? now())->toIso8601String(),
            'inLanguage' => 'bn',
            'category' => 'https://schema.org/EducationalOccupationalCredential',
            'publisher' => [
                '@type' => 'EducationalOrganization',
                'name' => $this->siteName,
                'url' => $this->siteUrl,
            ],
        ];

        if ($notice->application_deadline) {
            $schema['expires'] = $notice->application_deadline->toIso8601String();
        }

        if ($notice->apply_url) {
            $schema['webVerification'] = $notice->apply_url;
        }

        return [
            'title' => $metaTitle,
            'description' => $metaDescription,
            'og_image' => $image,
            'canonical' => $canonical,
            'keywords' => implode(', ', array_filter([
                $notice->institution_name,
                'ভর্তি বিজ্ঞপ্তি ২০২৬',
                'Admission Circular',
                $notice->university_type,
                'Sikhun',
            ])),
            'json_ld' => $schema,
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'ভর্তি বিজ্ঞপ্তি', 'url' => $this->siteUrl.'/admission-notice'],
                ['name' => $notice->institution_name, 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Digital Book SEO.
     */
    public function forBook(Book $book): array
    {
        $canonical = $this->siteUrl.'/library/'.$book->slug;

        return [
            'title' => "{$book->title} — {$this->levelLabel($book->level)} | {$this->siteName}",
            'description' => Str::limit(strip_tags($book->description ?? ''), 155) ?: "{$book->title} ডিজিটাল বই সরাসরি অনলাইনে পড়ুন এবং AI স্টাডি সহকারীর সাথে আলোচনা করুন।",
            'og_image' => $book->cover_image_url ?? $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => implode(', ', array_filter([$book->title, $book->subject, $book->level, 'ডিজিটাল বই', 'পাঠ্যবই', 'Sikhun'])),
            'json_ld' => array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Book',
                'name' => $book->title,
                'description' => Str::limit(strip_tags($book->description ?? ''), 200),
                'author' => $book->author ? ['@type' => 'Person', 'name' => $book->author->name] : null,
                'publisher' => $book->publication ? ['@type' => 'Organization', 'name' => $book->publication->name] : null,
                'inLanguage' => 'bn',
                'educationalLevel' => $this->levelLabel($book->level),
                'url' => $canonical,
            ]),
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Library', 'url' => $this->siteUrl.'/library'],
                ['name' => $book->title, 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Course SEO.
     */
    public function forCourse(Course $course): array
    {
        $canonical = $this->siteUrl.'/courses/'.$course->slug;

        return [
            'title' => "{$course->title} | {$this->siteName}",
            'description' => Str::limit(strip_tags($course->description ?? ''), 155) ?: "{$course->title} অনলাইন কোর্সে অভিজ্ঞ মেন্টর ও লাইভ ক্লাসের মাধ্যমে প্রস্তুতি নিন।",
            'og_image' => $course->cover_image_url ?? $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => implode(', ', [$course->title, 'কোর্স', 'শিখুন', 'Sikhun', 'অনলাইন ক্লাস']),
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
                'url' => $canonical,
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Courses', 'url' => $this->siteUrl.'/courses'],
                ['name' => $course->title, 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Library Catalog Index SEO.
     */
    public function forLibrary(): array
    {
        $canonical = $this->siteUrl.'/library';

        return [
            'title' => "ডিজিটাল লাইব্রেরি — {$this->siteName}",
            'description' => 'HSC, SSC ও বিশ্ববিদ্যালয়ের ডিজিটাল বই — সরাসরি ব্রাউজারে পড়ুন, AI-এর সাথে বই নিয়ে আলোচনা করুন, ফ্ল্যাশকার্ড তৈরি করুন।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'ডিজিটাল লাইব্রেরি, অনলাইন বই, HSC বই, SSC বই, পড়ার বই, sikhun library, AI book reader',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "ডিজিটাল লাইব্রেরি | {$this->siteName}",
                'url' => $canonical,
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Library', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Courses Catalog Index SEO.
     */
    public function forCourses(): array
    {
        $canonical = $this->siteUrl.'/courses';

        return [
            'title' => "অনলাইন কোর্সসমূহ — {$this->siteName}",
            'description' => 'অভিজ্ঞ মেন্টরদের সাথে লাইভ কোর্স: HSC, SSC, বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতি। ঘরে বসেই সম্পূর্ণ কোর্স শেষ করুন।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'অনলাইন কোর্স, লাইভ ক্লাস, HSC কোর্স, SSC কোর্স, ভর্তি প্রস্তুতি, sikhun courses',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "লাইভ কোর্সসমূহ | {$this->siteName}",
                'url' => $canonical,
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'Courses', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Blog Archive Index SEO.
     */
    public function forBlogIndex(?BlogCategory $category = null): array
    {
        $title = $category
            ? "{$category->name_bn} — ব্লগ | {$this->siteName}"
            : "ব্লগ — বিজ্ঞান, প্রযুক্তি, AI ও শিক্ষা | {$this->siteName}";

        $canonical = $category
            ? "{$this->siteUrl}/blog/category/{$category->slug}"
            : "{$this->siteUrl}/blog";

        $crumbs = [
            ['name' => 'Home', 'url' => $this->siteUrl],
            ['name' => 'Blog', 'url' => $this->siteUrl.'/blog'],
        ];
        if ($category) {
            $crumbs[] = ['name' => $category->name_bn, 'url' => $canonical];
        }

        return [
            'title' => $title,
            'description' => 'বিজ্ঞান, প্রযুক্তি, কৃত্রিম বুদ্ধিমত্তা (AI) এবং একাডেমিক প্রস্তুতি নিয়ে বাংলা শিক্ষামূলক ব্লগ ও আর্টিকেল।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'বাংলা ব্লগ, প্রযুক্তি ব্লগ, AI ব্লগ, বিজ্ঞান আর্টিকেল, শিক্ষা ব্লগ, Sikhun blog',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => ['bn', 'en'],
                'publisher' => ['@type' => 'Organization', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb($crumbs),
        ];
    }

    /**
     * Individual Blog Article SEO + Article Schema.
     */
    public function forBlogPost(BlogPost $post): array
    {
        $metaTitle = $post->meta_title ?: "{$post->title} | {$this->siteName}";
        $metaDescription = $post->meta_description ?: Str::limit(strip_tags($post->excerpt ?: $post->content), 155);
        $canonical = "{$this->siteUrl}/blog/{$post->slug}";
        $image = $post->featured_image_url ?? $this->socialImage();

        $crumbs = [
            ['name' => 'Home', 'url' => $this->siteUrl],
            ['name' => 'Blog', 'url' => $this->siteUrl.'/blog'],
        ];
        if ($post->category) {
            $crumbs[] = [
                'name' => $post->category->name_bn,
                'url' => "{$this->siteUrl}/blog/category/{$post->category->slug}",
            ];
        }
        $crumbs[] = ['name' => $post->title, 'url' => $canonical];

        return [
            'title' => $metaTitle,
            'description' => $metaDescription,
            'og_image' => $image,
            'canonical' => $canonical,
            'keywords' => implode(', ', array_filter([$post->title, $post->category?->name_bn, 'ব্লগ', 'Sikhun'])),
            'json_ld' => array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => Str::limit($post->title, 110, ''),
                'description' => $metaDescription,
                'image' => [$image],
                'datePublished' => ($post->published_at ?? $post->created_at)?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'inLanguage' => 'bn',
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
                'author' => ['@type' => 'Organization', 'name' => $this->siteName, 'url' => $this->siteUrl],
                'publisher' => ['@type' => 'Organization', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ]),
            'breadcrumb' => $this->breadcrumb($crumbs),
        ];
    }

    /**
     * Job News Archive SEO.
     */
    public function forJobIndex(): array
    {
        $canonical = $this->siteUrl.'/job-news';

        return [
            'title' => "চাকরির খবর ও নিয়োগ বিজ্ঞপ্তি ২০২৬ | {$this->siteName}",
            'description' => 'সরকারি, ব্যাংক ও বেসরকারি চাকরির সর্বশেষ নিয়োগ বিজ্ঞপ্তি, আবেদন প্রক্রিয়া, পরীক্ষার রুটিন ও প্রস্তুতি সহায়িকা।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'চাকরির খবর, নিয়োগ বিজ্ঞপ্তি ২০২৬, সরকারি চাকরি, ব্যাংক জব, job circular bangladesh, sikhun jobs',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "চাকরির খবর ও সার্কুলার ২০২৬ | {$this->siteName}",
                'url' => $canonical,
                'inLanguage' => ['bn', 'en'],
                'isPartOf' => ['@type' => 'WebSite', 'name' => $this->siteName, 'url' => $this->siteUrl],
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'চাকরির খবর', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Individual Job Circular SEO.
     */
    public function forJobCircular(JobCircular $job): array
    {
        $canonical = $this->siteUrl.'/job-news/'.$job->slug;
        $metaDescription = Str::limit(strip_tags($job->description ?? ''), 155) ?: "{$job->title} চাকরির নিয়োগ বিজ্ঞপ্তি, আবেদনের যোগ্যতা ও বিস্তারিত।";

        return [
            'title' => "{$job->title} | {$this->siteName}",
            'description' => $metaDescription,
            'og_image' => $job->image_url ?: $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => implode(', ', array_filter([$job->title, $job->organization, 'চাকরির খবর', 'Sikhun'])),
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'JobPosting',
                'title' => $job->title,
                'description' => Str::limit(strip_tags($job->description ?? ''), 250),
                'datePosted' => ($job->created_at ?? now())->toIso8601String(),
                'validThrough' => $job->deadline?->toIso8601String(),
                'hiringOrganization' => [
                    '@type' => 'Organization',
                    'name' => $job->organization ?: $this->siteName,
                ],
                'jobLocation' => [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'BD',
                    ],
                ],
                'url' => $canonical,
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'চাকরির খবর', 'url' => $this->siteUrl.'/job-news'],
                ['name' => $job->title, 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Student Tools Hub SEO.
     */
    public function forToolsIndex(): array
    {
        $canonical = $this->siteUrl.'/tools';

        return [
            'title' => "স্টুডেন্ট টুলস ও রেজাল্ট ক্যালকুলেটর | {$this->siteName}",
            'description' => 'শিক্ষার্থীদের জন্য প্রয়োজনীয় অনলাইন শিক্ষামূলক টুলস: GPA ক্যালকুলেটর, বয়স ক্যালকুলেটর ও পরীক্ষার ফলাফল অনুসন্ধান।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'স্টুডেন্ট টুলস, GPA ক্যালকুলেটর, বয়স ক্যালকুলেটর, রেজাল্ট অনুসন্ধান, sikhun tools',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => "স্টুডেন্ট টুলস | {$this->siteName}",
                'url' => $canonical,
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'টুলস', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Contact Us Page SEO.
     */
    public function forContact(): array
    {
        $canonical = $this->siteUrl.'/contact';

        return [
            'title' => "যোগাযোগ করুন | {$this->siteName}",
            'description' => 'Sikhun.com-এর সাথে যেকোনো শিক্ষা বিষয়ক পরামর্শ, কোর্স বা কারিগরি সহায়তায় যোগাযোগ করুন।',
            'og_image' => $this->socialImage(),
            'canonical' => $canonical,
            'keywords' => 'যোগাযোগ, sikhun contact, কাস্টমার সাপোর্ট, শিখুন সাপোর্ট',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => "যোগাযোগ করুন | {$this->siteName}",
                'url' => $canonical,
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'যোগাযোগ', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * FAQ Page SEO with FAQPage Schema.
     */
    public function forFaq(array $qaPairs): array
    {
        $canonical = $this->siteUrl.'/p/faq';

        return [
            'title' => "প্রশ্নোত্তর (FAQ) | {$this->siteName}",
            'description' => 'Sikhun.com প্ল্যাটফর্ম, বই পড়া, কোর্স এবং AI স্টাডি টুল সম্পর্কিত সচরাচর জিজ্ঞাসিত প্রশ্নের উত্তর।',
            'canonical' => $canonical,
            'og_image' => $this->socialImage(),
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($qaPairs)->map(fn ($qa) => [
                    '@type' => 'Question',
                    'name' => $qa['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['answer']],
                ])->all(),
            ],
            'breadcrumb' => $this->breadcrumb([
                ['name' => 'Home', 'url' => $this->siteUrl],
                ['name' => 'FAQ', 'url' => $canonical],
            ]),
        ];
    }

    /**
     * Dynamic Central Resolver for Server-Side Blade Rendering.
     *
     * Ensures every single request (public or private) gets safe, escaped,
     * accurate meta tags, canonical link, and appropriate robots directive.
     */
    public function resolveForRequest(Request $request, ?array $seoProps = null): array
    {
        $path = trim($request->path(), '/');

        // Check if the current route is private and must not be indexed
        $isPrivate = $this->isPrivateRoute($path, $request);

        if ($isPrivate) {
            return [
                'title' => $this->siteName,
                'description' => 'Sikhun.com — AI-powered learning platform',
                'canonical' => $this->siteUrl.'/'.$path,
                'robots' => 'noindex, nofollow',
                'site_name' => $this->siteName,
                'og_image' => null,
                'keywords' => null,
                'json_ld' => null,
                'breadcrumb' => null,
            ];
        }

        // If controller passed an explicit 'seo' prop, use it
        if (! empty($seoProps) && is_array($seoProps)) {
            return [
                'title' => $seoProps['title'] ?? $this->siteName,
                'description' => $seoProps['description'] ?? 'Sikhun.com — AI-powered learning platform in Bangladesh.',
                'canonical' => $seoProps['canonical'] ?? ($this->siteUrl.($path ? '/'.$path : '/')),
                'robots' => $seoProps['robots'] ?? 'index, follow',
                'site_name' => $this->siteName,
                'og_image' => $seoProps['og_image'] ?? $this->socialImage(),
                'keywords' => $seoProps['keywords'] ?? null,
                'json_ld' => $seoProps['json_ld'] ?? null,
                'breadcrumb' => $seoProps['breadcrumb'] ?? null,
            ];
        }

        // Automatic path-based fallback for public routes where controller didn't supply seo
        $fallback = match (true) {
            $path === '' || $path === '/' => $this->forHome(),
            $path === 'library' => $this->forLibrary(),
            $path === 'courses' => $this->forCourses(),
            $path === 'blog' => $this->forBlogIndex(),
            $path === 'admission-notice' => $this->forAdmissionIndex(),
            $path === 'job-news' => $this->forJobIndex(),
            $path === 'tools' || $path === 'results' => $this->forToolsIndex(),
            $path === 'contact' => $this->forContact(),
            default => [
                'title' => "{$this->siteName} — AI-চালিত শিক্ষা প্ল্যাটফর্ম",
                'description' => 'HSC, SSC, বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতির জন্য বাংলাদেশের শীর্ষস্থানীয় AI শিক্ষা প্ল্যাটফর্ম।',
                'canonical' => $this->siteUrl.'/'.$path,
                'robots' => 'index, follow',
                'site_name' => $this->siteName,
                'og_image' => $this->socialImage(),
                'keywords' => 'sikhun, শিক্ষা, বই, কোর্স, মডেল টেস্ট',
                'json_ld' => null,
                'breadcrumb' => null,
            ],
        };

        $fallback['site_name'] = $this->siteName;
        $fallback['robots'] = 'index, follow';

        return $fallback;
    }

    /**
     * Determines whether a given path is an authenticated/private route.
     */
    public function isPrivateRoute(string $path, ?Request $request = null): bool
    {
        $privatePrefixes = [
            'admin',
            'dashboard',
            'bookshelf',
            'wallet',
            'plans',
            'ai',
            'exams',
            'flashcards',
            'essays',
            'schedules',
            'leaderboard',
            'referrals',
            'profile',
            'notifications',
            'reader',
            'mistakes',
            'question-bank',
            'support',
            'api',
            'sanctum',
            'horizon',
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'verify-email',
            'two-factor',
        ];

        foreach ($privatePrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        // If query parameters have search / filter pagination with high cardinality
        if ($request && $request->filled('search') && strlen((string) $request->input('search')) > 0) {
            return false; // Handled via canonical self-referencing clean URL
        }

        return false;
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
