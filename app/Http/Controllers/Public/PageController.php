<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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

        return Inertia::render('Public/Home', [
            'stats' => $stats,
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
            'plans' => Plan::where('is_active', true)->orderBy('price_monthly')->get(),
            'testimonials' => Testimonial::published()->get(['student_name', 'student_role', 'avatar', 'quote']),
            'seo' => $seo->forHome(),
        ]);
    }

    public function show(CustomPage $page, SeoService $seo): Response
    {
        abort_unless($page->is_published, 404);

        if ($page->slug === 'contact') {
            abort(404);
        }

        $props = [
            'page' => [
                'slug' => $page->slug,
                'title' => $page->localizedTitle(),
                'content' => $page->localizedContent(),
            ],
            'seo' => [
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
            ],
        ];

        return Inertia::render(self::DESIGNED_PAGES[$page->slug] ?? 'Public/CustomPage', $props);
    }
}
