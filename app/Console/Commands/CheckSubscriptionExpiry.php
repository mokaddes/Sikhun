<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryMail;
use App\Models\StudentSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Warns students 7, 3, and 1 day(s) before their subscription lapses
 * (REQ-SUB-07). Runs once daily — a subscription expiring in exactly N
 * days gets exactly one email per threshold, never a duplicate, since
 * "expires_at is between today+N and today+N+1" only matches on one
 * specific day for any given subscription.
 */
class CheckSubscriptionExpiry extends Command
{
    protected $signature = 'subscriptions:expiry-check';

    protected $description = 'Email students whose active subscription expires in 7, 3, or 1 day(s)';

    public function handle(): int
    {
        $sent = 0;

        foreach ([7, 3, 1] as $daysRemaining) {
            $windowStart = now()->addDays($daysRemaining)->startOfDay();
            $windowEnd = now()->addDays($daysRemaining)->endOfDay();

            $subscriptions = StudentSubscription::where('status', 'active')
                ->whereBetween('expires_at', [$windowStart, $windowEnd])
                ->with(['student', 'plan'])
                ->get();

            foreach ($subscriptions as $subscription) {
                Mail::to($subscription->student->email)->send(new SubscriptionExpiryMail($subscription, $daysRemaining));
                $sent++;
            }
        }

        $this->info("Sent {$sent} subscription expiry warning(s).");

        return self::SUCCESS;
    }
}
