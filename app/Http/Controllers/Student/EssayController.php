<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmitEssayRequest;
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

        \App\Jobs\GradeEssay::dispatch($submission->id);
        $student->increment('ai_trial_minutes_used');

        return redirect()->route('essays.show', $submission);
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
