<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionBankController extends Controller
{
    /**
     * Public, open student Question Bank portal (No login required).
     */
    public function index(Request $request): Response
    {
        $segment = $request->input('segment', 'admission');
        if (!in_array($segment, ['admission', 'job'])) {
            $segment = 'admission';
        }

        $query = QuestionBank::query()
            ->with(['subject', 'topic'])
            ->published()
            ->where('segment', $segment)
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('origin_tag', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('exam_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject') && $request->input('subject') !== 'all') {
            $query->whereHas('subject', function ($q) use ($request) {
                $q->where('slug', $request->input('subject'));
            });
        }

        if ($request->filled('origin') && $request->input('origin') !== 'all') {
            $query->where('origin_tag', $request->input('origin'));
        }

        if ($request->filled('difficulty') && $request->input('difficulty') !== 'all') {
            $query->where('difficulty', $request->input('difficulty'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $questions = $query->paginate(15)->withQueryString();

        $subjects = QuestionSubject::active()
            ->forSegment($segment)
            ->get(['id', 'name_bn', 'name_en', 'slug', 'icon']);

        $origins = QuestionBank::published()
            ->where('segment', $segment)
            ->distinct()
            ->pluck('origin_tag')
            ->filter()
            ->values();

        $years = QuestionBank::published()
            ->where('segment', $segment)
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values();

        $counts = [
            'admission' => QuestionBank::published()->where('segment', 'admission')->count(),
            'job' => QuestionBank::published()->where('segment', 'job')->count(),
        ];

        return Inertia::render('Public/QuestionBank/Index', [
            'questions' => $questions,
            'subjects' => $subjects,
            'origins' => $origins,
            'years' => $years,
            'counts' => $counts,
            'currentSegment' => $segment,
            'filters' => $request->only(['search', 'subject', 'origin', 'difficulty', 'year', 'segment']),
        ]);
    }
}
