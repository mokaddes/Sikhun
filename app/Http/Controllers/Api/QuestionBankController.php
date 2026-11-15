<?php

namespace App\Http\Controllers\Api;

use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionBankController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $student = auth('sanctum')->user();
        $studentType = strtolower($student?->type ?? 'hsc');

        $segment = $request->input('segment');
        if (!in_array($segment, ['admission', 'job', 'ssc', 'hsc', 'university'], true)) {
            $segment = ($studentType === 'job') ? 'job' : 'admission';
        }

        $query = QuestionBank::query()
            ->with(['subject:id,name,slug', 'topic:id,name,slug'])
            ->published()
            ->where('segment', $segment)
            ->latest('id');

        // Access control by student type
        $query->where(function ($q) use ($studentType) {
            $q->whereNull('allowed_student_types')
              ->orWhereJsonContains('allowed_student_types', 'all')
              ->orWhereJsonContains('allowed_student_types', $studentType);
        });

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('origin_tag', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('exam_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $query->whereHas('subject', fn ($q) => $q->where('slug', $request->input('subject')));
        }

        if ($request->filled('origin')) {
            $query->where('origin_tag', $request->input('origin'));
        }

        if ($request->filled('year')) {
            $query->where('year', (int) $request->input('year'));
        }

        $questions = $query->paginate(20);

        return $this->success($questions);
    }

    public function show(QuestionBank $question): JsonResponse
    {
        abort_unless($question->is_published, 404);

        $question->load(['subject', 'topic']);
        return $this->success($question);
    }

    public function filters(Request $request): JsonResponse
    {
        $segment = $request->input('segment', 'admission');

        $subjects = QuestionSubject::where('segment', $segment)
            ->withCount(['questions' => fn ($q) => $q->published()])
            ->get();

        $origins = QuestionBank::published()
            ->where('segment', $segment)
            ->whereNotNull('origin_tag')
            ->distinct()
            ->pluck('origin_tag');

        $years = QuestionBank::published()
            ->where('segment', $segment)
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return $this->success([
            'subjects' => $subjects,
            'origins' => $origins,
            'years' => $years,
        ]);
    }
}
