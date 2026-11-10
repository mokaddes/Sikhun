<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AdmissionNotice::published();

        if ($type = $request->input('type')) {
            if (in_array($type, ['public', 'guccho', 'private'], true)) {
                $query->where('university_type', $type);
            }
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('institution_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $notices = $query->orderByDesc('is_featured')
            ->orderBy('application_deadline')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => AdmissionNotice::published()->count(),
            'public' => AdmissionNotice::published()->where('university_type', 'public')->count(),
            'guccho' => AdmissionNotice::published()->where('university_type', 'guccho')->count(),
            'private' => AdmissionNotice::published()->where('university_type', 'private')->count(),
        ];

        return Inertia::render('Public/Admissions/Index', [
            'notices' => $notices,
            'filters' => [
                'type' => $request->input('type', 'all'),
                'search' => $request->input('search', ''),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(string $slug): Response
    {
        $notice = AdmissionNotice::where('slug', $slug)->firstOrFail();
        $notice->increment('views_count');

        $relatedNotices = AdmissionNotice::published()
            ->where('id', '!=', $notice->id)
            ->where(function ($q) use ($notice) {
                $q->where('university_type', $notice->university_type)
                    ->orWhere('institution_name', $notice->institution_name);
            })
            ->latest('published_at')
            ->take(4)
            ->get();

        return Inertia::render('Public/Admissions/Show', [
            'notice' => $notice,
            'relatedNotices' => $relatedNotices,
        ]);
    }
}
