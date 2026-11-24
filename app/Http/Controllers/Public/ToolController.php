<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class ToolController extends Controller
{
    public const SLUG_MAP = [
        'formula-sheet' => 'formulas',
        'gpa-calculator' => 'gpa',
        'study-routine' => 'routine',
        'photo-resizer' => 'resizer',
        'age-calculator' => 'age',
        'negative-marking' => 'negative_marking',
        'image-to-pdf' => 'image_to_pdf',
        'pomodoro-timer' => 'pomodoro',
    ];

    public function __construct(
        private readonly SeoService $seo
    ) {}

    public function tools(?string $toolSlug = null): Response
    {
        $initialTool = 'catalog';
        if ($toolSlug !== null) {
            abort_unless(isset(self::SLUG_MAP[$toolSlug]), 404);
            $initialTool = self::SLUG_MAP[$toolSlug];
        }

        return Inertia::render('Public/Tools/Index', [
            'initialTool' => $initialTool,
            'recentJobs' => JobCircular::where('deadline', '>=', now())
                ->latest('published_date')
                ->take(3)
                ->get(['id', 'title', 'slug', 'organization', 'deadline']),
            'seo' => $toolSlug
                ? $this->seo->forSpecificTool($toolSlug)
                : $this->seo->forToolsIndex(),
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
            'seo' => $this->seo->forResults(),
        ]);
    }
}
