<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmitEssayRequest;
use App\Jobs\GradeEssay;
use App\Models\EssaySubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EssayController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Essays/Index', [
            'essays' => $student->essaySubmissions()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Student/Essays/Create');
    }

    public function store(SubmitEssayRequest $request): RedirectResponse
    {
        $student = auth('web')->user();

        $submission = $student->essaySubmissions()->create([
            'grading_type' => $request->grading_type,
            'essay_text' => $request->essay_text,
            'status' => 'grading',
        ]);

        GradeEssay::dispatch($submission->id);

        if (! app(\App\Services\AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return redirect()->route('essays.show', $submission);
    }

    public function retry(EssaySubmission $essay): RedirectResponse
    {
        $this->authorizeEssay($essay);
        abort_unless($essay->status === 'failed', 409, 'Only failed essay grading can be retried.');

        $claimed = EssaySubmission::query()
            ->whereKey($essay->id)
            ->where('status', 'failed')
            ->update(['status' => 'grading', 'result' => null]);
        abort_unless($claimed === 1, 409, 'This essay has already been retried.');

        try {
            GradeEssay::dispatch($essay->id);
        } catch (\Throwable $exception) {
            $essay->update(['status' => 'failed']);
            throw $exception;
        }

        return redirect()->route('essays.show', $essay);
    }

    public function show(EssaySubmission $essay): Response
    {
        $this->authorizeEssay($essay);

        return Inertia::render('Student/Essays/Show', ['essay' => $essay]);
    }

    public function status(EssaySubmission $essay): JsonResponse
    {
        $this->authorizeEssay($essay);

        return response()->json(['status' => $essay->status, 'result' => $essay->result]);
    }

    private function authorizeEssay(EssaySubmission $essay): void
    {
        abort_unless($essay->student_id === Auth::guard('web')->id(), 403);
    }
}
