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

    private function authorizeOwner(Request $request, Invoice $invoice): void
    {
        abort_unless((int) $invoice->student_id === (int) $request->user('web')->getKey(), 403);
    }
}
