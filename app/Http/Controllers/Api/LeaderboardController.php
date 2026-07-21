<?php

namespace App\Http\Controllers\Api;

use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends BaseApiController
{
    public function index(Request $request, LeaderboardService $leaderboard): JsonResponse
    {
        $period = in_array($request->period, ['weekly', 'monthly', 'all_time'], true) ? $request->period : 'weekly';
        $filters = $request->only('type', 'subject', 'book_id');

        return $this->success($leaderboard->getTopStudents($period, $filters));
    }

    public function myRank(Request $request, LeaderboardService $leaderboard): JsonResponse
    {
        $period = in_array($request->period, ['weekly', 'monthly', 'all_time'], true) ? $request->period : 'weekly';
        $filters = $request->only('type', 'subject', 'book_id');

        return $this->success($leaderboard->getStudentRank(auth('sanctum')->user(), $period, $filters));
    }
}
