<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Services\AccessGrantService;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class QuestionBankPracticeController extends Controller
{
    /**
     * Display student question bank practice index with filters and counts.
     */
    public function index(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $student = auth('web')->user();
        $studentType = $student->type ?? 'admission';

        // Available sector counts in QuestionBank
        $sectorCounts = [
            'admission' => QuestionBank::published()->where('segment', 'admission')->count(),
            'job' => QuestionBank::published()->where('segment', 'job')->count(),
            'hsc' => QuestionBank::published()->where('segment', 'hsc')->count(),
            'ssc' => QuestionBank::published()->where('segment', 'ssc')->count(),
        ];

        // Determine active sector strictly based on student education goal when logged in
        if ($student) {
            if ($studentType === 'job') {
                $activeSector = 'job';
            } elseif ($studentType === 'admission') {
                $activeSector = 'admission';
            } elseif (in_array($studentType, ['hsc', 'ssc'])) {
                $activeSector = $studentType;
            } else {
                $activeSector = 'admission';
            }

            // If user requested another sector via query string, strip it to maintain strict goal isolation
            if ($request->filled('sector') && $request->input('sector') !== $activeSector) {
                return redirect()->route('student.question-bank.index', $request->except('sector'));
            }
        } else {
            $requestedSector = $request->input('sector');
            $activeSector = ($requestedSector && in_array($requestedSector, ['admission', 'job', 'hsc', 'ssc']))
                ? $requestedSector
                : 'admission';
        }

        $query = QuestionBank::query()
            ->with(['subject', 'topic'])
            ->published()
            ->where('segment', $activeSector); // Strictly isolated by sector

        $mode = $request->input('mode');

        if ($mode === 'exam') {
            if ($request->filled('origin_tag')) {
                $query->where('origin_tag', $request->input('origin_tag'));
            } elseif ($request->filled('exam_name')) {
                $query->where('exam_name', $request->input('exam_name'));
            }
        } elseif ($mode === 'subject') {
            if ($request->filled('subject_id')) {
                $query->where('subject_id', (int) $request->input('subject_id'));
            }
            if ($request->filled('topic_id')) {
                $query->where('topic_id', (int) $request->input('topic_id'));
            }
        } else {
            if ($request->filled('origin_tag')) {
                $query->where('origin_tag', $request->input('origin_tag'));
            }
            if ($request->filled('subject_id')) {
                $query->where('subject_id', (int) $request->input('subject_id'));
            }
        }

        // Support search query for questions
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('origin_tag', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('exam_name', 'like', "%{$search}%");
            });
        }

        $questions = $query->latest('id')->paginate(20)->withQueryString();

        // Available subjects for the active sector
        $subjects = QuestionSubject::active()
            ->where(function ($q) use ($activeSector) {
                $q->where('segment', $activeSector)
                  ->orWhere('segment', 'both');
            })
            ->whereHas('questions', function ($q) use ($activeSector) {
                $q->published()->where('segment', $activeSector);
            })
            ->withCount(['questions' => function ($q) use ($activeSector) {
                $q->published()->where('segment', $activeSector);
            }])
            ->orderByDesc('questions_count')
            ->get(['id', 'name_bn', 'name_en', 'slug', 'segment', 'icon', 'order']);

        // Available exams for the active sector distinct by origin_tag, exam_name, institution, year with counts
        $exams = QuestionBank::published()
            ->where('segment', $activeSector)
            ->whereNotNull('origin_tag')
            ->selectRaw('origin_tag, exam_name, institution, year, count(*) as count')
            ->groupBy('origin_tag', 'exam_name', 'institution', 'year')
            ->having('count', '>', 0)
            ->orderByDesc('year')
            ->orderByDesc('count')
            ->get();

        // Check if student is a paid subscriber
        $isPaid = (bool) ($student?->activeSubscription || ($student && app(AccessGrantService::class)->hasActiveAccess($student)));

        return Inertia::render('Student/QuestionBank/Index', [
            'questions' => $questions,
            'subjects' => $subjects,
            'exams' => $exams,
            'filters' => array_merge($request->only(['mode', 'subject_id', 'origin_tag', 'search']), [
                'sector' => $activeSector,
            ]),
            'isPaid' => $isPaid,
            'studentType' => $studentType,
            'lockedToGoal' => (bool) $student,
            'studentGoal' => $student?->type,
            'activeSector' => $activeSector,
            'sectorCounts' => $sectorCounts,
        ]);
    }

    /**
     * Fetch or auto-generate AI explanation for a question.
     */
    public function aiExplain(Request $request, int $id): JsonResponse
    {
        $question = QuestionBank::with('subject')->findOrFail($id);

        // If question already has an explanation in meta['ai_explanation'] or in explanation field, return it.
        if (!empty($question->meta['ai_explanation'])) {
            return response()->json([
                'success' => true,
                'explanation' => $question->meta['ai_explanation'],
            ]);
        }

        if (!empty($question->explanation)) {
            return response()->json([
                'success' => true,
                'explanation' => $question->explanation,
            ]);
        }

        $explanation = app(QuestionAiStructuringService::class)->generateAiExplanation(
            $question->question_text,
            $question->options ?? [],
            $question->correct_option ?? '',
            $question->subject?->name_bn
        );

        $meta = $question->meta ?? [];
        $meta['ai_explanation'] = $explanation;
        $question->meta = $meta;
        $question->save();

        return response()->json([
            'success' => true,
            'explanation' => $explanation,
        ]);
    }

    /**
     * Generate custom mock test from QuestionBank.
     */
    public function generateTest(Request $request): JsonResponse
    {
        $student = auth('web')->user();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        // Check paid access
        if (!$student->activeSubscription && !app(AccessGrantService::class)->hasActiveAccess($student)) {
            return response()->json([
                'success' => false,
                'requires_upgrade' => true,
                'message' => 'প্রশ্নব্যাংক থেকে আনলিমিটেড মক টেস্ট তৈরি করতে Sikhun প্রিমিয়াম প্ল্যানে আপগ্রেড করুন।'
            ], 403);
        }

        // Validate params
        $validated = $request->validate([
            'subject_id' => ['nullable', 'integer'],
            'origin_tag' => ['nullable', 'string'],
            'question_count' => ['nullable', 'integer', 'min:5', 'max:100'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'negative_marking' => ['nullable', 'boolean'],
        ]);

        $count = (int) ($validated['question_count'] ?? 20);
        $durationMinutes = (int) ($validated['duration_minutes'] ?? 20);
        $negativeMarking = $request->has('negative_marking') ? $request->boolean('negative_marking') : true;
        $studentType = $student->type ?? 'admission';

        // Query random questions matching criteria and student type
        $query = QuestionBank::query()
            ->with('subject')
            ->published();

        $query->where(function ($q) use ($studentType) {
            $q->where('segment', $studentType)
              ->orWhereNull('allowed_student_types')
              ->orWhereJsonContains('allowed_student_types', 'all')
              ->orWhereJsonContains('allowed_student_types', $studentType);
        });

        $subjectName = null;
        if (!empty($validated['subject_id'])) {
            $query->where('subject_id', (int) $validated['subject_id']);
            $subject = QuestionSubject::find($validated['subject_id']);
            $subjectName = $subject?->name_bn ?? $subject?->name_en;
        }

        $examName = null;
        if (!empty($validated['origin_tag'])) {
            $query->where('origin_tag', $validated['origin_tag']);
            $firstMatched = (clone $query)->first();
            $examName = $firstMatched?->exam_name ?? $validated['origin_tag'];
        }

        $questions = $query->inRandomOrder()->limit($count)->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'নির্বাচিত ফিল্টারে কোনো প্রশ্ন পাওয়া যায়নি।'
            ], 422);
        }

        $formattedQuestions = $questions->values()->map(function ($q, $index) {
            $options = $q->options ?? [];
            $optionTexts = [];
            $keys = [];
            $correctAnswerText = null;

            if (is_array($options)) {
                foreach ($options as $opt) {
                    if (is_array($opt)) {
                        $key = $opt['key'] ?? '';
                        $text = $opt['text'] ?? '';
                        $keys[] = $key;
                        $optionTexts[] = $text;
                        if (strtoupper(trim($key)) === strtoupper(trim($q->correct_option ?? ''))) {
                            $correctAnswerText = $text;
                        }
                    } else {
                        $optionTexts[] = (string) $opt;
                    }
                }
            }

            if (!$correctAnswerText) {
                $correctAnswerText = $q->correct_option_text ?? $q->correct_option;
            }

            return [
                'id' => $index + 1,
                'question_bank_id' => $q->id,
                'question' => $q->question_text,
                'question_text' => $q->question_text,
                'type' => $q->question_type ?? 'mcq',
                'keys' => !empty($keys) ? $keys : ['A', 'B', 'C', 'D'],
                'options' => !empty($optionTexts) ? $optionTexts : $options,
                'options_data' => $options,
                'correct_option' => $q->correct_option,
                'correct_answer' => $correctAnswerText,
                'marks' => (float) ($q->marks ?? 1.0),
                'negative_marks' => (float) ($q->negative_marks ?? 0.25),
                'explanation' => $q->meta['ai_explanation'] ?? $q->explanation,
                'subject_id' => $q->subject_id,
                'origin_tag' => $q->origin_tag,
                'exam_name' => $q->exam_name,
            ];
        })->all();

        $title = "কাস্টম মক টেস্ট - " . ($subjectName ?? $examName ?? 'প্রশ্নব্যাংক');

        $session = ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'exam',
            'title' => $title,
            'source_type' => 'topic',
            'config' => [
                'title' => $title,
                'type' => 'mcq',
                'count' => count($formattedQuestions),
                'duration' => $durationMinutes,
                'duration_seconds' => $durationMinutes * 60,
                'mode' => 'exam',
                'negative_marking' => $negativeMarking,
                'subject_id' => $validated['subject_id'] ?? null,
                'origin_tag' => $validated['origin_tag'] ?? null,
            ],
            'questions' => $formattedQuestions,
            'total' => count($formattedQuestions),
            'duration_seconds' => $durationMinutes * 60,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $redirectUrl = Route::has('exams.take')
            ? route('exams.take', $session->id)
            : route('exams.show', $session->id);

        return response()->json([
            'success' => true,
            'exam_id' => $session->id,
            'redirect_url' => $redirectUrl,
        ]);
    }
}
