<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = Invoice::query()
            ->where('student_id', $request->user('web')->getKey())
            ->withCount('items')
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(15);

        return Inertia::render('Student/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice): Response
    {
        $this->authorizeOwner($request, $invoice);

        $invoice->load(['items', 'order']);

        return Inertia::render('Student/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function print(Request $request, Invoice $invoice)
    {
        $this->authorizeOwner($request, $invoice);

        $invoice->load(['items', 'student', 'order']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'forAdmin' => false,
        ]);
    }

    public function pay(
        Request $request,
        Invoice $invoice,
        \App\Services\PurchaseService $purchases,
        \App\Services\Payment\ZinipayService $zinipay,
        \App\Services\WalletService $wallet,
        \App\Services\SubscriptionService $subscriptions
    ): \Symfony\Component\HttpFoundation\Response {
        $this->authorizeOwner($request, $invoice);

        abort_unless(in_array($invoice->status, ['sent', 'overdue', 'draft'], true), 400, 'This invoice is already paid or cannot be paid.');

        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet,zinipay'],
        ]);

        $student = $request->user('web');
        $method = $validated['payment_method'];

        if ($method === 'wallet') {
            if ($student->wallet_balance < $invoice->total) {
                return back()->with('error', 'Insufficient wallet balance to pay this invoice.');
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $student, $invoice, $subscriptions) {
                $wallet->debit(
                    $student,
                    (float) $invoice->total,
                    'invoice_payment',
                    $invoice->invoice_number,
                    "Paid invoice {$invoice->invoice_number}"
                );

                $invoice->update(['status' => 'paid']);

                if (! empty($invoice->meta['subscription_id'])) {
                    $sub = \App\Models\StudentSubscription::find($invoice->meta['subscription_id']);
                    if ($sub && $sub->status === 'active') {
                        $sub->update(['expires_at' => $sub->expires_at->addMonth()]);
                    } elseif ($sub) {
                        $subscriptions->assign($student, $sub->plan, 1);
                    }
                } elseif (! empty($invoice->meta['plan_id'])) {
                    $plan = \App\Models\Plan::find($invoice->meta['plan_id']);
                    if ($plan) {
                        $subscriptions->assign($student, $plan, 1);
                    }
                }
            });

            return back()->with('success', 'Invoice paid successfully via wallet!');
        }

        $order = \App\Models\Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-INV-'.now()->format('ymd').'-'.strtoupper(\Illuminate\Support\Str::random(6)),
            'orderable_type' => 'invoice',
            'orderable_id' => $invoice->id,
            'amount' => $invoice->total,
            'payment_method' => 'zinipay',
            'status' => 'pending',
            'meta' => ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number],
        ]);

        $redirectUrl = $zinipay->initiate($order);

        return \Inertia\Inertia::location($redirectUrl);
    }

    private function authorizeOwner(Request $request, Invoice $invoice): void
    {
        abort_unless((int) $invoice->student_id === (int) $request->user('web')->getKey(), 403);
    }
}
