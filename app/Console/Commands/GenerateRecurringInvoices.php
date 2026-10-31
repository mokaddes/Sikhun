<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

/**
 * Creates the renewal invoice for subscriptions that expire within a week.
 * Runs daily and is idempotent: an existing non-void invoice for the same
 * billing period is never created twice.
 */
class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring {--days=7 : How many days ahead to look}';

    protected $description = 'Create renewal invoices for subscriptions expiring within N days';

    public function handle(InvoiceService $invoices): int
    {
        $days = max(1, (int) $this->option('days'));
        $created = $invoices->generateUpcomingRecurring($days);

        $this->info("Created {$created} recurring invoice(s) for subscriptions expiring within {$days} day(s).");

        return self::SUCCESS;
    }
}
