<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaderboardController extends Controller
{
    public function index(Request $request, LeaderboardService $leaderboard): Response
    {
        $student = auth('web')->user();
        $studentType = $student->type ?: 'hsc';

        $period = in_array($request->period, ['weekly', 'monthly', 'all_time'], true) ? $request->period : 'weekly';
        $filters = $request->only('subject', 'book_id');
        // Enforce that students only see the leaderboard for their own education cohort
        $filters['type'] = $studentType;

        return Inertia::render('Student/Leaderboard/Index', [
            'period' => $period,
            'filters' => $filters,
            'studentType' => $studentType,
            'top' => $leaderboard->getTopStudents($period, $filters),
            'myRank' => $leaderboard->getStudentRank($student, $period, $filters),
            'optedOut' => (bool) $student->leaderboard_opt_out,
        ]);
    }
}
