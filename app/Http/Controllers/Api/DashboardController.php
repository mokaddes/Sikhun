<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\ReadingSession;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    private const GOALS = ['hsc', 'ssc', 'admission', 'job', 'university'];

    public function index(LeaderboardService $leaderboard, AccessGrantService $grants): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();

        $lastReading = ReadingSession::where('student_id', $student->id)
            ->with('book:id,title,slug,cover_image,total_pages')
            ->orderByDesc('last_activity_at')
            ->first();

        $lastCourse = $student->courseEnrollments()
            ->with('course:id,title,slug,cover_image')
            ->whereNull('completed_at')
            ->latest('updated_at')
            ->first();

        $recentExams = $student->examSessions()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(5)
            ->get(['id', 'title', 'config', 'score', 'total', 'percentage', 'completed_at']);

        $pendingOrder = $student->orders()
            ->where('status', 'pending')
            ->latest()
            ->first(['id', 'order_number', 'orderable_type', 'amount', 'payment_method', 'gateway_transaction_id', 'created_at']);

        $topLeaderboard = [];
        $myRank = null;
        try {
            $topLeaderboard = array_slice($leaderboard->getTopStudents('weekly', ['type' => $student->type]), 0, 5);
            $myRank = $leaderboard->getStudentRank($student, 'weekly', ['type' => $student->type]);
        } catch (\Throwable $e) {}

        return $this->success([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'type' => $student->type,
                'avatar' => $student->avatar,
                'wallet_balance' => (float) $student->wallet_balance,
            ],
            'stats' => [
                'wallet_balance' => (float) $student->wallet_balance,
                'books_owned' => $student->books()->count(),
                'exams_taken' => $student->examSessions()->where('status', 'completed')->count(),
                'courses_enrolled' => $student->courseEnrollments()->count(),
                'unresolved_mistakes' => $student->examAnswers()->where('is_correct', false)->count(),
            ],
            'pending_order' => $pendingOrder,
            'continue_reading' => $lastReading ? [
                'book_id' => $lastReading->book_id,
                'book_title' => $lastReading->book?->title,
                'book_slug' => $lastReading->book?->slug,
                'cover_image' => $lastReading->book?->cover_image ? asset('storage/'.$lastReading->book->cover_image) : null,
                'current_page' => $lastReading->current_page,
                'total_pages' => $lastReading->book?->total_pages ?? 0,
                'progress' => ($lastReading->book?->total_pages ?? 0) > 0
                    ? round(($lastReading->current_page / $lastReading->book->total_pages) * 100)
                    : 0,
                'book' => $lastReading->book,
            ] : null,
            'continue_course' => $lastCourse ? [
                'course_id' => $lastCourse->course_id,
                'course_title' => $lastCourse->course?->title,
                'course_slug' => $lastCourse->course?->slug,
                'cover_image' => $lastCourse->course?->cover_image ? asset('storage/'.$lastCourse->course->cover_image) : null,
                'course' => $lastCourse->course,
            ] : null,
            'recent_exams' => $recentExams,
            'leaderboard_top' => $topLeaderboard,
            'my_rank' => $myRank,
            'active_subscription' => $student->activeSubscription()->with('plan')->first(),
            'access' => $grants->accessSummary($student),
            'can_access_question_bank' => in_array($student->type, ['admission', 'job'], true),
            'onboarding' => [
                'completed' => (bool) $student->onboarding_completed_at,
                'progress' => $student->onboarding_progress ?? [],
            ],
        ]);
    }

    public function onboarding(): JsonResponse
    {
        /** @var Student $student */
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

    public function saveOnboardingStep(Request $request): JsonResponse
    {
        $step = $request->integer('step');
        abort_unless($step >= 1 && $step <= 4, 404);

        /** @var Student $student */
        $student = auth('sanctum')->user();
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

        return $this->success([
            'completed' => (bool) $student->onboarding_completed_at,
            'progress' => $student->onboarding_progress,
        ], 'Onboarding step saved');
    }

    public function skipOnboarding(): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();
        $student->onboarding_completed_at ??= now();
        $student->save();

        return $this->success(['completed' => true], 'Onboarding skipped');
    }
}
