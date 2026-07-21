<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

class ResetMonthlyLeaderboard extends Command
{
    protected $signature = 'leaderboard:reset-monthly';

    protected $description = 'Invalidate the cached monthly leaderboard at the start of a new month';

    public function handle(LeaderboardService $leaderboard): int
    {
        $leaderboard->invalidateAll();
        $this->info('Monthly leaderboard cache cleared.');

        return self::SUCCESS;
    }
}
