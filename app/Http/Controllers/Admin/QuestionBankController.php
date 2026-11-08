<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionSource;
use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionBankController extends Controller
{
    public function __construct(
        protected QuestionAiStructuringService $aiStructuringService
    ) {}

    /**
     * Display question bank list with comprehensive filtering.
     */
    public function index(Request $request): Response
    {
        $query = QuestionBank::query()
            ->with(['subject', 'topic', 'source'])
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

        if ($request->filled('segment') && $request->input('segment') !== 'all') {
            $query->where('segment', $request->input('segment'));
        }

        if ($request->filled('subject_id') && $request->input('subject_id') !== 'all') {
            $query->where('subject_id', $request->input('subject_id'));
        }

        if ($request->filled('origin_tag') && $request->input('origin_tag') !== 'all') {
            $query->where('origin_tag', $request->input('origin_tag'));
        }

        if ($request->filled('difficulty') && $request->input('difficulty') !== 'all') {
            $query->where('difficulty', $request->input('difficulty'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $questions = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => QuestionBank::count(),
            'admission' => QuestionBank::where('segment', 'admission')->count(),
            'job' => QuestionBank::where('segment', 'job')->count(),
            'verified' => QuestionBank::where('verification_status', 'verified')->count(),
        ];

        $subjects = QuestionSubject::active()->get(['id', 'name_bn', 'name_en', 'segment', 'slug']);
        $originTags = QuestionBank::distinct()->pluck('origin_tag')->filter()->values();
        $years = QuestionBank::distinct()->orderByDesc('year')->pluck('year')->filter()->values();

        return Inertia::render('Admin/Questions/Bank/Index', [
            'questions' => $questions,
            'stats' => $stats,
            'subjects' => $subjects,
            'originTags' => $originTags,
            'years' => $years,
            'filters' => $request->only(['search', 'segment', 'subject_id', 'origin_tag', 'difficulty', 'status', 'year']),
        ]);
    }

    /**
     * Show form to create a new question.
     */
    public function create(): Response
    {
        $subjects = QuestionSubject::with('topics')->active()->get();
        $sources = QuestionSource::active()->get(['id', 'name', 'institution']);

        return Inertia::render('Admin/Questions/Bank/Form', [
            'question' => null,
            'subjects' => $subjects,
            'sources' => $sources,
        ]);
    }

    /**
     * Store new question.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'segment' => ['required', 'string', 'in:admission,job'],
            'subject_id' => ['required', 'exists:question_subjects,id'],
            'topic_id' => ['nullable', 'exists:question_topics,id'],
            'topic_name' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'exam_name' => ['nullable', 'string', 'max:255'],
            'origin_tag' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2035'],
            'session' => ['nullable', 'string', 'max:50'],
            'shift_or_set' => ['nullable', 'string', 'max:50'],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'string', 'in:mcq,written,true_false'],
            'options' => ['required_if:question_type,mcq', 'array', 'min:2'],
            'options.*.key' => ['required', 'string'],
            'options.*.text' => ['required', 'string'],
            'correct_option' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'marks' => ['required', 'numeric', 'min:0'],
            'negative_marks' => ['required', 'numeric', 'min:0'],
            'difficulty' => ['required', 'string', 'in:easy,medium,hard'],
            'source_id' => ['nullable', 'exists:question_sources,id'],
            'status' => ['required', 'string', 'in:published,pending_review,draft,archived'],
        ]);

        $validated['origin_tag'] = strtoupper(trim($validated['origin_tag']));
        $validated['correct_option'] = strtoupper(trim($validated['correct_option']));
        $validated['verification_status'] = 'verified';
        $validated['verified_at'] = now();

        QuestionBank::create($validated);

        return redirect()->route('admin.question-bank.index')->with('success', 'প্রশ্নটি সফলভাবে প্রশ্ন ব্যাংকে যুক্ত করা হয়েছে।');
    }

    /**
     * Show form to edit an existing question.
     */
    public function edit(QuestionBank $questionBank): Response
    {
        $subjects = QuestionSubject::with('topics')->active()->get();
        $sources = QuestionSource::active()->get(['id', 'name', 'institution']);

        return Inertia::render('Admin/Questions/Bank/Form', [
            'question' => $questionBank,
            'subjects' => $subjects,
            'sources' => $sources,
        ]);
    }

    /**
     * Update an existing question.
     */
    public function update(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $validated = $request->validate([
            'segment' => ['required', 'string', 'in:admission,job'],
            'subject_id' => ['required', 'exists:question_subjects,id'],
            'topic_id' => ['nullable', 'exists:question_topics,id'],
            'topic_name' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'exam_name' => ['nullable', 'string', 'max:255'],
            'origin_tag' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2035'],
            'session' => ['nullable', 'string', 'max:50'],
            'shift_or_set' => ['nullable', 'string', 'max:50'],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'string', 'in:mcq,written,true_false'],
            'options' => ['required_if:question_type,mcq', 'array', 'min:2'],
            'options.*.key' => ['required', 'string'],
            'options.*.text' => ['required', 'string'],
            'correct_option' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'marks' => ['required', 'numeric', 'min:0'],
            'negative_marks' => ['required', 'numeric', 'min:0'],
            'difficulty' => ['required', 'string', 'in:easy,medium,hard'],
            'source_id' => ['nullable', 'exists:question_sources,id'],
            'status' => ['required', 'string', 'in:published,pending_review,draft,archived'],
        ]);

        $validated['origin_tag'] = strtoupper(trim($validated['origin_tag']));
        $validated['correct_option'] = strtoupper(trim($validated['correct_option']));

        $questionBank->update($validated);

        return redirect()->route('admin.question-bank.index')->with('success', 'প্রশ্ন তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Delete question.
     */
    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $questionBank->delete();
        return redirect()->back()->with('success', 'প্রশ্নটি সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * AJAX action to auto-generate AI explanation for a question.
     */
    public function aiExplain(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'options' => ['required', 'array'],
            'correct_option' => ['required', 'string'],
            'subject_name' => ['nullable', 'string'],
        ]);

        $explanation = $this->aiStructuringService->generateAiExplanation(
            $validated['question_text'],
            $validated['options'],
            $validated['correct_option'],
            $validated['subject_name'] ?? null
        );

        return response()->json([
            'success' => true,
            'explanation' => $explanation,
        ]);
    }
}
