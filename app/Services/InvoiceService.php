<?php

namespace App\Services;

use App\Mail\InvoiceReminderMail;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Notification as InAppNotification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\StudentSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Invoicing is intentionally boring: every completed Order produces exactly
 * one invoice (idempotent on order_id), subscriptions also get a recurring
 * renewal invoice a week before expiry, and reminders go out 5 and 1 days
 * before the due date.
 *
 * There is no PDF engine here on purpose — DomPDF cannot shape Bengali
 * conjuncts, so invoices are rendered as a print-optimised HTML page that
 * the browser can save as PDF with correct Bangla typography.
 */
class InvoiceService
{
    public function __construct(private SiteSettingService $settings) {}

    /**
     * Create an admin-entered invoice without linking it to a checkout order.
     * Totals are always recomputed server-side from the submitted line items.
     *
     * @param  array<int, array{description: string, qty: numeric, unit_price: numeric}>  $items
     */
    public function createManual(int $studentId, array $items, string $status = 'sent', ?string $dueAt = null, ?string $notes = null, float $discount = 0, ?float $vatPercent = null): Invoice
    {
        return DB::transaction(function () use ($studentId, $items, $status, $dueAt, $notes, $discount, $vatPercent) {
            $invoice = Invoice::create([
                'student_id' => $studentId,
                'order_id' => null,
                'invoice_number' => 'INV-'.now()->format('ym').'-M'.strtoupper(Str::random(6)),
                'type' => 'one_time',
                'issued_at' => now(),
                'due_at' => $dueAt,
                'subtotal' => 0,
                'discount' => 0,
                'vat' => 0,
                'total' => 0,
                'currency' => 'BDT',
                'status' => $status,
                'notes' => $notes,
                'meta' => ['manual' => true, 'reminders' => []],
            ]);

            return $this->recalculate($invoice, $items, $discount, $vatPercent);
        });
    }

    public function createForOrder(Order $order): ?Invoice
    {
        if ($order->status !== 'completed') {
            return null;
        }

        // A wallet top-up is not revenue by itself — the purchase paid from
        // that balance already produces its own invoice. Invoicing both
        // would double-count income in every report.
        if ($order->orderable_type === 'wallet_recharge') {
            return null;
        }

        if (Invoice::where('order_id', $order->id)->exists()) {
            return null;
        }

        [$description, $type, $periodStart, $periodEnd] = $this->describe($order);
        $subtotal = round((float) $order->amount, 2);
        $vat = $this->vatFor($subtotal);
        $issuedAt = $order->updated_at ?? now();

        return DB::transaction(function () use ($order, $description, $type, $periodStart, $periodEnd, $subtotal, $vat, $issuedAt) {
            $invoice = Invoice::create([
                'student_id' => $order->student_id,
                'order_id' => $order->id,
                'invoice_number' => $this->numberFor($order),
                'type' => $type,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'issued_at' => $issuedAt,
                'due_at' => $issuedAt,
                'subtotal' => $subtotal,
                'discount' => 0,
                'vat' => $vat,
                'total' => round($subtotal + $vat, 2),
                'currency' => 'BDT',
                'status' => 'paid',
                'meta' => [
                    'order_number' => $order->order_number,
                    'payment_method' => $order->payment_method,
                    'reminders' => [],
                ],
            ]);

            $invoice->items()->create([
                'description' => $description,
                'qty' => 1,
                'unit_price' => $subtotal,
                'amount' => $subtotal,
            ]);

            return $invoice;
        });
    }

    /**
     * Creates the renewal invoice for active subscriptions that expire within
     * $days. Safe to run daily: an existing non-void invoice for the same
     * period is skipped.
     */
    public function generateUpcomingRecurring(int $days = 7): int
    {
        $created = 0;

        StudentSubscription::with(['student', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->chunkById(200, function ($subscriptions) use (&$created) {
                foreach ($subscriptions as $subscription) {
                    if (! $subscription->student || ! $subscription->plan) {
                        continue;
                    }

                    $periodStart = $subscription->expires_at->copy()->startOfDay();
                    $periodEnd = $periodStart->copy()->addMonth()->subDay();

                    $alreadyInvoiced = Invoice::query()
                        ->where('student_id', $subscription->student_id)
                        ->where('type', 'recurring')
                        ->whereDate('period_start', $periodStart)
                        ->where('status', '!=', 'void')
                        ->exists();

                    if ($alreadyInvoiced) {
                        continue;
                    }

                    $subtotal = round((float) $subscription->plan->price_monthly, 2);
                    $vat = $this->vatFor($subtotal);
                    $bn = app()->getLocale() === 'bn';

                    $invoice = Invoice::create([
                        'student_id' => $subscription->student_id,
                        'order_id' => null,
                        'invoice_number' => $this->numberForSubscription($subscription),
                        'type' => 'recurring',
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'issued_at' => now(),
                        'due_at' => $periodStart,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'vat' => $vat,
                        'total' => round($subtotal + $vat, 2),
                        'currency' => 'BDT',
                        'status' => 'sent',
                        'meta' => [
                            'subscription_id' => $subscription->id,
                            'plan_id' => $subscription->plan_id,
                            'reminders' => [],
                        ],
                    ]);

                    $invoice->items()->create([
                        'description' => ($bn ? 'সাবস্ক্রিপশন নবায়ন: ' : 'Subscription renewal: ')
                            .$subscription->plan->name
                            .' ('.$periodStart->format('d M Y').' – '.$periodEnd->format('d M Y').')',
                        'qty' => 1,
                        'unit_price' => $subtotal,
                        'amount' => $subtotal,
                    ]);

                    $created++;
                }
            });

        return $created;
    }

    public function markOverdue(): int
    {
        return Invoice::query()
            ->where('status', 'sent')
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<', today())
            ->update(['status' => 'overdue']);
    }

    /**
     * Emails + in-app notifications for invoices due in 5 or 1 days.
     * The offsets already sent are stored in meta.reminders, so re-running
     * the command never duplicates a reminder.
     */
    public function sendDueReminders(): int
    {
        $sent = 0;
        $bn = app()->getLocale() === 'bn';

        Invoice::with('student')
            ->whereIn('status', ['sent', 'overdue'])
            ->whereNotNull('due_at')
            ->whereDate('due_at', '>=', today())
            ->whereDate('due_at', '<=', today()->addDays(5))
            ->chunkById(200, function ($invoices) use (&$sent, $bn) {
                foreach ($invoices as $invoice) {
                    if (! $invoice->student || ! $invoice->due_at) {
                        continue;
                    }

                    $daysLeft = (int) today()->diffInDays($invoice->due_at);

                    if (! in_array($daysLeft, [5, 1], true)) {
                        continue;
                    }

                    $alreadySent = $invoice->meta['reminders'] ?? [];

                    if (in_array($daysLeft, $alreadySent, true)) {
                        continue;
                    }

                    try {
                        Mail::to($invoice->student->email)->send(new InvoiceReminderMail($invoice, $daysLeft));
                    } catch (\Throwable $e) {
                        Log::warning('Invoice reminder mail failed', [
                            'invoice' => $invoice->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    InAppNotification::create([
                        'student_id' => $invoice->student_id,
                        'type' => 'invoice_reminder',
                        'title' => $bn ? 'ইনভয়েস পরিশোধের সময় আসছে' : 'Invoice due soon',
                        'body' => $bn
                            ? "ইনভয়েস {$invoice->invoice_number} এর {$daysLeft} দিন পর ৳{$invoice->total} পরিশোধ করতে হবে।"
                            : "Invoice {$invoice->invoice_number} for ৳{$invoice->total} is due in {$daysLeft} day(s).",
                        'data' => [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                        ],
                    ]);

                    $meta = $invoice->meta ?? [];
                    $meta['reminders'] = [...$alreadySent, $daysLeft];
                    $invoice->update(['meta' => $meta]);

                    $sent++;
                }
            });

        return $sent;
    }

    /**
     * Admin edit path: replaces the line items and recomputes totals from
     * scratch so a tampered client payload can never set its own total.
     *
     * @param  array<int, array{description: string, qty: numeric, unit_price: numeric}>  $items
     */
    public function recalculate(Invoice $invoice, array $items, float $discount = 0, ?float $vatPercent = null): Invoice
    {
        DB::transaction(function () use ($invoice, $items, $discount, $vatPercent) {
            $invoice->items()->delete();

            $subtotal = 0.0;

            foreach ($items as $item) {
                $qty = max(0.01, round((float) ($item['qty'] ?? 1), 2));
                $unitPrice = max(0, round((float) ($item['unit_price'] ?? 0), 2));
                $amount = round($qty * $unitPrice, 2);
                $subtotal += $amount;

                $invoice->items()->create([
                    'description' => (string) $item['description'],
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                ]);
            }

            $subtotal = round($subtotal, 2);
            $discount = round(min(max(0, $discount), $subtotal), 2);
            $vatPercent ??= (float) $this->settings->get('vat_percent', 0);
            $vat = round(($subtotal - $discount) * max(0, $vatPercent) / 100, 2);

            $invoice->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'vat' => $vat,
                'total' => round($subtotal - $discount + $vat, 2),
            ]);
        });

        return $invoice->refresh();
    }

    /**
     * @return array{0: string, 1: string, 2: ?Carbon, 3: ?Carbon}
     */
    private function describe(Order $order): array
    {
        $bn = app()->getLocale() === 'bn';
        $type = 'one_time';
        $periodStart = null;
        $periodEnd = null;

        $description = match ($order->orderable_type) {
            'book' => ($bn ? 'বই: ' : 'Book: ')
                .(Book::whereKey($order->orderable_id)->value('title') ?? '#'.$order->orderable_id),
            'book_chapter' => ($bn ? 'বইয়ের অধ্যায়: ' : 'Book chapter: ')
                .($order->meta['chapter_title'] ?? BookChapter::whereKey($order->orderable_id)->value('title') ?? '#'.$order->orderable_id),
            'course' => ($bn ? 'কোর্স: ' : 'Course: ')
                .(Course::whereKey($order->orderable_id)->value('title') ?? '#'.$order->orderable_id),
            'subscription' => ($bn ? 'সাবস্ক্রিপশন: ' : 'Subscription: ')
                .(Plan::whereKey($order->orderable_id)->value('name') ?? '#'.$order->orderable_id),
            default => $bn ? 'ওয়ালেট রিচার্জ' : 'Wallet recharge',
        };

        if ($order->orderable_type === 'subscription') {
            $type = 'recurring';
            $months = max(1, (int) ($order->meta['months'] ?? 1));
            $periodStart = ($order->created_at ?? now())->copy()->startOfDay();
            $periodEnd = $periodStart->copy()->addMonths($months)->subDay();

            $description .= ' ('.$months.' '.($bn ? 'মাস' : ($months === 1 ? 'month' : 'months')).')';
        }

        return [$description, $type, $periodStart, $periodEnd];
    }

    private function vatFor(float $subtotal): float
    {
        $percent = (float) ($this->settings->get('vat_percent', 0) ?? 0);

        return round($subtotal * max(0, $percent) / 100, 2);
    }

    private function numberFor(Order $order): string
    {
        return 'INV-'.($order->created_at ?? now())->format('ym').'-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
    }

    private function numberForSubscription(StudentSubscription $subscription): string
    {
        return 'INV-'.now()->format('ym').'-S'.str_pad((string) $subscription->id, 4, '0', STR_PAD_LEFT)
            .'-'.$subscription->expires_at->format('ymd');
    }
}
