<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Category;
use App\Models\Course;
use App\Models\CustomPage;
use App\Models\Mentor;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Testimonial;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    private const DESIGNED_PAGES = [
        'about' => 'Public/Pages/About',
        'how-it-works' => 'Public/Pages/HowItWorks',
        'faq' => 'Public/Pages/Faq',
        'terms' => 'Public/Pages/Terms',
        'privacy' => 'Public/Pages/Privacy',
    ];

    public function home(SeoService $seo): Response
    {
        // Real platform stats for the hero strip — cached 30 min since these
        // are cheap COUNT queries but the homepage is the highest-traffic
        // page on the whole site; no reason to run them on every single hit.
        $stats = Cache::remember('homepage_stats', 1800, fn () => [
            'courses' => Course::active()->count(),
            'books' => Book::published()->count(),
            'students' => Student::where('status', 'active')->count(),
            'mentors' => Mentor::count(),
        ]);

        // Cache the public homepage shelves for 5 minutes to eliminate ~12
        // repeated queries per hit on the highest-traffic route.
        $payload = Cache::remember('homepage_public_payload_v1', 300, fn () => [
            'categories' => Category::whereNull('parent_id')->orderBy('name')->get(['id', 'name', 'slug', 'type']),
            'topCourses' => Course::active()
                ->with(['mentor:id,name'])
                ->withCount('enrollments')
                ->latest()
                ->take(4)
                ->get(['id', 'title', 'slug', 'cover_image', 'price', 'level', 'mentor_id']),
            'popularBooks' => Book::published()
                ->with('author:id,name')
                ->latest()
                ->take(5)
                ->get(['id', 'title', 'slug', 'cover_image', 'price', 'is_free', 'level', 'total_pages', 'author_id']),
            'latestBlogs' => BlogPost::published()
                ->with('category:id,name_bn,name_en,slug')
                ->orderByDesc('published_at')
                ->take(3)
                ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'blog_category_id', 'published_at', 'view_count']),
            'plans' => Plan::where('is_active', true)->orderBy('price_monthly')->get(),
            'featuredJobs' => \App\Models\JobCircular::where('title', 'not like', '%admission%')
                ->where('title', 'not like', '%ভর্তি%')
                ->orderByDesc('is_featured')
                ->orderBy('deadline')
                ->take(4)
                ->get(),
            'featuredAdmissions' => AdmissionNotice::published()->orderByDesc('is_featured')->orderBy('application_deadline')->take(4)->get(['id', 'title', 'slug', 'institution_name', 'university_type', 'application_deadline', 'units']),
            'testimonials' => Testimonial::published()->get(['student_name', 'student_role', 'avatar', 'quote']),
        ]);

        return Inertia::render('Public/Home', [
            'stats' => $stats,
            ...$payload,
            'seo' => $seo->forHome(),
        ]);
    }

    public function show(CustomPage $page, SeoService $seo): Response
    {
        abort_unless($page->is_published, 404);

        if ($page->slug === 'contact') {
            abort(404);
        }

        $pageSeo = $page->slug === 'faq'
            ? $seo->forFaq([
                [
                    'question' => 'Sikhun.com কী এবং এটি কীভাবে কাজ করে?',
                    'answer' => 'Sikhun.com বাংলাদেশের প্রথম AI-চালিত ডিজিটাল শিক্ষা প্ল্যাটফর্ম, যেখানে HSC, SSC, বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতির ডিজিটাল বই, AI বুক টিউটর, ৫০,০০০+ প্রশ্নব্যাংক, মডেল টেস্ট ও মেন্টর-পরিচালিত কোর্স একসাথে পাওয়া যায়।',
                ],
                [
                    'question' => 'Sikhun.com-এ কি বিনামূল্যে পড়াশোনা করা যায়?',
                    'answer' => 'হ্যাঁ, বিনামূল্যে অ্যাকাউন্ট খুলে ফ্রি বই পড়া, প্রশ্নব্যাংক অনুশীলন, ভর্তি ও চাকরির বিজ্ঞপ্তি দেখা এবং ফ্রি প্ল্যানে AI চ্যাট ও মডেল টেস্ট ব্যবহার করা যায়।',
                ],
                [
                    'question' => 'AI বুক টিউটর কীভাবে পাঠ্যবই থেকে উত্তর দেয়?',
                    'answer' => 'শিক্ষার্থীরা ডিজিটাল বই পড়ার সময় যেকোনো অধ্যায় বা পৃষ্ঠা নিয়ে প্রশ্ন করলে AI টিউটর সরাসরি বইয়ের নির্ভরযোগ্য অংশ রেফারেন্স করে বাংলা বা ইংরেজিতে ব্যাখ্যা দেয়।',
                ],
                [
                    'question' => 'বিশ্ববিদ্যালয় ভর্তি ও চাকরির প্রস্তুতির জন্য কী কী সুবিধা আছে?',
                    'answer' => 'ঢাবি, রাবি, বুয়েট, গুচ্ছ, মেডিকেল এবং বিসিএস ও ব্যাংক চাকরির বিগত বছরের প্রশ্নব্যাংক, নেগেটিভ মার্কিংসহ মডেল টেস্ট, আমার ভুলসমূহ রিভিশন বুক এবং নিয়মিত আপডেট হওয়া ভর্তি ও চাকরির সার্কুলার পাওয়া যায়।',
                ],
            ])
            : [
                'title' => $page->slug === 'about'
                    ? (app()->getLocale() === 'bn' ? 'Sikhun সম্পর্কে | শেখার আরও ভালো উপায়' : 'About Sikhun | A better way to learn')
                    : ($page->localizedMetaTitle() ?: "{$page->localizedTitle()} | Sikhun.com"),
                'description' => $page->slug === 'about'
                    ? (app()->getLocale() === 'bn'
                        ? 'Sikhun-এ বই, মেন্টর পরিচালিত কোর্স এবং AI-সহায়ক স্টাডি টুল দিয়ে নিজের গতিতে শিখুন।'
                        : 'Discover Sikhun: digital books, mentor-led courses, and thoughtful AI study tools in one welcoming learning space.')
                    : $page->localizedMetaDescription(),
                'og_image' => $seo->socialImage(),
                'canonical' => url('/p/'.$page->slug),
            ];

        $props = [
            'page' => [
                'slug' => $page->slug,
                'title' => $page->localizedTitle(),
                'content' => $page->localizedContent(),
            ],
            'seo' => $pageSeo,
        ];

        return Inertia::render(self::DESIGNED_PAGES[$page->slug] ?? 'Public/CustomPage', $props);
    }
}
