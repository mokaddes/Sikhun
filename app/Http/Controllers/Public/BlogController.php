<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
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
            ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term);
            });
        }

        $activeTag = $request->filled('tag')
            ? BlogTag::where('slug', $request->string('tag'))->first()
            : null;
        if ($activeTag) {
            $query->whereHas('tags', fn ($q) => $q->whereKey($activeTag->id));
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
        $tags = BlogTag::whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name_bn')
            ->get();

        return Inertia::render('Public/Blog/Index', [
            'posts' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'tags' => $tags,
            'activeTag' => $activeTag,
            'filters' => $request->only(['search', 'category', 'tag']),
            'seo' => $seo->forBlogIndex($activeCategory),
        ]);
    }

    public function category(BlogCategory $category, Request $request, SeoService $seo): Response
    {
        abort_unless($category->is_active, 404);

        $query = BlogPost::published()
            ->where('blog_category_id', $category->id)
            ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term);
            });
        }

        $activeTag = $request->filled('tag')
            ? BlogTag::where('slug', $request->string('tag'))->first()
            : null;
        if ($activeTag) {
            $query->whereHas('tags', fn ($q) => $q->whereKey($activeTag->id));
        }

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();
        $tags = BlogTag::whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name_bn')
            ->get();

        return Inertia::render('Public/Blog/Index', [
            'posts' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'activeCategory' => $category,
            'tags' => $tags,
            'activeTag' => $activeTag,
            'filters' => [
                'search' => $request->input('search'),
                'category' => $category->slug,
                'tag' => $activeTag?->slug,
            ],
            'seo' => $seo->forBlogIndex($category),
        ]);
    }

    public function show(BlogPost $post, SeoService $seo): Response
    {
        abort_unless($post->isPublished(), 404);

        $post->increment('view_count');
        $post->load(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug']);

        $related = collect();
        $tagIds = $post->tags->modelKeys();
        if ($tagIds) {
            $related = BlogPost::published()
                ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
                ->where('id', '!=', $post->id)
                ->whereHas('tags', fn ($q) => $q->whereIn('blog_tags.id', $tagIds))
                ->orderByDesc('published_at')
                ->take(3)
                ->get();
        }

        if ($related->count() < 3 && $post->blog_category_id) {
            $excludeIds = $related->pluck('id')->push($post->id)->all();
            $sameCategory = BlogPost::published()
                ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
                ->where('blog_category_id', $post->blog_category_id)
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('published_at')
                ->take(3 - $related->count())
                ->get();
            $related = $related->concat($sameCategory);
        }

        if ($related->count() < 3) {
            $excludeIds = $related->pluck('id')->push($post->id)->all();
            $more = BlogPost::published()
                ->with(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug'])
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('published_at')
                ->take(3 - $related->count())
                ->get();
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
