<?php

namespace Tests\Feature;

use App\Contracts\AiProviderContract;
use App\Models\AiLog;
use App\Models\AiProvider;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PushSubscription;
use App\Models\Student;
use App\Services\Ai\AiCostGovernanceService;
use App\Services\Ai\LoggingAiProviderDecorator;
use App\Services\Media\GoogleDriveStreamService;
use App\Services\NotificationService;
use App\Services\Payment\BkashMerchantService;
use App\Services\Payment\MhpayService;
use App\Services\Payment\NagadMerchantService;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class P0GapImplementationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_zinipay_is_removed_and_bkash_nagad_are_disabled_until_enabled_in_admin(): void
    {
        $resolver = app(PaymentGatewayResolver::class);
        $settings = app(SiteSettingService::class);

        $this->assertNull($resolver->resolve('zinipay'));
        $this->assertInstanceOf(MhpayService::class, $resolver->resolve('mhpay'));
        $this->assertNull($resolver->resolve('bkash'));
        $this->assertNull($resolver->resolve('nagad'));

        $settings->set('payment_bkash_enabled', '1');
        $settings->set('payment_nagad_enabled', '1');

        $this->assertInstanceOf(BkashMerchantService::class, $resolver->resolve('bkash'));
        $this->assertInstanceOf(NagadMerchantService::class, $resolver->resolve('nagad'));
    }

    public function test_bkash_and_nagad_webhooks_verify_and_complete_orders(): void
    {
        $settings = app(SiteSettingService::class);
        $settings->set('payment_bkash_enabled', '1');
        $settings->set('payment_bkash_app_key', 'bk_key');
        $settings->set('payment_bkash_app_secret', 'bk_secret');
        $settings->set('payment_bkash_username', 'bk_user');
        $settings->set('payment_bkash_password', 'bk_pass');
        $settings->set('payment_nagad_enabled', '1');

        $student = Student::create([
            'name' => 'Gateway Student',
            'email' => 'gw@example.com',
            'password' => 'password123',
            'type' => 'hsc',
            'referral_code' => 'GW1234',
        ]);

        $book = Book::create([
            'title' => 'Paid Book',
            'slug' => 'paid-book',
            'price' => 120,
            'is_free' => false,
            'is_published' => true,
        ]);

        $bkashOrder = Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-BK-1001',
            'orderable_type' => 'book',
            'orderable_id' => $book->id,
            'amount' => 120,
            'payment_method' => 'bkash',
            'gateway_invoice_id' => 'BK-PAY-1001',
            'status' => 'pending',
        ]);

        $nagadOrder = Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-NG-1002',
            'orderable_type' => 'book',
            'orderable_id' => $book->id,
            'amount' => 120,
            'payment_method' => 'nagad',
            'gateway_invoice_id' => 'NG-REF-1002',
            'status' => 'pending',
        ]);

        Http::fake([
            '*/tokenized/checkout/token/grant' => Http::response(['id_token' => 'tok_123'], 200),
            '*/tokenized/checkout/execute' => Http::response(['transactionStatus' => 'Completed', 'trxID' => 'BKTRX999'], 200),
            '*/verify/payment/NG-REF-1002' => Http::response(['status' => 'Success', 'issuerPaymentRefNo' => 'NGTRX888'], 200),
        ]);

        $this->postJson('/api/bkash/webhook', [
            'paymentID' => 'BK-PAY-1001',
            'status' => 'success',
        ])->assertOk();

        $this->assertSame('completed', $bkashOrder->fresh()->status);
        $this->assertSame('BKTRX999', $bkashOrder->fresh()->gateway_transaction_id);

        $this->postJson('/api/nagad/webhook', [
            'payment_ref_id' => 'NG-REF-1002',
            'status' => 'Success',
        ])->assertOk();

        $this->assertSame('completed', $nagadOrder->fresh()->status);
        $this->assertSame('NGTRX888', $nagadOrder->fresh()->gateway_transaction_id);
    }

    public function test_sms_otp_respects_admin_toggle_and_registers_and_logs_in_student(): void
    {
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free Plan',
                'price_monthly' => 0,
                'is_free' => true,
                'is_active' => true,
            ]
        );

        // Off by default
        $this->postJson('/otp/send', [
            'phone' => '01711223344',
            'purpose' => 'register',
        ])->assertStatus(422);

        // Turn ON from Admin Settings
        $settings = app(SiteSettingService::class);
        $settings->set('sms_otp_enabled', '1');
        $settings->set('sms_api_url', 'https://sms.example.test/send');
        $settings->set('sms_api_key', 'test-key');

        $capturedCode = null;
        Http::fake(function ($request) use (&$capturedCode) {
            $body = $request->data();
            if (preg_match('/(\d{6})/', $body['message'] ?? '', $m)) {
                $capturedCode = $m[1];
            }

            return Http::response(['ok' => true], 200);
        });

        $this->postJson('/api/auth/otp/send', [
            'phone' => '01711223344',
            'purpose' => 'register',
        ])->assertOk();

        $this->assertNotNull($capturedCode);

        $regResp = $this->postJson('/api/auth/otp/register', [
            'name' => 'OTP Student',
            'phone' => '01711223344',
            'code' => $capturedCode,
            'type' => 'hsc',
        ])->assertStatus(201);

        $this->assertNotEmpty($regResp->json('data.token'));
        $this->assertDatabaseHas('students', ['phone' => '01711223344']);
    }

    public function test_fcm_push_notification_dispatches_when_enabled(): void
    {
        config(['broadcasting.default' => 'log']);
        $settings = app(SiteSettingService::class);
        $settings->set('fcm_enabled', '1');
        $settings->set('fcm_server_key', 'legacy-test-key');

        $student = Student::create([
            'name' => 'Push Student',
            'email' => 'push@example.com',
            'password' => 'password123',
            'type' => 'hsc',
            'referral_code' => 'PSH123',
        ]);

        PushSubscription::create([
            'student_id' => $student->id,
            'channel' => 'fcm',
            'token' => 'fcm_device_token_abc',
        ]);

        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
        ]);

        app(NotificationService::class)->send(
            $student,
            'exam_reminder',
            'Test Push',
            'You have a scheduled exam.',
            ['exam_id' => 5]
        );

        Http::assertSent(fn ($req) => $req->url() === 'https://fcm.googleapis.com/fcm/send'
            && ($req->data()['to'] ?? null) === 'fcm_device_token_abc');
    }

    public function test_ai_cost_governance_blocks_student_when_monthly_budget_exceeded(): void
    {
        $settings = app(SiteSettingService::class);
        $settings->set('ai_default_monthly_cost_limit_usd', '0.50');

        $student = Student::create([
            'name' => 'Cost Student',
            'email' => 'cost@example.com',
            'password' => 'password123',
            'type' => 'hsc',
            'referral_code' => 'CST123',
        ]);

        $provider = AiProvider::create([
            'name' => 'Test OpenAI',
            'type' => 'openai',
            'model_name' => 'gpt-4o-mini',
            'api_key' => 'sk-test',
            'is_active' => true,
        ]);

        AiLog::create([
            'student_id' => $student->id,
            'user_type' => 'student',
            'provider_id' => $provider->id,
            'provider_name' => $provider->name,
            'provider_type' => $provider->type,
            'model_name' => $provider->model_name,
            'use_case' => 'book_chat',
            'prompt' => 'test',
            'status' => 'success',
            'input_tokens' => 1000,
            'output_tokens' => 500,
            'total_tokens' => 1500,
            'cost' => 0.55,
        ]);

        $inner = new class implements AiProviderContract {
            public function chat(array $messages, array $options = []): string { return 'ok'; }
            public function stream(array $messages, array $options = []): \Generator { yield 'ok'; }
            public function isAvailable(): bool { return true; }
        };

        $decorator = new LoggingAiProviderDecorator($inner, $provider, 'book_chat', ['student' => $student]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monthly AI usage budget reached');
        $decorator->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    public function test_course_demo_video_hides_drive_url_and_streams_through_proxy(): void
    {
        $course = Course::create([
            'title' => 'Drive Demo Course',
            'slug' => 'drive-demo-course',
            'description' => 'Course with hidden drive demo',
            'demo_video_url' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz/view?usp=sharing',
            'delivery_type' => 'video',
            'price' => 500,
            'is_active' => true,
        ]);

        $serialized = $course->toArray();
        $this->assertArrayNotHasKey('demo_video_url', $serialized);
        $this->assertTrue($serialized['has_demo_video']);
        $this->assertStringContainsString('/courses/drive-demo-course/demo-stream', $serialized['demo_preview_url']);

        $this->assertSame(
            '1AbCdEfGhIjKlMnOpQrStUvWxYz',
            app(GoogleDriveStreamService::class)->extractFileId($course->demo_video_url)
        );

        Http::fake([
            'https://drive.usercontent.google.com/*' => Http::response('FAKE_MP4_BYTES', 200, [
                'Content-Type' => 'video/mp4',
                'Content-Length' => '14',
            ]),
        ]);

        $resp = $this->get('/courses/drive-demo-course/demo-stream');
        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'video/mp4');
        $this->assertSame('FAKE_MP4_BYTES', $resp->streamedContent());
    }
}
