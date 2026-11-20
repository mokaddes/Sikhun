<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class FailStaleGatewayOrders extends Command
{
    protected $signature = 'orders:fail-stale-gateway {--hours=24 : Minimum age of a pending gateway order}';

    protected $description = 'Mark pending payment gateway orders (MHPay, bKash, Nagad) failed after the payment window expires';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $updated = Order::query()
            ->whereIn('payment_method', ['mhpay', 'bkash', 'nagad'])
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours($hours))
            ->update(['status' => 'failed']);

        $this->info("Marked {$updated} stale gateway order(s) as failed.");

        return self::SUCCESS;
    }
}
