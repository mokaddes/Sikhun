<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\MyBook;
use App\Models\Order;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Services\PurchaseService;
use App\Services\SearchService;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class P1AndP2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(array $attrs = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Test Student',
            'email' => 'st-'.uniqid().'@example.com',
            'password' => 'password123',
            'type' => 'hsc',
            'wallet_balance' => 500,
            'referral_code' => 'REF'.strtoupper(uniqid()),
        ], $attrs));
    }

    public function test_mobile_api_parity_endpoints_work_for_onboarding_feedback_qb_and_my_book_reader(): void
    {
        $student = $this->makeStudent(['type' => 'hsc']);
        Sanctum::actingAs($student);

        // Onboarding API
        $this->getJson('/api/onboarding')->assertOk()->assertJsonPath('success', true);
        $this->postJson('/api/onboarding/step', ['step' => 1, 'goal' => 'hsc'])->assertOk();
        $this->postJson('/api/onboarding/skip')->assertOk();

        // Feedback API
        $this->postJson('/api/feedback', [
            'type' => 'feedback',
            'rating' => 5,
            'message' => 'Great mobile app!',
        ])->assertStatus(201);
        $this->getJson('/api/feedback')->assertOk()->assertJsonPath('success', true);

        // Question Bank Practice API
        $subject = QuestionSubject::create([
            'name_bn' => 'পদার্থবিজ্ঞান',
            'name_en' => 'Physics',
            'slug' => 'physics-'.uniqid(),
            'segment' => 'hsc',
            'is_active' => true,
        ]);
        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $subject->id,
            'origin_tag' => 'HSC-2025',
            'question_text' => 'What is F=ma?',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Newton 2nd Law']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);
        $this->getJson('/api/question-bank-practice')->assertOk()->assertJsonCount(1, 'data.questions.data');
        $this->postJson('/api/question-bank-practice/start-exam', ['question_count' => 5])->assertStatus(201);

        // MyBook Reader API
        $myBook = MyBook::create([
            'student_id' => $student->id,
            'title' => 'My Physics Notes',
            'file_path' => 'my-books/sample.pdf',
            'total_pages' => 10,
            'processing_status' => 'completed',
        ]);
        $this->getJson("/api/my-books/{$myBook->id}/reader")->assertOk()->assertJsonPath('data.my_book.id', $myBook->id);
        $this->postJson("/api/my-books/{$myBook->id}/reader/progress", ['page' => 4])->assertOk();
        $this->assertSame(4, $myBook->fresh()->last_page);
    }

    public function test_question_bank_is_unlocked_for_ssc_hsc_and_university_students(): void
    {
        $hscStudent = $this->makeStudent(['type' => 'hsc']);
        $subject = QuestionSubject::create([
            'name_bn' => 'রসায়ন',
            'name_en' => 'Chemistry',
            'slug' => 'chem-'.uniqid(),
            'segment' => 'hsc',
            'is_active' => true,
        ]);
        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $subject->id,
            'origin_tag' => 'HSC-2025',
            'question_text' => 'H2O is?',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Water']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $this->actingAs($hscStudent, 'web')
            ->get(route('student.question-bank.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Student/QuestionBank/Index')
                ->where('isUnavailable', false)
                ->where('activeSector', 'hsc')
                ->has('questions.data', 1)
            );
    }

    public function test_social_oauth_respects_admin_toggle_and_issues_sanctum_token(): void
    {
        $this->postJson('/api/auth/oauth/google/exchange', ['access_token' => 'tok'])
            ->assertStatus(422);

        $settings = app(SiteSettingService::class);
        $settings->set('oauth_google_enabled', '1');

        Http::fake([
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'oauth.student@example.com',
                'name' => 'OAuth Student',
            ], 200),
        ]);

        $resp = $this->postJson('/api/auth/oauth/google/exchange', ['access_token' => 'valid_google_token'])
            ->assertOk();

        $this->assertNotEmpty($resp->json('data.token'));
        $this->assertDatabaseHas('students', ['email' => 'oauth.student@example.com']);
    }

    public function test_student_can_export_data_and_delete_account(): void
    {
        $student = $this->makeStudent(['email' => 'exportme@example.com']);

        $this->actingAs($student, 'web')
            ->get('/profile/export')
            ->assertOk()
            ->assertJsonPath('profile.email', 'exportme@example.com');

        $this->actingAs($student, 'web')
            ->delete('/profile/account', ['confirmation' => 'DELETE'])
            ->assertRedirect(route('home'));

        $fresh = $student->fresh();
        $this->assertSame('inactive', $fresh->status);
        $this->assertNull($fresh->email);
    }

    public function test_mhpay_webhook_verifies_hmac_signature_and_prevents_stale_replay(): void
    {
        config(['mhpay.webhook_secret' => 'whsec_test_secret']);

        $student = $this->makeStudent();
        $book = Book::create([
            'title' => 'Webhook Book',
            'slug' => 'webhook-book',
            'price' => 150,
            'is_free' => false,
            'is_published' => true,
        ]);
        $order = Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-WH-1',
            'orderable_type' => 'book',
            'orderable_id' => $book->id,
            'amount' => 150,
            'payment_method' => 'mhpay',
            'gateway_invoice_id' => 'MH-INV-1',
            'status' => 'pending',
        ]);

        $payload = json_encode(['invoice_id' => 'MH-INV-1', 'reference' => 'SKH-WH-1']);
        $staleTs = time() - 600;
        $staleSig = hash_hmac('sha256', "{$staleTs}.{$payload}", 'whsec_test_secret');

        $this->call('POST', '/api/mhpay/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => "t={$staleTs},v1={$staleSig}",
        ], $payload)->assertStatus(403);

        Http::fake([
            '*/api/v1/invoices/MH-INV-1/status' => Http::response([
                'id' => 'MH-INV-1',
                'status' => 'paid',
                'verification' => ['transaction_id' => 'MHTRX-1'],
            ], 200),
        ]);

        $freshTs = time();
        $freshSig = hash_hmac('sha256', "{$freshTs}.{$payload}", 'whsec_test_secret');

        $this->call('POST', '/api/mhpay/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => "t={$freshTs},v1={$freshSig}",
        ], $payload)->assertOk();

        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_purchase_service_rejects_insufficient_wallet_balance(): void
    {
        $student = $this->makeStudent(['wallet_balance' => 10]);
        $book = Book::create([
            'title' => 'Expensive Book',
            'slug' => 'expensive-book',
            'price' => 250,
            'is_free' => false,
            'is_published' => true,
        ]);

        $this->expectException(\RuntimeException::class);
        app(PurchaseService::class)->purchaseBook($student, $book, 'wallet');
    }

    public function test_observability_health_command_and_search_service_work(): void
    {
        $this->artisan('monitor:health')->assertExitCode(0);

        Book::create([
            'title' => 'HSC Higher Math Part 1',
            'slug' => 'hsc-higher-math-1',
            'subject' => 'Math',
            'is_published' => true,
        ]);

        $results = app(SearchService::class)->searchBooks('Higher Math');
        $this->assertCount(1, $results);
    }
}
