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
        $period = in_array($request->period, ['weekly', 'monthly', 'all_time'], true) ? $request->period : 'weekly';
        $filters = $request->only('type', 'subject', 'book_id');

        return Inertia::render('Student/Leaderboard/Index', [
            'period' => $period,
            'filters' => $filters,
            'top' => $leaderboard->getTopStudents($period, $filters),
            'myRank' => $leaderboard->getStudentRank($student, $period, $filters),
            'optedOut' => $student->leaderboard_opt_out,
        ]);
    }
}
