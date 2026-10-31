<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class FailStaleZinipayOrders extends Command
{
    protected $signature = 'orders:fail-stale-zinipay {--hours=24 : Minimum age of a pending ZiniPay order}';

    protected $description = 'Mark pending ZiniPay orders failed after the payment window expires';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $updated = Order::query()
            ->where('payment_method', 'zinipay')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours($hours))
            ->update(['status' => 'failed']);

        $this->info("Marked {$updated} stale ZiniPay order(s) as failed.");

        return self::SUCCESS;
    }
}
