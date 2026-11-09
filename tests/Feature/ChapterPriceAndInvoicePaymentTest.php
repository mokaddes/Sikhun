<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\ExamSession;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapterPriceAndInvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): Admin
    {
        return Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@sikhun.test',
            'password' => bcrypt('secret123'),
        ]);
    }

    protected function student(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Test Student',
            'email' => 'student-' . uniqid() . '@sikhun.test',
            'password' => bcrypt('secret123'),
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'STU-' . strtoupper(uniqid()),
            'wallet_balance' => 0.00,
        ], $attributes));
    }

    public function test_admin_can_update_chapter_prices(): void
    {
        $admin = $this->admin();
        $book = Book::create([
            'title' => 'Physics First Paper',
            'slug' => 'physics-1st',
            'is_published' => true,
            'is_free' => false,
            'price' => 120.00,
        ]);

        $ch1 = BookChapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1: Vectors',
            'slug' => 'ch-1-vectors',
            'start_page' => 1,
            'end_page' => 30,
            'price' => 20.00,
        ]);

        $ch2 = BookChapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 2: Dynamics',
            'slug' => 'ch-2-dynamics',
            'start_page' => 31,
            'end_page' => 60,
            'price' => 25.00,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.books.chapters.prices', $book), [
                'chapters' => [
                    ['id' => $ch1->id, 'price' => 35.00],
                    ['id' => $ch2->id, 'price' => 40.00],
                ],
            ]);

        $response->assertRedirect();
        $this->assertEquals(35.00, (float) $ch1->fresh()->price);
        $this->assertEquals(40.00, (float) $ch2->fresh()->price);
    }

    public function test_student_can_pay_invoice_using_wallet_balance(): void
    {
        $student = $this->student(['wallet_balance' => 500.00]);

        $plan = Plan::create([
            'name' => 'Pro Study',
            'slug' => 'pro-study',
            'price_monthly' => 99.00,
            'ai_chat_minutes' => 120,
            'ai_exam_count' => 30,
            'is_free' => false,
            'is_active' => true,
        ]);

        $invoice = Invoice::create([
            'student_id' => $student->id,
            'invoice_number' => 'INV-TEST-001',
            'total' => 99.00,
            'subtotal' => 99.00,
            'status' => 'sent',
            'due_at' => now()->addDays(3),
            'meta' => ['plan_id' => $plan->id],
        ]);

        $response = $this->actingAs($student, 'web')
            ->post(route('invoices.pay', $invoice), [
                'payment_method' => 'wallet',
            ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals(401.00, (float) $student->fresh()->wallet_balance);
        $this->assertDatabaseHas('student_subscriptions', [
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_student_who_owns_chapter_has_ai_chat_access(): void
    {
        $student = $this->student(['wallet_balance' => 100.00]);
        $plan = Plan::create([
            'name' => 'Pro Study',
            'slug' => 'pro-study-2',
            'price_monthly' => 99.00,
            'ai_chat_minutes' => 120,
            'ai_exam_count' => 30,
            'is_free' => false,
            'is_active' => true,
        ]);

        StudentSubscription::create([
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'ai_chat_minutes_remaining' => 50,
            'ai_exam_count_remaining' => 10,
        ]);

        $book = Book::create([
            'title' => 'Chemistry 1st Paper',
            'slug' => 'chem-1',
            'price' => 200.00,
            'is_published' => true,
            'is_free' => false,
        ]);

        $chapter = BookChapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1: Laboratory Safety',
            'slug' => 'ch-1-lab',
            'start_page' => 1,
            'end_page' => 25,
            'price' => 30.00,
        ]);

        // Student has not bought book or chapter yet -> AI chat creation returns 403
        $resp = $this->actingAs($student, 'web')
            ->post(route('ai-chat.create'), [
                'source_book_id' => $book->id,
                'message' => 'Hello AI',
            ]);
        $resp->assertStatus(403);

        // Student purchases chapter
        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $chapter->id,
            'price' => 30.00,
            'purchased_at' => now(),
            'source' => 'purchased',
        ]);

        // Now AI chat creation succeeds
        $resp2 = $this->actingAs($student, 'web')
            ->post(route('ai-chat.create'), [
                'source_book_id' => $book->id,
                'message' => 'Explain chapter 1',
            ]);
        $resp2->assertRedirect();
    }

    public function test_exam_result_print_page_renders_successfully(): void
    {
        $student = $this->student();
        $exam = ExamSession::create([
            'student_id' => $student->id,
            'source_type' => 'book',
            'mode' => 'practice',
            'config' => ['type' => 'mcq'],
            'title' => 'Physics Model Test 1',
            'total' => 10,
            'correct' => 8,
            'score' => 8.0,
            'percentage' => 80.0,
            'status' => 'completed',
            'started_at' => now()->subMinutes(15),
            'completed_at' => now(),
            'time_taken_seconds' => 900,
            'questions' => [
                [
                    'question' => 'What is the SI unit of force?',
                    'options' => ['Joule', 'Newton', 'Watt', 'Pascal'],
                    'correct_answer' => 'Newton',
                    'student_answer' => 'Newton',
                    'is_correct' => true,
                    'explanation' => 'Force = mass x acceleration; unit is Newton.',
                ],
            ],
        ]);

        $response = $this->actingAs($student, 'web')
            ->get(route('exams.print', $exam));

        $response->assertStatus(200);
        $response->assertSee('Physics Model Test 1');
        $response->assertSee('What is the SI unit of force?');
        $response->assertSee('Newton');
    }
}
