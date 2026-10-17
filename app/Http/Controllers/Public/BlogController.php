<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request, SeoService $seo): Response
    {
        $query = BlogPost::published()
            ->with(['category:id,name_bn,name_en,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term);
            });
        }

        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = BlogCategory::where('slug', $request->string('category'))->first();
            if ($activeCategory) {
                $query->where('blog_category_id', $activeCategory->id);
            }
        }

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        return Inertia::render('Public/Blog/Index', [
            'posts' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'filters' => $request->only(['search', 'category']),
            'seo' => $seo->forBlogIndex($activeCategory),
        ]);
    }

    public function category(BlogCategory $category, Request $request, SeoService $seo): Response
    {
        abort_unless($category->is_active, 404);

        $query = BlogPost::published()
            ->where('blog_category_id', $category->id)
            ->with(['category:id,name_bn,name_en,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term);
            });
        }

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        return Inertia::render('Public/Blog/Index', [
            'posts' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'activeCategory' => $category,
            'filters' => [
                'search' => $request->input('search'),
                'category' => $category->slug,
            ],
            'seo' => $seo->forBlogIndex($category),
        ]);
    }

    public function show(BlogPost $post, SeoService $seo): Response
    {
        abort_unless($post->isPublished(), 404);

        $post->increment('view_count');
        $post->load(['category:id,name_bn,name_en,slug']);

        $related = BlogPost::published()
            ->with(['category:id,name_bn,name_en,slug'])
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->orderByDesc('published_at')
            ->take(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'featured_image', 'blog_category_id', 'published_at', 'view_count']);

        if ($related->count() < 3) {
            $excludeIds = $related->pluck('id')->push($post->id)->all();
            $more = BlogPost::published()
                ->with(['category:id,name_bn,name_en,slug'])
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('published_at')
                ->take(3 - $related->count())
                ->get(['id', 'slug', 'title', 'excerpt', 'featured_image', 'blog_category_id', 'published_at', 'view_count']);
            $related = $related->concat($more);
        }

        $featuredBooks = Book::published()
            ->with('author:id,name')
            ->latest()
            ->take(2)
            ->get(['id', 'title', 'slug', 'cover_image', 'price', 'is_free', 'level', 'author_id']);

        $featuredCourses = Course::active()
            ->with('mentor:id,name')
            ->latest()
            ->take(2)
            ->get(['id', 'title', 'slug', 'cover_image', 'price', 'level', 'mentor_id']);

        return Inertia::render('Public/Blog/Show', [
            'post' => $post,
            'related' => $related,
            'featuredBooks' => $featuredBooks,
            'featuredCourses' => $featuredCourses,
            'seo' => $seo->forBlogPost($post),
        ]);
    }
}
