<?php

namespace App\Http\Controllers\Api;

use App\Models\ExamAnswer;
use App\Models\Student;
use App\Services\ExamMistakeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MistakeReviewController extends BaseApiController
{
    public function index(Request $request, ExamMistakeService $mistakeService): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();

        $filters = $request->only(['status', 'subject_id', 'institution', 'search']);
        $data = $mistakeService->getMistakesData($student, $filters);

        return $this->success([
            'mistakes' => $data['mistakes'],
            'subjects' => $data['subjects'],
            'institutions' => $data['institutions'],
            'stats' => $data['stats'],
            'filters' => [
                'status' => $filters['status'] ?? 'unresolved',
                'subject_id' => $filters['subject_id'] ?? '',
                'institution' => $filters['institution'] ?? '',
                'search' => $filters['search'] ?? '',
            ],
        ]);
    }

    public function practice(Request $request, ExamMistakeService $mistakeService): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();

        $validated = $request->validate([
            'subject_id' => ['nullable', 'integer'],
            'subject_name' => ['nullable', 'string'],
            'institution' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        $session = $mistakeService->createMistakePracticeExam($student, $validated, $limit);

        if (! $session) {
            return $this->error('অনুশীলন করার মতো কোনো ভুল প্রশ্ন পাওয়া যায়নি। দারুণ অগ্রগতি!', [], 422);
        }

        return $this->success($session, 'Mistake practice exam created', 201);
    }

    public function reset(Request $request): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();
        $query = ExamAnswer::where('student_id', $student->id)->where('is_correct', false);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->input('subject_id'));
        }

        if ($request->filled('institution')) {
            $query->where('institution', $request->input('institution'));
        }

        $count = $query->delete();

        return $this->success(
            ['deleted_count' => $count],
            "ভুল প্রশ্নের তালিকা থেকে {$count}টি প্রশ্ন সফলভাবে রিসেট করা হয়েছে।"
        );
    }
}
