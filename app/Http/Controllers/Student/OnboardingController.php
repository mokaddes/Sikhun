<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    private const GOALS = ['hsc', 'ssc', 'admission', 'job'];

    public function show(): Response
    {
        $student = Student::findOrFail(auth('web')->id());

        return Inertia::render('Student/Onboarding/Index', [
            'progress' => $student->onboarding_progress ?? [],
            'completedAt' => $student->onboarding_completed_at,
            'freeBooks' => Book::published()
                ->where('is_free', true)
                ->orderBy('title')
                ->get(['id', 'title', 'subject', 'level']),
        ]);
    }

    public function saveStep(Request $request): RedirectResponse
    {
        $step = $request->integer('step');
        abort_unless($step >= 1 && $step <= 4, 404);

        $student = Student::findOrFail(auth('web')->id());
        $progress = $student->onboarding_progress ?? [];
        $completedSteps = $progress['completed_steps'] ?? [];
        abort_unless($step !== 4 || $request->boolean('skip') || ! empty($progress['first_question_completed']), 422);

        if (! $request->boolean('skip')) {
            $rules = match ($step) {
                1 => ['goal' => ['required', 'string', 'in:'.implode(',', self::GOALS)]],
                2 => [
                    'class_level' => ['required', 'string', 'max:80'],
                    'subject' => ['required', 'string', 'max:120'],
                ],
                3 => ['book_id' => ['nullable', 'integer', 'exists:books,id']],
                default => [],
            };
            $validated = $request->validate($rules);

            if ($step === 3 && ! empty($validated['book_id'])) {
                abort_unless(Book::published()->where('is_free', true)->whereKey($validated['book_id'])->exists(), 422);
            }

            $progress = array_merge($progress, $validated);
        }

        $completedSteps[] = $step;
        $completedSteps = array_values(array_unique(array_map('intval', $completedSteps)));

        if ($step === 4) {
            $student->onboarding_completed_at ??= now();
        } else {
            $progress['current_step'] = $step + 1;
        }

        $progress['completed_steps'] = $completedSteps;
        $student->onboarding_progress = $progress;
        $student->save();

        return redirect()->route($student->onboarding_completed_at ? 'dashboard' : 'onboarding.show');
    }

    public function skip(): RedirectResponse
    {
        $student = Student::findOrFail(auth('web')->id());
        $student->onboarding_completed_at ??= now();
        $student->save();

        return redirect()->route('dashboard');
    }
}
