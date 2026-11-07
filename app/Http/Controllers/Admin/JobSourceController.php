<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobSource;
use App\Services\Jobs\JobScraperService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobSourceController extends Controller
{
    public function index(JobScraperService $scraper): Response
    {
        $scraper->ensureDefaultSources();

        return Inertia::render('Admin/Jobs/Sources', [
            'sources' => JobSource::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'feed_url' => ['nullable', 'url', 'max:500'],
            'type' => ['required', 'in:rss,web'],
            'default_category' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        JobSource::create($data);

        return back()->with('success', 'নতুন চাকরির স্ক্র্যাপিং সোর্স যুক্ত করা হয়েছে।');
    }

    public function update(Request $request, JobSource $jobSource): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'feed_url' => ['nullable', 'url', 'max:500'],
            'type' => ['required', 'in:rss,web'],
            'default_category' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $jobSource->update($data);

        return back()->with('success', 'স্ক্র্যাপিং সোর্স সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(JobSource $jobSource): RedirectResponse
    {
        $jobSource->delete();

        return back()->with('success', 'স্ক্র্যাপিং সোর্স ডিলিট করা হয়েছে।');
    }

    public function run(JobSource $jobSource, JobScraperService $scraper): RedirectResponse
    {
        $count = $scraper->scrapeSource($jobSource);

        return back()->with('success', "'{$jobSource->name}' সোর্স থেকে মোট {$count}টি সার্কুলার সিঙ্ক করা হয়েছে।");
    }

    public function runAll(JobScraperService $scraper): RedirectResponse
    {
        $count = $scraper->scrapeAll();

        return back()->with('success', "সকল সক্রিয় সোর্স থেকে মোট {$count}টি সার্কুলার সফলভাবে সিঙ্ক করা হয়েছে।");
    }
}
