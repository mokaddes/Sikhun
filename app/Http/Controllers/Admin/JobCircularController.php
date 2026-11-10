<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobCircularRequest;
use App\Jobs\DispatchPagePilotSocialPost;
use App\Models\JobCircular;
use App\Services\Jobs\JobScraperService;
use App\Services\Social\PagePilotSocialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class JobCircularController extends Controller
{
    public function index(Request $request): Response
    {
        $query = JobCircular::query()->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') {
                $query->where('deadline', '>=', now()->toDateString());
            } elseif ($request->string('status') === 'expired') {
                $query->where('deadline', '<', now()->toDateString());
            }
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('organization', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('category', 'like', $term);
            });
        }

        $stats = [
            'total' => JobCircular::count(),
            'active' => JobCircular::where('deadline', '>=', now()->toDateString())->count(),
            'featured' => JobCircular::where('is_featured', true)->count(),
            'total_views' => (int) JobCircular::sum('views_count'),
        ];

        return Inertia::render('Admin/Jobs/Index', [
            'jobs' => $query->paginate(15)->withQueryString(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'category', 'status', 'is_featured']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Jobs/Form', [
            'job' => null,
        ]);
    }

    public function store(JobCircularRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            if (JobCircular::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $data['slug'] . '-' . Str::random(5);
            }
        }
        $data['is_featured'] = $request->boolean('is_featured');
        if (empty($data['published_date'])) {
            $data['published_date'] = now()->toDateString();
        }

        $job = JobCircular::create($data);

        DispatchPagePilotSocialPost::dispatch('job', $job->id);

        return redirect()->route('admin.jobs.index')->with('success', 'চাকরির সার্কুলার সফলভাবে তৈরি করা হয়েছে।');
    }

    public function edit(JobCircular $job): Response
    {
        return Inertia::render('Admin/Jobs/Form', [
            'job' => $job,
        ]);
    }

    public function update(JobCircularRequest $request, JobCircular $job): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            if (JobCircular::where('slug', $data['slug'])->where('id', '!=', $job->id)->exists()) {
                $data['slug'] = $data['slug'] . '-' . Str::random(5);
            }
        }
        $data['is_featured'] = $request->boolean('is_featured');

        $job->update($data);

        return redirect()->route('admin.jobs.index')->with('success', 'চাকরির সার্কুলার সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(JobCircular $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'চাকরির সার্কুলার ডিলিট করা হয়েছে।');
    }

    public function toggleFeatured(JobCircular $job): RedirectResponse
    {
        $job->update(['is_featured' => ! $job->is_featured]);

        $status = $job->is_featured ? 'ফিচার্ড করা হয়েছে' : 'ফিচার্ড থেকে রিমুভ করা হয়েছে';

        return back()->with('success', "সার্কুলারটি সফলভাবে {$status}।");
    }

    public function scrapeNow(JobScraperService $scraper): RedirectResponse
    {
        $importedCount = $scraper->scrapeAll();

        return back()->with('success', "স্ক্র্যাপ সম্পন্ন হয়েছে! মোট {$importedCount}টি নতুন সার্কুলার ডাটাবেজে সংরক্ষিত ও হালনাগাদ করা হয়েছে।");
    }

    public function shareToFacebook(JobCircular $job, PagePilotSocialService $social): RedirectResponse
    {
        $result = $social->publishJobCircular($job);

        if ($result['success'] ?? false) {
            return back()->with('success', 'ফেসবুক পেজে সফলভাবে সার্কুলার ও প্রথম কমেন্ট প্রকাশিত হয়েছে!');
        }

        return back()->with('error', 'ফেসবুক পোস্টে ব্যর্থ হয়েছে: ' . ($result['message'] ?? 'অজানা ত্রুটি'));
    }
}
