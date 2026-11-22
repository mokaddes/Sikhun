<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Services\PushNotificationService;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirebasePushNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function seedFcmSettings(): void
    {
        $pairs = [
            'fcm_enabled' => '1',
            'fcm_project_id' => 'sikhun-test',
            'fcm_api_key' => 'AIzaTestKey123',
            'fcm_auth_domain' => 'sikhun-test.firebaseapp.com',
            'fcm_storage_bucket' => 'sikhun-test.appspot.com',
            'fcm_messaging_sender_id' => '1234567890',
            'fcm_app_id' => '1:1234567890:web:abcdef',
            'fcm_vapid_key' => 'BTestVapidKey123456',
        ];

        $service = app(SiteSettingService::class);
        foreach ($pairs as $k => $v) {
            SiteSetting::updateOrCreate(['key' => $k], ['value' => $v]);
            $service->forget($k);
        }
    }

    public function test_firebase_service_worker_is_served_dynamically(): void
    {
        $this->seedFcmSettings();

        $response = $this->get('/firebase-messaging-sw.js');

        $response->assertOk();
        $this->assertStringContainsString('application/javascript', (string) $response->headers->get('Content-Type'));
        $this->assertSame('/', $response->headers->get('Service-Worker-Allowed'));
        $response->assertSee('sikhun-test', false);
        $response->assertSee('AIzaTestKey123', false);
        $response->assertSee('firebase-messaging-compat.js', false);
    }

    public function test_student_can_subscribe_multiple_fcm_tokens_and_unsubscribe_one(): void
    {
        $student = Student::create([
            'name' => 'Push Tester',
            'email' => 'push-'.uniqid().'@sikhun.test',
            'password' => bcrypt('secret123'),
            'referral_code' => 'REF'.uniqid(),
        ]);

        $this->actingAs($student, 'web')
            ->postJson('/notifications/push-subscription', ['token' => 'fcm-token-laptop'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($student, 'web')
            ->postJson('/notifications/push-subscription', ['token' => 'fcm-token-phone'])
            ->assertOk();

        // Duplicate token should not create a third row
        $this->actingAs($student, 'web')
            ->postJson('/notifications/push-subscription', ['token' => 'fcm-token-laptop'])
            ->assertOk();

        $this->assertSame(2, PushSubscription::where('student_id', $student->id)->where('channel', 'fcm')->count());

        $this->actingAs($student, 'web')
            ->deleteJson('/notifications/push-subscription', ['token' => 'fcm-token-laptop'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(1, PushSubscription::where('student_id', $student->id)->where('channel', 'fcm')->count());
        $this->assertDatabaseHas('push_subscriptions', [
            'student_id' => $student->id,
            'channel' => 'fcm',
            'token' => 'fcm-token-phone',
        ]);
    }

    public function test_web_config_reports_configured_only_when_complete(): void
    {
        $push = app(PushNotificationService::class);
        $this->assertFalse($push->webConfig()['configured']);

        $this->seedFcmSettings();
        $cfg = $push->webConfig();
        $this->assertTrue($cfg['enabled']);
        $this->assertTrue($cfg['configured']);
        $this->assertSame('sikhun-test', $cfg['project_id']);
        $this->assertSame('BTestVapidKey123456', $cfg['vapid_key']);
    }
}
