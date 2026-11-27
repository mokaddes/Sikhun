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
    public function createManual(
        int $studentId,
        array $items,
        string $status = 'sent',
        ?string $dueAt = null,
        ?string $notes = null,
        float $discount = 0,
        ?float $vatPercent = null,
        array $options = []
    ): Invoice {
        $invoice = DB::transaction(function () use ($studentId, $items, $status, $dueAt, $notes, $discount, $vatPercent, $options) {
            $type = ($options['type'] ?? 'one_time') === 'recurring' ? 'recurring' : 'one_time';
            $issuedAt = ! empty($options['issued_at']) ? Carbon::parse($options['issued_at']) : now();

            $meta = [
                'manual' => true,
                'reminders' => [],
            ];

            if ($type === 'recurring') {
                $meta['recurrence_interval'] = $options['recurrence_interval'] ?? 'monthly';
                $meta['recurrence_days'] = isset($options['recurrence_days']) && $options['recurrence_days'] !== ''
                    ? (int) $options['recurrence_days']
                    : null;
                $meta['auto_generate_next'] = (bool) ($options['auto_generate_next'] ?? true);
            }

            foreach (['plan_id', 'course_id', 'book_id'] as $refKey) {
                if (! empty($options[$refKey])) {
                    $meta[$refKey] = (int) $options[$refKey];
                }
            }

            $invoice = Invoice::create([
                'student_id' => $studentId,
                'order_id' => null,
                'invoice_number' => 'INV-'.now()->format('ym').'-M'.strtoupper(Str::random(6)),
                'type' => $type,
                'period_start' => $options['period_start'] ?? null,
                'period_end' => $options['period_end'] ?? null,
                'issued_at' => $issuedAt,
                'due_at' => $dueAt,
                'subtotal' => 0,
                'discount' => 0,
                'vat' => 0,
                'total' => 0,
                'currency' => 'BDT',
                'status' => $status,
                'notes' => $notes,
                'meta' => $meta,
            ]);

            return $this->recalculate($invoice, $items, $discount, $vatPercent);
        });

        if (! empty($options['notify_student']) && $invoice->status !== 'draft') {
            $this->sendInvoiceNotification($invoice);
        }

        return $invoice;
    }

    public function sendInvoiceNotification(Invoice $invoice): void
    {
        $invoice->loadMissing('student');
        if (! $invoice->student) {
            return;
        }

        $daysLeft = $invoice->due_at
            ? (int) max(0, today()->diffInDays($invoice->due_at->copy()->startOfDay(), false))
            : 0;
        $bn = app()->getLocale() === 'bn';

        try {
            if (! empty($invoice->student->email)) {
                Mail::to($invoice->student->email)->send(new InvoiceReminderMail($invoice, max(0, $daysLeft)));
            }
        } catch (\Throwable $e) {
            Log::warning('Manual invoice notification mail failed', ['invoice' => $invoice->id, 'error' => $e->getMessage()]);
        }

        try {
            app(NotificationService::class)->send(
                student: $invoice->student,
                type: 'subscription_expiry',
                title: $bn ? "নতুন ইনভয়েস প্রস্তুত ({$invoice->invoice_number})" : "Invoice Ready ({$invoice->invoice_number})",
                body: $bn
                    ? "আপনার জন্য ৳{$invoice->total} টাকার ইনভয়েস {$invoice->invoice_number} তৈরি করা হয়েছে। বিস্তারিত দেখে পরিশোধ করুন।"
                    : "Invoice {$invoice->invoice_number} for ৳{$invoice->total} is ready. Click to view & pay.",
                data: [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'url' => '/invoices/'.$invoice->id,
                ],
                mandatory: true
            );
        } catch (\Throwable $e) {
            Log::warning('Manual invoice push notification failed', ['invoice' => $invoice->id, 'error' => $e->getMessage()]);
        }
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
        $discount = round((float) ($order->meta['coupon_discount'] ?? 0), 2);
        $subtotal = $discount > 0
            ? round((float) ($order->meta['original_amount'] ?? ($order->amount + $discount)), 2)
            : round((float) $order->amount, 2);
        $taxable = max(0.0, round($subtotal - $discount, 2));
        $vat = $this->vatFor($taxable);
        $issuedAt = $order->updated_at ?? now();

        $meta = [
            'order_number' => $order->order_number,
            'payment_method' => $order->payment_method,
            'reminders' => [],
        ];
        if (! empty($order->meta['coupon_code'])) {
            $meta['coupon_id'] = $order->meta['coupon_id'] ?? null;
            $meta['coupon_code'] = $order->meta['coupon_code'];
            $meta['coupon_discount'] = $discount;
        }

        return DB::transaction(function () use ($order, $description, $type, $periodStart, $periodEnd, $subtotal, $discount, $taxable, $vat, $issuedAt, $meta) {
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
                'discount' => $discount,
                'vat' => $vat,
                'total' => round($taxable + $vat, 2),
                'currency' => 'BDT',
                'status' => 'paid',
                'meta' => $meta,
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
            ->whereHas('plan', fn ($q) => $q->where('is_free', false)->where('price_monthly', '>', 0))
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

                    // Auto-send renewal invoice email + mandatory push/in-app notification 7 days before expiry
                    $daysUntilExpiry = (int) max(0, today()->diffInDays($subscription->expires_at->copy()->startOfDay()));
                    try {
                        if (! empty($subscription->student->email)) {
                            Mail::to($subscription->student->email)->send(new InvoiceReminderMail($invoice, $daysUntilExpiry));
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Auto renewal invoice mail failed', ['invoice' => $invoice->id, 'error' => $e->getMessage()]);
                    }

                    app(NotificationService::class)->send(
                        student: $subscription->student,
                        type: 'subscription_expiry',
                        title: $bn ? "নবায়ন ইনভয়েস তৈরি হয়েছে ({$subscription->plan->name})" : "Renewal Invoice Ready ({$subscription->plan->name})",
                        body: $bn
                            ? "আপনার {$subscription->plan->name} প্ল্যানের মেয়াদ {$daysUntilExpiry} দিন পর শেষ হবে। ইনভয়েস {$invoice->invoice_number} (৳{$invoice->total}) পরিশোধ করে নিরবচ্ছিন্ন সেবা চালু রাখুন।"
                            : "Your {$subscription->plan->name} plan expires in {$daysUntilExpiry} day(s). Pay invoice {$invoice->invoice_number} (৳{$invoice->total}) to stay active.",
                        data: [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'url' => '/invoices/'.$invoice->id,
                        ],
                        mandatory: true
                    );

                    $meta = $invoice->meta ?? [];
                    $meta['reminders'] = array_values(array_unique([...($meta['reminders'] ?? []), $daysUntilExpiry]));
                    $invoice->update(['meta' => $meta]);

                    $created++;
                }
            });

        // Also generate next-cycle invoices for manual recurring invoices when period_end approaches.
        Invoice::with(['student', 'items'])
            ->where('type', 'recurring')
            ->where('status', '!=', 'void')
            ->whereNull('order_id')
            ->get()
            ->filter(function (Invoice $inv) use ($days) {
                $meta = is_array($inv->meta) ? $inv->meta : [];
                if (empty($meta['manual']) || empty($meta['auto_generate_next']) || ! empty($meta['next_generated_invoice_id'])) {
                    return false;
                }
                if (! empty($meta['subscription_id'])) {
                    return false;
                }
                $anchor = $inv->period_end ?? $inv->due_at;
                if (! $anchor) {
                    return false;
                }

                return $anchor->between(now()->subDays(30)->startOfDay(), now()->addDays($days)->endOfDay());
            })
            ->each(function (Invoice $inv) use (&$created) {
                if (! $inv->student || $inv->items->isEmpty()) {
                    return;
                }

                $meta = is_array($inv->meta) ? $inv->meta : [];
                $anchor = ($inv->period_end ?? $inv->due_at)->copy()->startOfDay();
                $nextStart = $anchor->copy()->addDay();
                $interval = $meta['recurrence_interval'] ?? 'monthly';
                $customDays = max(1, (int) ($meta['recurrence_days'] ?? 30));

                $nextEnd = match ($interval) {
                    'weekly' => $nextStart->copy()->addDays(6),
                    'quarterly' => $nextStart->copy()->addMonths(3)->subDay(),
                    'yearly' => $nextStart->copy()->addYear()->subDay(),
                    'custom' => $nextStart->copy()->addDays($customDays - 1),
                    default => $nextStart->copy()->addMonth()->subDay(),
                };

                $exists = Invoice::query()
                    ->where('student_id', $inv->student_id)
                    ->where('type', 'recurring')
                    ->whereDate('period_start', $nextStart)
                    ->where('status', '!=', 'void')
                    ->exists();

                if ($exists) {
                    return;
                }

                $items = $inv->items->map(fn ($item) => [
                    'description' => $item->description,
                    'qty' => (float) $item->qty,
                    'unit_price' => (float) $item->unit_price,
                ])->all();

                $vatPercent = (float) $this->settings->get('vat_percent', 0);
                if ($inv->subtotal > 0 && $inv->vat > 0) {
                    $taxable = max(0.01, (float) $inv->subtotal - (float) $inv->discount);
                    $vatPercent = round(((float) $inv->vat / $taxable) * 100, 2);
                }

                $nextInvoice = $this->createManual(
                    $inv->student_id,
                    $items,
                    'sent',
                    $nextStart->toDateString(),
                    $inv->notes,
                    (float) $inv->discount,
                    $vatPercent,
                    [
                        'type' => 'recurring',
                        'period_start' => $nextStart->toDateString(),
                        'period_end' => $nextEnd->toDateString(),
                        'recurrence_interval' => $interval,
                        'recurrence_days' => $meta['recurrence_days'] ?? null,
                        'auto_generate_next' => true,
                        'plan_id' => $meta['plan_id'] ?? null,
                        'course_id' => $meta['course_id'] ?? null,
                        'book_id' => $meta['book_id'] ?? null,
                        'notify_student' => true,
                    ]
                );

                $meta['next_generated_invoice_id'] = $nextInvoice->id;
                $inv->update(['meta' => $meta]);
                $created++;
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
     * Emails + in-app + Firebase push notifications for invoices due in 7, 3, or 0 (same day) days.
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
            ->whereDate('due_at', '<=', today()->addDays(7))
            ->chunkById(200, function ($invoices) use (&$sent, $bn) {
                foreach ($invoices as $invoice) {
                    if (! $invoice->student || ! $invoice->due_at) {
                        continue;
                    }

                    $daysLeft = (int) today()->diffInDays($invoice->due_at->copy()->startOfDay());

                    if (! in_array($daysLeft, [7, 3, 0], true)) {
                        continue;
                    }

                    $alreadySent = $invoice->meta['reminders'] ?? [];

                    if (in_array($daysLeft, $alreadySent, true)) {
                        continue;
                    }

                    try {
                        if (! empty($invoice->student->email)) {
                            Mail::to($invoice->student->email)->send(new InvoiceReminderMail($invoice, $daysLeft));
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Invoice reminder mail failed', [
                            'invoice' => $invoice->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    if ($daysLeft === 0) {
                        $title = $bn ? 'আজই আপনার প্ল্যান ও ইনভয়েসের শেষ দিন!' : 'Invoice due today — plan expires today!';
                        $body = $bn
                            ? "ইনভয়েস {$invoice->invoice_number} (৳{$invoice->total}) আজই পরিশোধের শেষ দিন। এখনই পরিশোধ করতে ক্লিক করুন।"
                            : "Invoice {$invoice->invoice_number} for ৳{$invoice->total} is due today. Click to pay now and keep your access active.";
                    } else {
                        $title = $bn ? "ইনভয়েস পরিশোধের {$daysLeft} দিন বাকি" : "Invoice due in {$daysLeft} days";
                        $body = $bn
                            ? "ইনভয়েস {$invoice->invoice_number} এর {$daysLeft} দিন পর ৳{$invoice->total} পরিশোধ করতে হবে। বিস্তারিত দেখতে ক্লিক করুন।"
                            : "Invoice {$invoice->invoice_number} for ৳{$invoice->total} is due in {$daysLeft} day(s). Click to view & pay.";
                    }

                    app(NotificationService::class)->send(
                        student: $invoice->student,
                        type: 'subscription_expiry',
                        title: $title,
                        body: $body,
                        data: [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'url' => '/invoices/'.$invoice->id,
                        ],
                        mandatory: true
                    );

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
