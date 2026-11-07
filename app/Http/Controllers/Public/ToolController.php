<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use Inertia\Inertia;
use Inertia\Response;

class ToolController extends Controller
{
    public function tools(): Response
    {
        return Inertia::render('Public/Tools/Index', [
            'recentJobs' => JobCircular::where('deadline', '>=', now())
                ->latest('published_date')
                ->take(3)
                ->get(['id', 'title', 'slug', 'organization', 'deadline']),
        ]);
    }

    public function calculators(): Response
    {
        return $this->tools();
    }

    public function results(): Response
    {
        return Inertia::render('Public/Tools/ResultChecker', [
            'recentJobs' => JobCircular::where('deadline', '>=', now())
                ->latest('published_date')
                ->take(3)
                ->get(['id', 'title', 'slug', 'organization', 'deadline']),
        ]);
    }
}
