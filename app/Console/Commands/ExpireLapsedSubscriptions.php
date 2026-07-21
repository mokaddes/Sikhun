<?php

namespace App\Console\Commands;

use App\Models\StudentSubscription;
use Illuminate\Console\Command;

/**
 * AI access control (Student::hasActiveAiAccess) already checks
 * expires_at->isFuture() directly, so this command isn't needed for
 * correctness there — but without it, a lapsed subscription still shows
 * status='active' in the DB, which throws off the admin dashboard's
 * "Active Subscriptions" count. Purely a reporting-accuracy cleanup.
 */
class ExpireLapsedSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-lapsed';

    protected $description = 'Flip status to expired for subscriptions whose expires_at has passed';

    public function handle(): int
    {
        $count = StudentSubscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Marked {$count} lapsed subscription(s) as expired.");

        return self::SUCCESS;
    }
}
