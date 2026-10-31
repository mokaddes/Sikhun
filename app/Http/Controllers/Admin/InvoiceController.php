<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
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
        return Inertia::render('Admin/Invoices/Create', [
            'students' => Student::query()->orderBy('name')->get(['id', 'name', 'email']),
            'vatPercent' => (float) $settings->get('vat_percent', 0),
        ]);
    }

    public function store(Request $request, InvoiceService $invoices): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'status' => ['required', 'in:draft,sent,paid'],
            'due_at' => ['nullable', 'date'],
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
        );

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', app()->getLocale() === 'bn'
                ? 'ম্যানুয়াল ইনভয়েস তৈরি হয়েছে।'
                : 'Manual invoice created.');
    }

    public function index(Request $request): Response
    {
        $query = Invoice::query()->with('student')->orderByDesc('issued_at')->orderByDesc('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
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
            'filters' => $request->only('status', 'search'),
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

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => $invoice,
            'vatPercent' => (float) $settings->get('vat_percent', 0),
        ]);
    }

    public function update(Request $request, Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,sent,paid,void,overdue'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice->update([
            'status' => $data['status'],
            'issued_at' => $data['issued_at'] ?? $invoice->issued_at,
            'due_at' => $data['due_at'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoices->recalculate(
            $invoice,
            $data['items'],
            (float) ($data['discount'] ?? 0),
            (float) ($data['vat_percent'] ?? 0),
        );

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', app()->getLocale() === 'bn'
                ? 'ইনভয়েস আপডেট হয়েছে।'
                : 'Invoice updated.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['items', 'student', 'order']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'forAdmin' => true,
        ]);
    }
}
