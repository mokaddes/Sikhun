<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchPagePilotSocialPost;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Services\Blog\AiBlogStudioService;
use App\Services\Blog\BlogDeduplicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AiBlogStudioController extends Controller
{
    public function __construct(
        private readonly AiBlogStudioService $studioService,
        private readonly BlogDeduplicator $deduplicator
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Blog/AiStudio', [
            'categories' => BlogCategory::orderBy('sort_order')->orderBy('name_bn')->get(['id', 'name_bn', 'name_en']),
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'min:3', 'max:255'],
            'audience' => ['nullable', 'string', 'max:50'],
            'tone' => ['nullable', 'string', 'max:50'],
        ]);

        $article = $this->studioService->generateArticle(
            $validated['topic'],
            $validated['audience'] ?? 'hsc',
            $validated['tone'] ?? 'educational'
        );

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }

    public function enhance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'action' => ['required', 'in:expand,add_quiz,add_table,add_flowchart'],
            'topic' => ['nullable', 'string'],
        ]);

        $enhancedContent = $this->studioService->enhanceArticle(
            $validated['content'],
            $validated['action'],
            $validated['topic'] ?? ''
        );

        return response()->json([
            'success' => true,
            'content' => $enhancedContent,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:450'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:900'],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'status' => ['required', 'in:draft,published'],
            'featured_image_url' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = Str::slug($validated['slug'] ?: $validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        $validated['origin'] = 'ai_generated';
        $validated['admin_id'] = auth('admin')->id();
        $validated['content_hash'] = $this->deduplicator->hashContent($validated['content']);
        $validated['title_fingerprint'] = $this->deduplicator->fingerprintTitle($validated['title']);

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 220);
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Assign default category if empty
        if (empty($validated['blog_category_id'])) {
            $validated['blog_category_id'] = BlogCategory::first()?->id;
        }

        $post = BlogPost::create($validated);

        if ($post->status === 'published' && SiteSetting::get('pagepilot_auto_publish', true)) {
            DispatchPagePilotSocialPost::dispatch('blog', $post->id);
        }

        return redirect()->route('admin.blog.index')->with('success', 'এআই ব্লগ সফলভাবে তৈরি ও সংরক্ষিত হয়েছে।');
    }
}
