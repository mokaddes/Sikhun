<?php

namespace Tests\Feature;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Order;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionAndMistakeReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Admission Student',
            'email' => 'admission-' . uniqid() . '@sikhun.test',
            'password' => bcrypt('password'),
            'type' => 'admission',
            'is_active' => true,
            'wallet_balance' => 0,
            'referral_code' => 'REF' . strtoupper(uniqid()),
        ], $attributes));
    }

    private function createSubject(array $attributes = []): QuestionSubject
    {
        return QuestionSubject::create(array_merge([
            'name_bn' => 'পদার্থবিজ্ঞান',
            'name_en' => 'Physics',
            'slug' => 'physics-' . uniqid(),
            'segment' => 'admission',
            'is_active' => true,
        ], $attributes));
    }

    public function test_admission_student_dashboard_surfaces_education_type_and_pending_order(): void
    {
        $student = $this->createStudent(['type' => 'admission']);

        // Create pending order for ৳644
        $pendingOrder = Order::create([
            'student_id' => $student->id,
            'order_number' => 'ORD-644-' . uniqid(),
            'orderable_type' => 'wallet_recharge',
            'amount' => 644.00,
            'payment_method' => 'manual',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student, 'web')->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Dashboard/Index')
            ->has('pendingOrder')
            ->where('pendingOrder.id', $pendingOrder->id)
            ->where('pendingOrder.amount', '644.00')
            ->where('auth.student.education_type', 'admission')
            ->where('auth.student.type', 'admission')
        );
    }

    public function test_wallet_index_surfaces_pending_orders_alert(): void
    {
        $student = $this->createStudent();

        $order = Order::create([
            'student_id' => $student->id,
            'order_number' => 'ORD-PENDING-' . uniqid(),
            'orderable_type' => 'wallet_recharge',
            'amount' => 644.00,
            'payment_method' => 'mhpay',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student, 'web')->get('/wallet');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Wallet/Index')
            ->has('pendingOrders')
            ->where('pendingOrders.0.id', $order->id)
            ->where('pendingOrders.0.amount', '644.00')
        );
    }

    public function test_student_can_submit_and_verify_transaction_id_for_pending_order(): void
    {
        $student = $this->createStudent();

        $order = Order::create([
            'student_id' => $student->id,
            'order_number' => 'ORD-TRX-' . uniqid(),
            'orderable_type' => 'wallet_recharge',
            'amount' => 644.00,
            'payment_method' => 'manual',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student, 'web')
            ->post("/wallet/orders/{$order->id}/verify", [
                'transaction_id' => 'TXN987654321',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('TXN987654321', $order->gateway_transaction_id);
    }

    public function test_student_cannot_verify_another_students_order(): void
    {
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        $order = Order::create([
            'student_id' => $student2->id,
            'order_number' => 'ORD-OTHER-' . uniqid(),
            'orderable_type' => 'wallet_recharge',
            'amount' => 500.00,
            'payment_method' => 'manual',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student1, 'web')
            ->post("/wallet/orders/{$order->id}/verify", [
                'transaction_id' => 'HACK12345',
            ]);

        $response->assertForbidden();
    }

    public function test_exam_mistakes_are_recorded_when_exam_is_completed(): void
    {
        $student = $this->createStudent(['type' => 'admission']);
        $subject = $this->createSubject();

        $qb = QuestionBank::create([
            'segment' => 'admission',
            'allowed_student_types' => ['admission'],
            'subject_id' => $subject->id,
            'origin_tag' => 'DU-A-2023',
            'institution' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            'exam_name' => 'DU KA Unit 2023',
            'question_text' => 'What is the SI unit of force?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'Joule'],
                ['key' => 'B', 'text' => 'Newton'],
                ['key' => 'C', 'text' => 'Watt'],
                ['key' => 'D', 'text' => 'Pascal'],
            ],
            'correct_option' => 'B',
            'marks' => 1.0,
            'negative_marks' => 0.25,
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $examSession = ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'exam',
            'title' => 'Admission Model Test',
            'source_type' => 'topic',
            'config' => ['type' => 'mcq', 'count' => 1],
            'questions' => [
                [
                    'id' => 1,
                    'question_bank_id' => $qb->id,
                    'question_text' => $qb->question_text,
                    'type' => 'mcq',
                    'options' => $qb->options,
                    'correct_option' => 'B',
                    'correct_answer' => 'Newton',
                    'subject_id' => $subject->id,
                    'origin_tag' => 'DU-A-2023',
                    'institution' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
                ],
            ],
            'total' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Student submits wrong answer 'A' (Joule instead of Newton)
        $response = $this->actingAs($student, 'web')
            ->post("/exams/{$examSession->id}/complete", [
                'answers' => [
                    1 => 'A',
                ],
            ]);

        $response->assertRedirect("/exams/{$examSession->id}/result");

        // Verify mistake was recorded in exam_answers table
        $this->assertDatabaseHas('exam_answers', [
            'student_id' => $student->id,
            'exam_session_id' => $examSession->id,
            'question_bank_id' => $qb->id,
            'is_correct' => false,
            'student_answer' => 'A',
            'correct_option' => 'B',
        ]);
    }

    public function test_mistake_review_index_displays_mistakes_and_filters(): void
    {
        $student = $this->createStudent(['type' => 'admission']);
        $subject = $this->createSubject();

        $session = ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'exam',
            'title' => 'Sample Test',
            'source_type' => 'topic',
            'config' => ['type' => 'mcq'],
            'total' => 1,
            'status' => 'completed',
        ]);

        // Seed an ExamAnswer directly
        ExamAnswer::create([
            'student_id' => $student->id,
            'exam_session_id' => $session->id,
            'question_text' => 'Derivative of sin(x)?',
            'question_type' => 'mcq',
            'options' => ['A' => 'cos(x)', 'B' => '-cos(x)'],
            'correct_option' => 'A',
            'correct_answer' => 'cos(x)',
            'student_answer' => 'B',
            'is_correct' => false,
            'subject_id' => $subject->id,
            'subject_name' => $subject->name_bn,
            'origin_tag' => 'RU-C-2022',
            'institution' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
        ]);

        $response = $this->actingAs($student, 'web')->get('/mistakes');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Mistakes/Index')
            ->has('mistakes.data', 1)
            ->where('stats.total_mistakes', 1)
            ->has('subjects')
            ->has('institutions')
        );

        // Test filtering by institution
        $responseRu = $this->actingAs($student, 'web')->get('/mistakes?institution=RU');
        $responseRu->assertOk();
        $responseRu->assertInertia(fn ($page) => $page
            ->has('mistakes.data', 1)
        );

        // Test filtering by unknown institution returns empty list
        $responseBuet = $this->actingAs($student, 'web')->get('/mistakes?institution=BUET');
        $responseBuet->assertOk();
        $responseBuet->assertInertia(fn ($page) => $page
            ->has('mistakes.data', 0)
        );
    }

    public function test_student_can_practice_mistakes_and_master_them(): void
    {
        $student = $this->createStudent(['type' => 'admission']);
        $subject = $this->createSubject();

        $session = ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'exam',
            'title' => 'Initial Test',
            'source_type' => 'topic',
            'config' => ['type' => 'mcq'],
            'total' => 1,
            'status' => 'completed',
        ]);

        // Seed mistake
        $mistake = ExamAnswer::create([
            'student_id' => $student->id,
            'exam_session_id' => $session->id,
            'question_text' => 'Integral of 1/x dx?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'ln|x|'],
                ['key' => 'B', 'text' => 'x^2'],
            ],
            'correct_option' => 'A',
            'correct_answer' => 'ln|x|',
            'student_answer' => 'B',
            'is_correct' => false,
            'subject_id' => $subject->id,
            'subject_name' => $subject->name_bn,
            'origin_tag' => 'DU-A-2021',
            'institution' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
        ]);

        // Launch practice re-test
        $practiceResponse = $this->actingAs($student, 'web')
            ->postJson('/mistakes/practice', [
                'subject_id' => $subject->id,
                'limit' => 10,
            ]);

        $practiceResponse->assertOk();
        $practiceResponse->assertJson(['success' => true]);

        $examId = $practiceResponse->json('exam_id');
        $this->assertNotNull($examId);

        $revisionExam = ExamSession::find($examId);
        $this->assertEquals('practice', $revisionExam->mode);
        $this->assertEquals('in_progress', $revisionExam->status);
        $this->assertCount(1, $revisionExam->questions);

        // Now complete the revision exam with the CORRECT answer 'A'
        $completeResponse = $this->actingAs($student, 'web')
            ->post("/exams/{$revisionExam->id}/complete", [
                'answers' => [
                    1 => 'A', // correct!
                ],
            ]);

        $completeResponse->assertRedirect("/exams/{$revisionExam->id}/result");

        // The answer for this revision session is now correct (mastered!)
        $this->assertDatabaseHas('exam_answers', [
            'student_id' => $student->id,
            'exam_session_id' => $revisionExam->id,
            'is_correct' => true,
            'student_answer' => 'A',
        ]);
    }
}
