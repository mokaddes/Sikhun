<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends BaseApiController
{
    private const GOALS = ['hsc', 'ssc', 'admission', 'job', 'university'];

    public function show(): JsonResponse
    {
        $student = auth('sanctum')->user();

        return $this->success([
            'progress' => $student->onboarding_progress ?? [],
            'completed_at' => $student->onboarding_completed_at,
            'free_books' => Book::published()
                ->where('is_free', true)
                ->orderBy('title')
                ->get(['id', 'title', 'subject', 'level']),
        ]);
    }

    public function saveStep(Request $request): JsonResponse
    {
        $step = $request->integer('step');
        abort_unless($step >= 1 && $step <= 4, 404);

        $student = auth('sanctum')->user();
        $progress = $student->onboarding_progress ?? [];
        $completedSteps = $progress['completed_steps'] ?? [];

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

        return $this->success([
            'progress' => $student->onboarding_progress,
            'completed_at' => $student->onboarding_completed_at,
        ], 'Onboarding step saved');
    }

    public function skip(): JsonResponse
    {
        $student = auth('sanctum')->user();
        $student->onboarding_completed_at ??= now();
        $student->save();

        return $this->success(['completed_at' => $student->onboarding_completed_at], 'Onboarding skipped');
    }
}
