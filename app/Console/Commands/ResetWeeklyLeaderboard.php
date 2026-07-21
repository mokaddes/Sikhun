<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

/**
 * Doesn't delete rows — LeaderboardEntry rows are permanent history (also
 * feed the all-time board). "Reset" here just means invalidating the
 * cached weekly leaderboard so it recomputes cleanly for the new week
 * (the week_number/year columns naturally exclude old entries already).
 */
class ResetWeeklyLeaderboard extends Command
{
    protected $signature = 'leaderboard:reset-weekly';

    protected $description = 'Invalidate the cached weekly leaderboard at the start of a new week';

    public function handle(LeaderboardService $leaderboard): int
    {
        $leaderboard->invalidateAll();
        $this->info('Weekly leaderboard cache cleared.');

        return self::SUCCESS;
    }
}
