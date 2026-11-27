<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Student;
use App\Services\InvoiceService;
use App\Services\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function create(SiteSettingService $settings): Response
    {
        return Inertia::render('Admin/Invoices/Create', array_merge([
            'vatPercent' => (float) $settings->get('vat_percent', 0),
        ], $this->formOptions()));
    }

    public function store(Request $request, InvoiceService $invoices): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'type' => ['nullable', 'in:one_time,recurring'],
            'status' => ['required', 'in:draft,sent,paid'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'recurrence_interval' => ['nullable', 'in:weekly,monthly,quarterly,yearly,custom'],
            'recurrence_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'auto_generate_next' => ['nullable', 'boolean'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'book_id' => ['nullable', 'integer', 'exists:books,id'],
            'notify_student' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ]);

        $invoice = $invoices->createManual(
            (int) $data['student_id'],
            $data['items'],
            $data['status'],
            $data['due_at'] ?? null,
            $data['notes'] ?? null,
            (float) ($data['discount'] ?? 0),
            (float) ($data['vat_percent'] ?? 0),
            [
                'type' => $data['type'] ?? 'one_time',
                'issued_at' => $data['issued_at'] ?? null,
                'period_start' => $data['period_start'] ?? null,
                'period_end' => $data['period_end'] ?? null,
                'recurrence_interval' => $data['recurrence_interval'] ?? 'monthly',
                'recurrence_days' => $data['recurrence_days'] ?? null,
                'auto_generate_next' => $data['auto_generate_next'] ?? true,
                'plan_id' => $data['plan_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'book_id' => $data['book_id'] ?? null,
                'notify_student' => (bool) ($data['notify_student'] ?? false),
            ],
        );

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', app()->getLocale() === 'bn'
                ? 'ইনভয়েস সফলভাবে তৈরি হয়েছে।'
                : 'Manual invoice created.');
    }

    public function index(Request $request): Response
    {
        $query = Invoice::query()->with('student')->orderByDesc('issued_at')->orderByDesc('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($inner) use ($search) {
                $inner->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($student) use ($search) {
                        $student->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only('status', 'type', 'search'),
        ]);
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['items', 'student', 'order']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice, SiteSettingService $settings): Response
    {
        $invoice->load(['items', 'student']);

        $vatPercent = (float) $settings->get('vat_percent', 0);
        $taxable = max(0.0, (float) $invoice->subtotal - (float) $invoice->discount);
        if ($taxable > 0 && $invoice->vat > 0) {
            $vatPercent = round(((float) $invoice->vat / $taxable) * 100, 2);
        }

        return Inertia::render('Admin/Invoices/Edit', array_merge([
            'invoice' => $invoice,
            'vatPercent' => $vatPercent,
        ], $this->formOptions()));
    }

    public function update(Request $request, Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'type' => ['nullable', 'in:one_time,recurring'],
            'status' => ['required', 'in:draft,sent,paid,void,overdue'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'recurrence_interval' => ['nullable', 'in:weekly,monthly,quarterly,yearly,custom'],
            'recurrence_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'auto_generate_next' => ['nullable', 'boolean'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'book_id' => ['nullable', 'integer', 'exists:books,id'],
            'notify_student' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $type = $data['type'] ?? $invoice->type ?? 'one_time';
        $meta = is_array($invoice->meta) ? $invoice->meta : [];
        if ($type === 'recurring') {
            $meta['recurrence_interval'] = $data['recurrence_interval'] ?? ($meta['recurrence_interval'] ?? 'monthly');
            $meta['recurrence_days'] = isset($data['recurrence_days']) && $data['recurrence_days'] !== ''
                ? (int) $data['recurrence_days']
                : ($meta['recurrence_days'] ?? null);
            $meta['auto_generate_next'] = array_key_exists('auto_generate_next', $data)
                ? (bool) $data['auto_generate_next']
                : (bool) ($meta['auto_generate_next'] ?? true);
        }

        foreach (['plan_id', 'course_id', 'book_id'] as $refKey) {
            if (array_key_exists($refKey, $data)) {
                if (! empty($data[$refKey])) {
                    $meta[$refKey] = (int) $data[$refKey];
                } else {
                    unset($meta[$refKey]);
                }
            }
        }

        $updates = [
            'type' => $type,
            'status' => $data['status'],
            'issued_at' => $data['issued_at'] ?? $invoice->issued_at,
            'due_at' => $data['due_at'] ?? null,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'notes' => $data['notes'] ?? null,
            'meta' => $meta,
        ];

        if (! empty($data['student_id']) && $invoice->status !== 'paid') {
            $updates['student_id'] = (int) $data['student_id'];
        }

        $invoice->update($updates);

        $invoices->recalculate(
            $invoice,
            $data['items'],
            (float) ($data['discount'] ?? 0),
            (float) ($data['vat_percent'] ?? 0),
        );

        if (! empty($data['notify_student']) && $invoice->status !== 'draft') {
            $invoices->sendInvoiceNotification($invoice);
        }

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', app()->getLocale() === 'bn'
                ? 'ইনভয়েস আপডেট হয়েছে।'
                : 'Invoice updated.');
    }

    public function notify(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $invoices->sendInvoiceNotification($invoice);

        return back()->with('success', app()->getLocale() === 'bn'
            ? 'শিক্ষার্থীর কাছে ইমেইল ও পুশ নোটিফিকেশন পাঠানো হয়েছে।'
            : 'Notification sent to student.');
    }

    public function duplicate(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $invoice->load('items');
        $meta = is_array($invoice->meta) ? $invoice->meta : [];
        $items = $invoice->items->map(fn ($item) => [
            'description' => $item->description,
            'qty' => (float) $item->qty,
            'unit_price' => (float) $item->unit_price,
        ])->all();

        $vatPercent = 0.0;
        $taxable = max(0.0, (float) $invoice->subtotal - (float) $invoice->discount);
        if ($taxable > 0 && $invoice->vat > 0) {
            $vatPercent = round(((float) $invoice->vat / $taxable) * 100, 2);
        }

        $copy = $invoices->createManual(
            $invoice->student_id,
            $items ?: [['description' => 'Invoice item', 'qty' => 1, 'unit_price' => (float) $invoice->total]],
            'draft',
            now()->addDays(7)->toDateString(),
            $invoice->notes,
            (float) $invoice->discount,
            $vatPercent,
            [
                'type' => $invoice->type,
                'period_start' => $invoice->period_start?->toDateString(),
                'period_end' => $invoice->period_end?->toDateString(),
                'recurrence_interval' => $meta['recurrence_interval'] ?? 'monthly',
                'recurrence_days' => $meta['recurrence_days'] ?? null,
                'auto_generate_next' => $meta['auto_generate_next'] ?? true,
                'plan_id' => $meta['plan_id'] ?? null,
                'course_id' => $meta['course_id'] ?? null,
                'book_id' => $meta['book_id'] ?? null,
            ]
        );

        return redirect()
            ->route('admin.invoices.edit', $copy)
            ->with('success', 'ইনভয়েস ডুপ্লিকেট করে নতুন ড্রাফট তৈরি করা হয়েছে।');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'পরিশোধিত ইনভয়েস মুছে ফেলা যাবে না (প্রয়োজনে status void করুন)।');
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'ইনভয়েস মুছে ফেলা হয়েছে।');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['items', 'student', 'order']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'forAdmin' => true,
        ]);
    }

    private function formOptions(): array
    {
        return [
            'students' => Student::query()->orderBy('name')->get(['id', 'name', 'email']),
            'plans' => Plan::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'price_monthly', 'price_total']),
            'courses' => Course::query()->where('is_active', true)->orderBy('title')->get(['id', 'title', 'price']),
            'books' => Book::query()->where('is_published', true)->orderBy('title')->get(['id', 'title', 'price']),
        ];
    }
}
