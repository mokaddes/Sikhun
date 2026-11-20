<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingSession;
use App\Services\AccessGrantService;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    public function index(LeaderboardService $leaderboard, AccessGrantService $grants): JsonResponse
    {
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
            ->get(['id', 'config', 'score', 'total', 'percentage', 'completed_at']);

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
            ] : null,
            'continue_course' => $lastCourse ? [
                'course_id' => $lastCourse->course_id,
                'course_title' => $lastCourse->course?->title,
                'course_slug' => $lastCourse->course?->slug,
                'cover_image' => $lastCourse->course?->cover_image ? asset('storage/'.$lastCourse->course->cover_image) : null,
            ] : null,
            'recent_exams' => $recentExams,
            'leaderboard_top' => $topLeaderboard,
            'my_rank' => $myRank,
            'active_subscription' => $student->activeSubscription()->with('plan')->first(),
        ]);
    }
}
