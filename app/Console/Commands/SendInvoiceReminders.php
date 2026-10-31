<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

/**
 * Sends the 5-day and 1-day payment reminders. The service stores which
 * offsets were already sent in invoices.meta.reminders, so running this
 * command more than once a day never spams the student.
 */
class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders';

    protected $description = 'Email and notify students about invoices due in 5 or 1 day(s)';

    public function handle(InvoiceService $invoices): int
    {
        $sent = $invoices->sendDueReminders();

        $this->info("Sent {$sent} invoice reminder(s).");

        return self::SUCCESS;
    }
}
