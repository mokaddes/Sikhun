<?php

namespace App\Http\Controllers\Api;

use App\Mail\ContactMessageMail;
use App\Models\AdmissionNotice;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Book;
use App\Models\Course;
use App\Models\JobCircular;
use App\Models\Plan;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublicContentController extends BaseApiController
{
    public function home(): JsonResponse
    {
        return $this->success([
            'featured_books' => Book::published()
                ->with(['author:id,name', 'category:id,name'])
                ->latest()
                ->take(8)
                ->get(),
            'featured_courses' => Course::active()
                ->with(['mentor:id,name', 'category:id,name'])
                ->latest()
                ->take(6)
                ->get(),
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->orderBy('price_monthly')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->take(6)->get(),
            'latest_blogs' => BlogPost::published()
                ->with('category:id,name_bn,name_en,slug')
                ->orderByDesc('published_at')
                ->take(4)
                ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'blog_category_id', 'published_at', 'reading_time_minutes']),
            'latest_admissions' => AdmissionNotice::published()
                ->orderByDesc('is_featured')
                ->orderBy('application_deadline')
                ->take(4)
                ->get(),
            'latest_jobs' => JobCircular::where('title', 'not like', '%admission%')
                ->where('title', 'not like', '%ভর্তি%')
                ->orderByDesc('is_featured')
                ->orderBy('deadline')
                ->take(4)
                ->get(),
        ]);
    }

    public function contact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $to = SiteSetting::get('contact_email') ?: config('mail.from.address');
        if ($to) {
            try {
                Mail::to($to)->send(new ContactMessageMail($validated));
            } catch (\Throwable $e) {
                Log::warning('Contact email failed', ['error' => $e->getMessage()]);
            }
        }

        return $this->success(null, 'Your message has been sent.');
    }

    public function blogIndex(Request $request): JsonResponse
    {
        $query = BlogPost::published()
            ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%'.trim((string) $request->input('search')).'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term);
            });
        }

        if ($request->filled('tag')) {
            $tag = BlogTag::where('slug', $request->string('tag'))->first();
            if ($tag) {
                $query->whereHas('tags', fn ($q) => $q->whereKey($tag->id));
            }
        }

        if ($request->filled('category')) {
            $cat = BlogCategory::where('slug', $request->string('category'))->first();
            if ($cat) {
                $query->where('blog_category_id', $cat->id);
            }
        }

        $posts = $query->paginate(12);
        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        return $this->success([
            'posts' => $posts->items(),
            'pagination' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
            ],
            'categories' => $categories,
        ]);
    }

    public function blogCategory(BlogCategory $category, Request $request): JsonResponse
    {
        abort_unless($category->is_active, 404);
        $request->merge(['category' => $category->slug]);

        return $this->blogIndex($request);
    }

    public function blogShow(BlogPost $post): JsonResponse
    {
        abort_unless($post->isPublished(), 404);

        $post->increment('view_count');
        $post->load(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug']);

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->orderByDesc('published_at')
            ->take(4)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'reading_time_minutes']);

        return $this->success([
            'post' => $post,
            'related_posts' => $related,
        ]);
    }

    public function jobIndex(Request $request): JsonResponse
    {
        $query = JobCircular::query()->where(function ($q) {
            $q->where('title', 'not like', '%admission%')
                ->where('title', 'not like', '%ভর্তি%')
                ->where('slug', 'not like', '%admission%')
                ->where('slug', 'not like', '%coursework%');
        });

        if (($cat = $request->input('category')) && $cat !== 'all') {
            $query->where('category', $cat);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderByDesc('is_featured')
            ->orderByRaw('CASE WHEN deadline >= CURRENT_DATE THEN 0 ELSE 1 END ASC')
            ->orderBy('deadline')
            ->paginate(12);

        return $this->success($jobs);
    }

    public function jobShow(string $slug): JsonResponse
    {
        $job = JobCircular::where('slug', $slug)->firstOrFail();
        $job->increment('views_count');

        $relatedJobs = JobCircular::where('id', '!=', $job->id)
            ->where('title', 'not like', '%admission%')
            ->where('title', 'not like', '%ভর্তি%')
            ->where(function ($q) use ($job) {
                $q->where('category', $job->category)
                    ->orWhere('organization', $job->organization);
            })
            ->latest('published_date')
            ->take(4)
            ->get();

        return $this->success([
            'job' => $job,
            'related_jobs' => $relatedJobs,
        ]);
    }

    public function admissionIndex(Request $request): JsonResponse
    {
        $query = AdmissionNotice::published();

        if (($type = $request->input('type')) && in_array($type, ['public', 'guccho', 'private'], true)) {
            $query->where('university_type', $type);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('institution_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $notices = $query->orderByDesc('is_featured')
            ->orderBy('application_deadline')
            ->paginate(12);

        return $this->success($notices);
    }

    public function admissionShow(string $slug): JsonResponse
    {
        $notice = AdmissionNotice::where('slug', $slug)->firstOrFail();
        $notice->increment('views_count');

        $relatedNotices = AdmissionNotice::published()
            ->where('id', '!=', $notice->id)
            ->where(function ($q) use ($notice) {
                $q->where('university_type', $notice->university_type)
                    ->orWhere('institution_name', $notice->institution_name);
            })
            ->latest('published_at')
            ->take(4)
            ->get();

        return $this->success([
            'notice' => $notice,
            'related_notices' => $relatedNotices,
        ]);
    }
}
