<?php

namespace Tests\Feature;

use App\Mail\InvoiceReminderMail;
use App\Models\Admin;
use App\Models\Book;
use App\Models\Invoice;
use App\Models\Notification as InAppNotification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\InvoiceService;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Invoice Student',
            'email' => 'invoice-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'INV'.strtoupper(uniqid()),
        ]);
    }

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Invoice Admin',
            'email' => 'admin-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    private function book(): Book
    {
        return Book::create([
            'title' => 'Higher Math',
            'slug' => 'higher-math-'.uniqid(),
            'price' => 250,
            'is_free' => false,
            'is_published' => true,
            'total_pages' => 100,
        ]);
    }

    private function completedBookOrder(Student $student, Book $book, float $amount = 250): Order
    {
        return Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-'.strtoupper(uniqid()),
            'orderable_type' => 'book',
            'orderable_id' => $book->id,
            'amount' => $amount,
            'payment_method' => 'zinipay',
            'status' => 'completed',
        ]);
    }

    private function plan(): Plan
    {
        return Plan::create([
            'name' => 'HSC Monthly',
            'slug' => 'hsc-monthly-'.uniqid(),
            'price_monthly' => 299,
            'ai_chat_minutes' => 60,
            'ai_vision_limit' => 20,
            'upload_book_limit' => 3,
            'monthly_reupload_limit' => 5,
            'ai_exam_count' => 20,
            'trial_ai_minutes' => 10,
            'is_active' => true,
        ]);
    }

    public function test_completed_order_creates_a_paid_invoice_with_one_item(): void
    {
        $student = $this->student();
        $order = $this->completedBookOrder($student, $this->book());

        $invoice = app(InvoiceService::class)->createForOrder($order);

        $this->assertNotNull($invoice);
        $this->assertSame('paid', $invoice->status);
        $this->assertSame($order->id, $invoice->order_id);
        $this->assertSame(1, $invoice->items()->count());
        $this->assertEquals(250, (float) $invoice->subtotal);
        $this->assertEquals(250, (float) $invoice->total);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
    }

    public function test_invoice_creation_is_idempotent_per_order(): void
    {
        $order = $this->completedBookOrder($this->student(), $this->book());
        $service = app(InvoiceService::class);

        $service->createForOrder($order);
        $service->createForOrder($order->fresh());

        $this->assertSame(1, Invoice::where('order_id', $order->id)->count());
    }

    public function test_wallet_recharges_are_not_invoiced(): void
    {
        $order = Order::create([
            'student_id' => $this->student()->id,
            'order_number' => 'SKH-'.strtoupper(uniqid()),
            'orderable_type' => 'wallet_recharge',
            'orderable_id' => null,
            'amount' => 500,
            'payment_method' => 'zinipay',
            'status' => 'completed',
        ]);

        $this->assertNull(app(InvoiceService::class)->createForOrder($order));
        $this->assertSame(0, Invoice::count());
    }

    public function test_fulfilment_creates_the_invoice(): void
    {
        Mail::fake();

        $student = $this->student();
        $order = $this->completedBookOrder($student, $this->book());

        app(PurchaseService::class)->fulfill($order);

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'student_id' => $student->id,
            'status' => 'paid',
        ]);
    }

    public function test_admin_update_recalculates_totals_server_side(): void
    {
        $student = $this->student();
        $invoice = app(InvoiceService::class)->createForOrder(
            $this->completedBookOrder($student, $this->book())
        );

        $this->actingAs($this->admin(), 'admin')
            ->put("/admin/invoices/{$invoice->id}", [
                'status' => 'sent',
                'issued_at' => now()->toDateString(),
                'due_at' => now()->addDays(5)->toDateString(),
                'notes' => 'Edited by admin',
                'discount' => 50,
                'vat_percent' => 5,
                'items' => [
                    ['description' => 'Higher Math', 'qty' => 2, 'unit_price' => 200],
                    ['description' => 'Delivery', 'qty' => 1, 'unit_price' => 100],
                ],
            ])
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();

        // 2×200 + 1×100 = 500; minus 50 discount = 450; 5% VAT = 22.50
        $this->assertEquals(500, (float) $invoice->subtotal);
        $this->assertEquals(50, (float) $invoice->discount);
        $this->assertEquals(22.5, (float) $invoice->vat);
        $this->assertEquals(472.5, (float) $invoice->total);
        $this->assertSame('sent', $invoice->status);
        $this->assertSame(2, $invoice->items()->count());
    }

    public function test_admin_can_create_manual_invoice_with_server_calculated_totals(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin(), 'admin')
            ->post('/admin/invoices', [
                'student_id' => $student->id,
                'status' => 'sent',
                'due_at' => now()->addDays(7)->toDateString(),
                'notes' => 'Manual charge',
                'discount' => 10,
                'vat_percent' => 10,
                'total' => 0.01,
                'items' => [
                    ['description' => 'Course access', 'qty' => 2, 'unit_price' => 100],
                    ['description' => 'Workbook', 'qty' => 1, 'unit_price' => 50],
                ],
            ])
            ->assertRedirect();

        $invoice = Invoice::where('student_id', $student->id)->firstOrFail();

        $this->assertNull($invoice->order_id);
        $this->assertSame('sent', $invoice->status);
        $this->assertSame(true, $invoice->meta['manual']);
        $this->assertEquals(250, (float) $invoice->subtotal);
        $this->assertEquals(10, (float) $invoice->discount);
        $this->assertEquals(24, (float) $invoice->vat);
        $this->assertEquals(264, (float) $invoice->total);
        $this->assertSame(2, $invoice->items()->count());
    }

    public function test_admin_update_rejects_an_empty_item_list(): void
    {
        $invoice = app(InvoiceService::class)->createForOrder(
            $this->completedBookOrder($this->student(), $this->book())
        );

        $this->actingAs($this->admin(), 'admin')
            ->put("/admin/invoices/{$invoice->id}", [
                'status' => 'paid',
                'items' => [],
            ])
            ->assertSessionHasErrors('items');
    }

    public function test_recurring_invoice_is_generated_once_for_an_expiring_subscription(): void
    {
        $student = $this->student();
        $plan = $this->plan();

        StudentSubscription::create([
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now()->subMonth(),
            'expires_at' => now()->addDays(3),
        ]);

        $service = app(InvoiceService::class);

        $this->assertSame(1, $service->generateUpcomingRecurring(7));
        $this->assertSame(0, $service->generateUpcomingRecurring(7));

        $invoice = Invoice::where('type', 'recurring')->firstOrFail();

        $this->assertSame('sent', $invoice->status);
        $this->assertEquals(299, (float) $invoice->total);
        $this->assertNotNull($invoice->period_start);
        $this->assertNull($invoice->order_id);
    }

    public function test_due_reminders_are_sent_once_for_five_day_offset(): void
    {
        Mail::fake();

        $invoice = app(InvoiceService::class)->createForOrder(
            $this->completedBookOrder($this->student(), $this->book())
        );

        $invoice->update([
            'status' => 'sent',
            'due_at' => today()->addDays(5),
        ]);

        $service = app(InvoiceService::class);

        $this->assertSame(1, $service->sendDueReminders());
        $this->assertSame(0, $service->sendDueReminders());

        Mail::assertSent(InvoiceReminderMail::class, 1);
        $this->assertSame([5], $invoice->fresh()->meta['reminders']);
        $this->assertSame(1, InAppNotification::where('type', 'invoice_reminder')->count());
    }

    public function test_overdue_invoices_are_marked(): void
    {
        $invoice = app(InvoiceService::class)->createForOrder(
            $this->completedBookOrder($this->student(), $this->book())
        );

        $invoice->update([
            'status' => 'sent',
            'due_at' => today()->subDay(),
        ]);

        $this->assertSame(1, app(InvoiceService::class)->markOverdue());
        $this->assertSame('overdue', $invoice->fresh()->status);
    }

    public function test_stale_pending_zinipay_orders_are_failed_after_24_hours_only(): void
    {
        $student = $this->student();
        $oldPending = $this->completedBookOrder($student, $this->book());
        $oldPending->forceFill([
            'status' => 'pending',
            'created_at' => now()->subHours(25),
        ])->save();

        $recentPending = $this->completedBookOrder($student, $this->book());
        $recentPending->forceFill([
            'status' => 'pending',
            'created_at' => now()->subHours(23),
        ])->save();

        $manualPending = $this->completedBookOrder($student, $this->book());
        $manualPending->forceFill([
            'status' => 'pending',
            'payment_method' => 'manual',
            'created_at' => now()->subDays(2),
        ])->save();

        $this->artisan('orders:fail-stale-zinipay')->assertExitCode(0);

        $this->assertSame('failed', $oldPending->fresh()->status);
        $this->assertSame('pending', $recentPending->fresh()->status);
        $this->assertSame('pending', $manualPending->fresh()->status);
    }

    public function test_student_invoice_pages_are_owner_only(): void
    {
        $owner = $this->student();
        $other = $this->student();

        $invoice = app(InvoiceService::class)->createForOrder(
            $this->completedBookOrder($owner, $this->book())
        );

        $this->actingAs($owner, 'web')
            ->get('/invoices')
            ->assertOk();

        $this->actingAs($owner, 'web')
            ->get("/invoices/{$invoice->id}")
            ->assertOk();

        $this->actingAs($other, 'web')
            ->get("/invoices/{$invoice->id}")
            ->assertForbidden();
    }
}
