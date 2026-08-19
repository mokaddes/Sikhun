<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\SubmitEssayRequest;
use App\Jobs\GradeEssay;
use App\Models\EssaySubmission;
use App\Services\AccessGrantService;
use Illuminate\Http\JsonResponse;

class EssayController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->essaySubmissions()->latest()->paginate(15));
    }

    public function store(SubmitEssayRequest $request): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted.');

        $submission = $student->essaySubmissions()->create([
            'grading_type' => $request->grading_type,
            'essay_text' => $request->essay_text,
            'status' => 'grading',
        ]);

        GradeEssay::dispatch($submission->id);

        if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return $this->success($submission, 'Grading started', 201);
    }

    public function show(EssaySubmission $essay): JsonResponse
    {
        abort_unless($essay->student_id === auth('sanctum')->id(), 403);

        return $this->success($essay);
    }
}
