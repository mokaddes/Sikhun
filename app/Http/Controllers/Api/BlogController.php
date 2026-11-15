<?php

namespace App\Http\Controllers\Api;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends BaseApiController
{
    public function index(Request $request): JsonResponse
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

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->input('category')));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->input('tag')));
        }

        $posts = $query->paginate(12);

        return $this->success($posts);
    }

    public function show(BlogPost $post): JsonResponse
    {
        abort_unless($post->status === 'published', 404);

        $post->load(['category:id,name_bn,name_en,slug', 'tags:id,name_bn,name_en,slug', 'author:id,name']);
        $post->increment('view_count');

        return $this->success($post);
    }

    public function categories(): JsonResponse
    {
        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        return $this->success($categories);
    }

    public function tags(): JsonResponse
    {
        $tags = BlogTag::whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name_bn')
            ->get();

        return $this->success($tags);
    }
}
