<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Student;
use App\Models\SupportTicket;
use App\Services\SupportBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportWidgetAndNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Widget Student',
            'email' => 'widget-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'WID'.strtoupper(uniqid()),
        ]);
    }

    private function notification(Student $student, string $title = 'Notice'): Notification
    {
        return Notification::create([
            'student_id' => $student->id,
            'type' => 'general',
            'title' => $title,
            'body' => 'Notification body',
        ]);
    }

    public function test_support_bot_prompt_uses_active_language_and_obeys_explicit_language_requests(): void
    {
        $service = app(SupportBotService::class);

        $this->assertStringContainsString('active interface language is Bengali', $service->systemPrompt('bn'));
        $this->assertStringContainsString('active interface language is English', $service->systemPrompt('en'));
        $this->assertStringContainsString('বাংলায় বলুন', $service->systemPrompt('en'));
    }

    public function test_chat_messages_do_not_consume_the_human_handoff_rate_limit(): void
    {
        $student = $this->student();

        for ($index = 1; $index <= 4; $index++) {
            $this->actingAs($student, 'web')
                ->postJson('/support/message', [
                    'message' => "Please help with issue {$index}.",
                    'locale' => 'bn',
                ])
                ->assertOk();
        }

        $conversation = $this->actingAs($student, 'web')
            ->getJson('/support/conversation')
            ->assertOk()
            ->json('conversation_id');

        $this->actingAs($student, 'web')
            ->postJson('/support/conversation/ticket', ['conversation_id' => $conversation])
            ->assertOk();

        $this->assertSame(1, SupportTicket::where('student_id', $student->id)->count());
    }

    public function test_student_can_dismiss_one_notification_and_clear_the_rest_without_affecting_others(): void
    {
        $student = $this->student();
        $otherStudent = $this->student();
        $dismissed = $this->notification($student, 'Dismissed');
        $remaining = $this->notification($student, 'Remaining');
        $other = $this->notification($otherStudent, 'Other student');

        $this->actingAs($student, 'web')
            ->deleteJson("/notifications/{$dismissed->id}")
            ->assertOk();

        $this->assertDatabaseMissing('notifications', ['id' => $dismissed->id]);
        $this->assertDatabaseHas('notifications', ['id' => $remaining->id]);

        $this->actingAs($student, 'web')->deleteJson('/notifications')->assertOk();

        $this->assertDatabaseMissing('notifications', ['id' => $remaining->id]);
        $this->assertDatabaseHas('notifications', ['id' => $other->id]);
    }

    public function test_student_cannot_dismiss_another_students_notification(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $notification = $this->notification($owner);

        $this->actingAs($other, 'web')
            ->deleteJson("/notifications/{$notification->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }
}
