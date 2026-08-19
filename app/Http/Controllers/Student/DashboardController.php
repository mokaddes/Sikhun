<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\ReadingSession;
use App\Services\AccessGrantService;
use App\Services\LeaderboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(LeaderboardService $leaderboard, AccessGrantService $grants): Response
    {
        $student = auth('web')->user();

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
            ->take(3)
            ->get(['id', 'config', 'score', 'total', 'percentage', 'completed_at']);

        return Inertia::render('Student/Dashboard/Index', [
            'stats' => [
                'wallet_balance' => $student->wallet_balance,
                'books_owned' => $student->books()->count(),
                'exams_taken' => $student->examSessions()->where('status', 'completed')->count(),
                'courses_enrolled' => $student->courseEnrollments()->count(),
            ],
            'continueReading' => $lastReading?->book,
            'continueCourse' => $lastCourse,
            'recentExams' => $recentExams,
            'leaderboardTop' => array_slice($leaderboard->getTopStudents('weekly', ['type' => $student->type]), 0, 3),
            'myRank' => $leaderboard->getStudentRank($student, 'weekly', ['type' => $student->type]),
            'activeSubscription' => $student->activeSubscription()->with('plan')->first(),
            'access' => $grants->accessSummary($student),
        ]);
    }
}
