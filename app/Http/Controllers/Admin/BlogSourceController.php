<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogScrapeLog;
use App\Models\BlogSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class BlogSourceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Blog/Sources', [
            'sources' => BlogSource::with('defaultCategory:id,name_bn,name_en')
                ->withCount('posts')
                ->orderBy('name')
                ->get(),
            'categories' => BlogCategory::orderBy('sort_order')->orderBy('name_bn')->get(['id', 'name_bn', 'name_en']),
            'logs' => BlogScrapeLog::with('source:id,name,url')
                ->latest()
                ->take(30)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'type' => ['required', 'in:rss,sitemap,html_list'],
            'link_selector' => ['nullable', 'string', 'max:255'],
            'title_selector' => ['nullable', 'string', 'max:255'],
            'content_selector' => ['nullable', 'string', 'max:255'],
            'default_category_id' => ['nullable', 'exists:blog_categories,id'],
            'max_per_run' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        BlogSource::create($data);

        return back()->with('success', 'Blog source added.');
    }

    public function update(Request $request, BlogSource $blogSource): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'type' => ['required', 'in:rss,sitemap,html_list'],
            'link_selector' => ['nullable', 'string', 'max:255'],
            'title_selector' => ['nullable', 'string', 'max:255'],
            'content_selector' => ['nullable', 'string', 'max:255'],
            'default_category_id' => ['nullable', 'exists:blog_categories,id'],
            'max_per_run' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $blogSource->update($data);

        return back()->with('success', 'Blog source updated.');
    }

    public function destroy(BlogSource $blogSource): RedirectResponse
    {
        $blogSource->delete();

        return back()->with('success', 'Blog source removed.');
    }

    public function runNow(BlogSource $blogSource): RedirectResponse
    {
        Artisan::call('blog:scrape', ['--source' => $blogSource->id]);

        $latestLog = BlogScrapeLog::where('blog_source_id', $blogSource->id)->latest('id')->first();

        if ($latestLog && $latestLog->error && $latestLog->drafted === 0) {
            return back()->with('error', "Scrape finished with error: {$latestLog->error}");
        }

        $drafted = $latestLog?->drafted ?? 0;
        $dups = $latestLog?->duplicates ?? 0;
        $blocked = $latestLog?->blocked ?? 0;

        return back()->with('success', "Scrape completed: {$drafted} new draft(s), {$dups} duplicate(s), {$blocked} blocked.");
    }

    public function runAll(): RedirectResponse
    {
        Artisan::call('blog:scrape');

        return back()->with('success', 'All active blog sources scraped.');
    }
}
