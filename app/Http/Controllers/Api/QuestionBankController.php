<?php

namespace App\Http\Controllers\Api;

use App\Models\ExamSession;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionBankController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        /** @var Student|null $student */
        $student = auth('sanctum')->user();
        $studentType = $student->type ?? 'admission';

        $sectorCounts = [
            'admission' => QuestionBank::published()->where('segment', 'admission')->count(),
            'job' => QuestionBank::published()->where('segment', 'job')->count(),
            'hsc' => QuestionBank::published()->where('segment', 'hsc')->count(),
            'ssc' => QuestionBank::published()->where('segment', 'ssc')->count(),
        ];

        $validSectors = ['admission', 'job', 'hsc', 'ssc'];
        $requestedSector = $request->input('sector');

        if ($requestedSector && in_array($requestedSector, $validSectors, true)) {
            $activeSector = $requestedSector;
        } elseif ($student && in_array($studentType, $validSectors, true) && ($sectorCounts[$studentType] ?? 0) > 0) {
            $activeSector = $studentType;
        } else {
            $fallbackOrder = $studentType === 'ssc'
                ? ['hsc', 'admission', 'job', 'ssc']
                : ['admission', 'hsc', 'job', 'ssc'];

            $activeSector = 'admission';
            foreach ($fallbackOrder as $candidate) {
                if (($sectorCounts[$candidate] ?? 0) > 0) {
                    $activeSector = $candidate;
                    break;
                }
            }
        }

        $isPaid = (bool) ($student?->activeSubscription || ($student && app(AccessGrantService::class)->hasActiveAccess($student)));

        $query = QuestionBank::query()
            ->with(['subject', 'topic'])
            ->published()
            ->where('segment', $activeSector);

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

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                    ->orWhere('origin_tag', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('exam_name', 'like', "%{$search}%");
            });
        }

        $questions = $query->latest('id')->paginate(20);

        $subjects = QuestionSubject::active()
            ->whereHas('questions', function ($q) use ($activeSector) {
                $q->published()->where('segment', $activeSector);
            })
            ->withCount(['questions' => function ($q) use ($activeSector) {
                $q->published()->where('segment', $activeSector);
            }])
            ->orderByDesc('questions_count')
            ->get(['id', 'name_bn', 'name_en', 'slug', 'segment', 'icon', 'order']);

        $exams = QuestionBank::published()
            ->where('segment', $activeSector)
            ->whereNotNull('origin_tag')
            ->selectRaw('origin_tag, exam_name, institution, year, count(*) as count')
            ->groupBy('origin_tag', 'exam_name', 'institution', 'year')
            ->having('count', '>', 0)
            ->orderByDesc('year')
            ->orderByDesc('count')
            ->get();

        return $this->success([
            'questions' => $questions->items(),
            'pagination' => [
                'total' => $questions->total(),
                'per_page' => $questions->perPage(),
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
            ],
            'subjects' => $subjects,
            'exams' => $exams,
            'filters' => array_merge($request->only(['mode', 'subject_id', 'origin_tag', 'search']), [
                'sector' => $activeSector,
            ]),
            'is_paid' => $isPaid,
            'student_type' => $studentType,
            'locked_to_goal' => false,
            'student_goal' => $student?->type,
            'active_sector' => $activeSector,
            'sector_counts' => $sectorCounts,
            'is_unavailable' => false,
        ]);
    }

    public function aiExplain(int $id): JsonResponse
    {
        $question = QuestionBank::with('subject')->findOrFail($id);

        if (! empty($question->meta['ai_explanation'])) {
            return $this->success(['explanation' => $question->meta['ai_explanation']]);
        }

        if (! empty($question->explanation)) {
            return $this->success(['explanation' => $question->explanation]);
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

        return $this->success(['explanation' => $explanation]);
    }

    public function generateTest(Request $request): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();
        $studentType = $student->type ?? '';

        if (! $student->activeSubscription && ! app(AccessGrantService::class)->hasActiveAccess($student)) {
            return $this->error('প্রশ্নব্যাংক থেকে আনলিমিটেড মক টেস্ট তৈরি করতে Sikhun প্রিমিয়াম প্ল্যানে আপগ্রেড করুন।', ['requires_upgrade' => true], 403);
        }

        $validated = $request->validate([
            'sector' => ['nullable', 'string', 'in:admission,job,hsc,ssc'],
            'subject_id' => ['nullable', 'integer'],
            'origin_tag' => ['nullable', 'string'],
            'question_count' => ['nullable', 'integer', 'min:5', 'max:100'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'negative_marking' => ['nullable', 'boolean'],
        ]);

        $count = (int) ($validated['question_count'] ?? 20);
        $durationMinutes = (int) ($validated['duration_minutes'] ?? 20);
        $negativeMarking = $request->has('negative_marking') ? $request->boolean('negative_marking') : true;

        $segment = $validated['sector'] ?? (in_array($studentType, ['admission', 'job', 'hsc', 'ssc'], true) ? $studentType : null);

        $query = QuestionBank::query()
            ->with('subject')
            ->published();
        if ($segment) {
            $query->where('segment', $segment);
        }

        $subjectName = null;
        if (! empty($validated['subject_id'])) {
            $query->where('subject_id', (int) $validated['subject_id']);
            $subject = QuestionSubject::find($validated['subject_id']);
            $subjectName = $subject?->name_bn ?? $subject?->name_en;
        }

        $examName = null;
        if (! empty($validated['origin_tag'])) {
            $query->where('origin_tag', $validated['origin_tag']);
            $firstMatched = (clone $query)->first();
            $examName = $firstMatched?->exam_name ?? $validated['origin_tag'];
        }

        $questions = $query->inRandomOrder()->limit($count)->get();

        if ($questions->isEmpty()) {
            return $this->error('নির্বাচিত ফিল্টারে কোনো প্রশ্ন পাওয়া যায়নি।', [], 422);
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

            if (! $correctAnswerText) {
                $correctAnswerText = $q->correct_option_text ?? $q->correct_option;
            }

            return [
                'id' => $index + 1,
                'question_bank_id' => $q->id,
                'question' => $q->question_text,
                'question_text' => $q->question_text,
                'type' => $q->question_type ?? 'mcq',
                'keys' => ! empty($keys) ? $keys : ['A', 'B', 'C', 'D'],
                'options' => ! empty($optionTexts) ? $optionTexts : $options,
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

        $title = 'কাস্টম মক টেস্ট - '.($subjectName ?? $examName ?? 'প্রশ্নব্যাংক');

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

        return $this->success($session, 'Custom mock test generated', 201);
    }
}
