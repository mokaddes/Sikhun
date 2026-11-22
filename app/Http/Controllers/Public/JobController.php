<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    public function __construct(
        private readonly SeoService $seo
    ) {}

    public function index(Request $request): Response
    {
        $query = JobCircular::query();

        // Exclude admission/academic entrance circulars from the job feed
        $query->where(function ($q) {
            $q->where('title', 'not like', '%admission%')
              ->where('title', 'not like', '%ভর্তি%')
              ->where('slug', 'not like', '%admission%')
              ->where('slug', 'not like', '%coursework%');
        });

        if ($cat = $request->input('category')) {
            if ($cat !== 'all') {
                $query->where('category', $cat);
            }
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
            ->paginate(12)
            ->withQueryString();

        $stats = Cache::remember('job_circular_stats_v1', 600, function () {
            $counts = JobCircular::where('title', 'not like', '%admission%')
                ->where('title', 'not like', '%ভর্তি%')
                ->selectRaw('category, COUNT(*) as cnt')
                ->groupBy('category')
                ->pluck('cnt', 'category');

            return [
                'total' => (int) $counts->sum(),
                'govt' => (int) ($counts['government'] ?? 0),
                'bank' => (int) ($counts['bank'] ?? 0),
                'ngo' => (int) ($counts['ngo'] ?? 0),
                'private' => (int) (($counts['private'] ?? 0) + ($counts['it_tech'] ?? 0)),
            ];
        });

        return Inertia::render('Public/Jobs/Index', [
            'jobs' => $jobs,
            'filters' => [
                'category' => $request->input('category', 'all'),
                'search' => $request->input('search', ''),
            ],
            'stats' => $stats,
            'seo' => $this->seo->forJobIndex(),
        ]);
    }

    public function show(string $slug): Response
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

        return Inertia::render('Public/Jobs/Show', [
            'job' => $job,
            'relatedJobs' => $relatedJobs,
            'seo' => $this->seo->forJobCircular($job),
        ]);
    }
}
