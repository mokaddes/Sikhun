<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogBlocklist;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogSource;
use App\Models\BlogTag;
use App\Services\Blog\BlogBlockService;
use App\Services\Blog\BlogDeduplicator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function __construct(
        private readonly BlogDeduplicator $deduplicator,
        private readonly BlogBlockService $blockService
    ) {}

    public function index(Request $request): Response
    {
        $query = BlogPost::with(['category:id,name_bn,name_en,slug', 'source:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->string('origin'));
        }

        if ($request->filled('category_id')) {
            $query->where('blog_category_id', $request->integer('category_id'));
        }

        if ($request->filled('source_id')) {
            $query->where('blog_source_id', $request->integer('source_id'));
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('source_title', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $query->paginate(15)->withQueryString(),
            'categories' => BlogCategory::orderBy('sort_order')->orderBy('name_bn')->get(['id', 'name_bn', 'name_en']),
            'sources' => BlogSource::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['status', 'origin', 'category_id', 'source_id', 'search']),
            'counts' => [
                'draft' => BlogPost::where('status', 'draft')->count(),
                'published' => BlogPost::where('status', 'published')->count(),
                'blocked' => BlogBlocklist::count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Blog/Form', [
            'post' => null,
            'categories' => BlogCategory::orderBy('sort_order')->orderBy('name_bn')->get(['id', 'name_bn', 'name_en']),
            'tags' => BlogTag::orderBy('name_bn')->get(['id', 'name_bn', 'name_en', 'slug']),
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image', 'remove_featured_image', 'tags']);
        $tagIds = $request->validated('tags', []) ?? [];

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog/covers', 'public');
        }

        $data['origin'] = 'manual';
        $data['admin_id'] = auth('admin')->id();
        $data['content_hash'] = $this->deduplicator->hashContent($data['content']);
        $data['title_fingerprint'] = $this->deduplicator->fingerprintTitle($data['title']);

        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(trim(strip_tags($data['content'])), 220);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);
        $post->tags()->sync($tagIds);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $blog): Response
    {
        return Inertia::render('Admin/Blog/Form', [
            'post' => $blog->load(['source:id,name', 'tags:id,name_bn,name_en,slug']),
            'categories' => BlogCategory::orderBy('sort_order')->orderBy('name_bn')->get(['id', 'name_bn', 'name_en']),
            'tags' => BlogTag::orderBy('name_bn')->get(['id', 'name_bn', 'name_en', 'slug']),
        ]);
    }

    public function update(BlogPostRequest $request, BlogPost $blog): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image', 'remove_featured_image', 'tags']);
        $tagIds = $request->validated('tags', []) ?? [];

        if ($request->boolean('remove_featured_image') && $blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
            $data['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog/covers', 'public');
        }

        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(trim(strip_tags($data['content'])), 220);
        }

        if ($data['status'] === 'published' && ! $blog->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blog->update($data);
        $blog->tags()->sync($tagIds);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
    }

    public function togglePublish(BlogPost $blog): RedirectResponse
    {
        if ($blog->status === 'published') {
            $blog->update(['status' => 'draft']);

            return back()->with('success', 'Blog post moved to draft.');
        }

        $blog->update([
            'status' => 'published',
            'published_at' => $blog->published_at ?? now(),
        ]);

        return back()->with('success', 'Blog post published.');
    }

    public function block(BlogPost $blog): RedirectResponse
    {
        $this->blockService->blockPost($blog, auth('admin')->id(), 'Blocked by admin');

        return back()->with('success', 'Blog post blocked and added to blocklist.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        // Decision: deleting an auto-scraped blog post automatically blocks it
        // so the scraper will skip it on future runs.
        if ($blog->origin === 'auto' || $blog->source_url) {
            $this->blockService->blockPost($blog, auth('admin')->id(), 'Auto-blocked on delete');

            return back()->with('success', 'Auto blog deleted and added to blocklist so it will not be scraped again.');
        }

        $blog->delete();

        return back()->with('success', 'Blog post deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:blog_posts,id'],
            'action' => ['required', 'in:publish,draft,delete,block'],
        ]);

        $posts = BlogPost::whereIn('id', $validated['ids'])->get();
        $adminId = auth('admin')->id();

        foreach ($posts as $post) {
            match ($validated['action']) {
                'publish' => $post->update([
                    'status' => 'published',
                    'published_at' => $post->published_at ?? now(),
                ]),
                'draft' => $post->update(['status' => 'draft']),
                'block' => $this->blockService->blockPost($post, $adminId, 'Bulk blocked by admin'),
                'delete' => ($post->origin === 'auto' || $post->source_url)
                    ? $this->blockService->blockPost($post, $adminId, 'Auto-blocked on bulk delete')
                    : $post->delete(),
            };
        }

        return back()->with('success', 'Bulk action completed.');
    }
}
