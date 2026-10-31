<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

/**
 * Flips `sent` invoices whose due date has passed to `overdue`, so the admin
 * dashboard and the student list show the real state instead of a stale
 * "sent" badge.
 */
class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Mark sent invoices past their due date as overdue';

    public function handle(InvoiceService $invoices): int
    {
        $updated = $invoices->markOverdue();

        $this->info("Marked {$updated} invoice(s) as overdue.");

        return self::SUCCESS;
    }
}
