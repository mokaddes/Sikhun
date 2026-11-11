<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentProfileAndScopedLeaderboardTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(array $attributes = []): Student
    {
        static $counter = 1;
        $counter++;

        return Student::create(array_merge([
            'name' => 'Student ' . $counter,
            'email' => "student{$counter}@sikhun.test",
            'phone_hash' => hash('sha256', "0170000000{$counter}"),
            'password' => Hash::make('secret123'),
            'type' => 'hsc',
            'status' => 'active',
            'wallet_balance' => 0,
            'referral_code' => 'REF' . strtoupper(uniqid()),
        ], $attributes));
    }

    public function test_student_can_update_education_type_and_preset_avatar(): void
    {
        $student = $this->createStudent([
            'type' => 'hsc',
            'avatar' => null,
        ]);

        $response = $this->actingAs($student, 'web')
            ->put('/profile/info', [
                'name' => 'Tanvir Ahmed',
                'email' => $student->email,
                'type' => 'admission',
                'avatar' => 'rocket',
            ]);

        $response->assertRedirect();
        $student->refresh();
        $this->assertSame('admission', $student->type);
        $this->assertSame('rocket', $student->avatar);
        $this->assertSame('Tanvir Ahmed', $student->name);
    }

    public function test_student_can_upload_custom_avatar_image(): void
    {
        Storage::fake('public');

        $student = $this->createStudent([
            'type' => 'ssc',
        ]);

        $file = UploadedFile::fake()->image('custom_avatar.png', 200, 200);

        $response = $this->actingAs($student, 'web')
            ->post('/profile/info', [
                '_method' => 'put',
                'name' => $student->name,
                'email' => $student->email,
                'type' => 'job',
                'avatar_file' => $file,
            ]);

        $response->assertRedirect();
        $student->refresh();
        $this->assertSame('job', $student->type);
        $this->assertNotNull($student->avatar);
        $this->assertStringStartsWith('/storage/avatars/', $student->avatar);

        $storedPath = str_replace('/storage/', '', $student->avatar);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_leaderboard_is_strictly_scoped_to_student_education_type(): void
    {
        $student = $this->createStudent([
            'type' => 'admission',
        ]);

        $response = $this->actingAs($student, 'web')
            ->get('/leaderboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Leaderboard/Index')
            ->where('studentType', 'admission')
            ->where('filters.type', 'admission')
        );
    }

    public function test_student_can_register_push_notification_subscription(): void
    {
        $student = $this->createStudent();

        $response = $this->actingAs($student, 'web')
            ->postJson('/notifications/push-subscription', [
                'subscription' => [
                    'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-token',
                    'keys' => [
                        'auth' => 'fake-auth',
                        'p256dh' => 'fake-p256dh',
                    ],
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'student_id' => $student->id,
            'channel' => 'web',
        ]);
    }
}
