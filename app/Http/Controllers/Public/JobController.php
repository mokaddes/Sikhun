<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use App\Services\SeoService;
use Illuminate\Http\Request;
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
            ->orderBy('deadline')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => JobCircular::count(),
            'govt' => JobCircular::where('category', 'government')->count(),
            'bank' => JobCircular::where('category', 'bank')->count(),
            'ngo' => JobCircular::where('category', 'ngo')->count(),
            'private' => JobCircular::whereIn('category', ['private', 'it_tech'])->count(),
        ];

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
