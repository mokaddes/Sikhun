<?php

use App\Models\Book;
use App\Models\Order;
use App\Models\Student;
use App\Services\InvoiceService;

$student = Student::find(1);
$book = Book::first();

$order = Order::create([
    'student_id' => $student->id,
    'order_number' => 'SKH-DEMO-INV-1',
    'orderable_type' => 'book',
    'orderable_id' => $book->id,
    'amount' => 250,
    'payment_method' => 'zinipay',
    'status' => 'completed',
]);

$invoice = app(InvoiceService::class)->createForOrder($order);
$invoice->update(['status' => 'sent', 'due_at' => today()->addDays(5)]);

echo $invoice->id.' '.$invoice->invoice_number.PHP_EOL;
