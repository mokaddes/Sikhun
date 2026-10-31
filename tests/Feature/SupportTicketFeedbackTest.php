<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Student\FeedbackController as StudentFeedbackController;
use App\Http\Controllers\Student\TicketController as StudentTicketController;
use App\Models\Admin;
use App\Models\AiSession;
use App\Models\Feedback;
use App\Models\Student;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SupportTicketFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/support/conversation/ticket', [\App\Http\Controllers\Public\SupportController::class, 'handoff']);
        Route::middleware(['web', 'auth:web'])->group(function () {
            Route::get('/support/tickets', [StudentTicketController::class, 'index']);
            Route::post('/support/tickets', [StudentTicketController::class, 'store']);
            Route::get('/support/tickets/{ticket}', [StudentTicketController::class, 'show']);
            Route::post('/support/tickets/{ticket}/messages', [StudentTicketController::class, 'reply']);
            Route::get('/feedback', [StudentFeedbackController::class, 'index']);
            Route::post('/feedback', [StudentFeedbackController::class, 'store']);
        });
        Route::middleware(['web', 'auth:admin'])->group(function () {
            Route::get('/admin/tickets', [AdminTicketController::class, 'index']);
            Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show']);
            Route::post('/admin/tickets/{ticket}/messages', [AdminTicketController::class, 'reply']);
            Route::patch('/admin/tickets/{ticket}', [AdminTicketController::class, 'update']);
            Route::get('/admin/feedback', [AdminFeedbackController::class, 'index']);
            Route::patch('/admin/feedback/{feedback}', [AdminFeedbackController::class, 'update']);
        });
    }

    private function student(): Student
    {
        return Student::create([
            'name' => 'Support Student',
            'email' => 'student-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'SUP'.strtoupper(uniqid()),
        ]);
    }

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Support Admin',
            'email' => 'admin-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_student_can_create_a_ticket_and_only_view_their_own_tickets(): void
    {
        $owner = $this->student();
        $other = $this->student();

        $this->actingAs($owner, 'web')->get('/support/tickets')->assertOk();

        $response = $this->actingAs($owner, 'web')->post('/support/tickets', [
            'subject' => 'Cannot open a lesson',
            'category' => 'technical',
            'message' => 'The lesson page returns an error.',
        ]);

        $ticket = SupportTicket::with('messages')->firstOrFail();
        $response->assertRedirect('/support/tickets/'.$ticket->id);
        $this->assertSame('open', $ticket->status);
        $this->assertSame('normal', $ticket->priority);
        $this->assertNotNull($ticket->sla_due_at);
        $this->assertSame('student', $ticket->messages[0]->sender_type);
        $this->assertSame('The lesson page returns an error.', $ticket->messages[0]->body);

        $this->actingAs($other, 'web')
            ->get('/support/tickets/'.$ticket->id)
            ->assertNotFound();
    }

    public function test_student_can_handoff_owned_conversation_with_transcript_and_repeat_safely(): void
    {
        $student = $this->student();
        $conversation = \App\Models\SupportConversation::create([
            'student_id' => $student->id,
            'status' => 'open',
            'bot_enabled' => true,
        ]);
        $conversation->messages()->createMany([
            ['sender_type' => 'student', 'message' => 'I cannot open my lesson.'],
            ['sender_type' => 'bot', 'message' => 'Please refresh and try again.'],
            ['sender_type' => 'student', 'message' => 'It still shows an error.'],
        ]);

        $response = $this->actingAs($student, 'web')->postJson('/support/conversation/ticket', [
            'conversation_id' => $conversation->id,
        ]);

        $ticket = SupportTicket::with('messages')->firstOrFail();
        $response->assertOk()
            ->assertJsonPath('ticket_id', $ticket->id)
            ->assertJsonPath('redirect_url', url('/support/tickets/'.$ticket->id));
        $this->assertSame('It still shows an error.', $ticket->subject);
        $this->assertSame($conversation->id, $ticket->conversation_id);
        $this->assertSame('open', $ticket->status);
        $this->assertNotNull($ticket->sla_due_at);
        $this->assertSame([
            ['student', 'I cannot open my lesson.'],
            ['bot', 'Please refresh and try again.'],
            ['student', 'It still shows an error.'],
        ], $ticket->messages->map(fn ($message) => [$message->sender_type, $message->body])->all());
        $this->assertFalse($conversation->fresh()->bot_enabled);

        $this->actingAs($student, 'web')->postJson('/support/conversation/ticket', [
            'conversation_id' => $conversation->id,
        ])->assertOk()->assertJsonPath('ticket_id', $ticket->id);
        $this->assertSame(1, SupportTicket::count());
        $this->assertSame(3, $ticket->messages()->count());
    }

    public function test_guest_handoff_requires_login_without_creating_a_ticket(): void
    {
        $conversation = \App\Models\SupportConversation::create([
            'guest_token' => 'guest-conversation-token',
            'status' => 'open',
            'bot_enabled' => true,
        ]);
        $conversation->messages()->create(['sender_type' => 'student', 'message' => 'I need help.']);

        $this->postJson('/support/conversation/ticket', [
            'conversation_id' => $conversation->id,
        ])->assertUnauthorized()
            ->assertJsonPath('login_url', route('login'));

        $this->assertSame(0, SupportTicket::count());
        $this->assertTrue($conversation->fresh()->bot_enabled);
    }

    public function test_student_cannot_handoff_another_students_conversation(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $conversation = \App\Models\SupportConversation::create([
            'student_id' => $other->id,
            'status' => 'open',
            'bot_enabled' => true,
        ]);

        $this->actingAs($owner, 'web')->postJson('/support/conversation/ticket', [
            'conversation_id' => $conversation->id,
        ])->assertNotFound();

        $this->assertSame(0, SupportTicket::count());
        $this->assertTrue($conversation->fresh()->bot_enabled);
    }

    public function test_student_cannot_attach_another_students_support_conversation(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $conversation = \App\Models\SupportConversation::create([
            'student_id' => $other->id,
            'status' => 'open',
            'bot_enabled' => true,
        ]);

        $this->actingAs($owner, 'web')->post('/support/tickets', [
            'subject' => 'Conversation handoff',
            'message' => 'Please help.',
            'conversation_id' => $conversation->id,
        ])->assertNotFound();

        $this->assertSame(0, SupportTicket::count());
    }

    public function test_admin_can_reply_assign_and_change_ticket_status(): void
    {
        $student = $this->student();
        $admin = $this->admin();
        $ticket = SupportTicket::create([
            'student_id' => $student->id,
            'subject' => 'Billing question',
            'priority' => 'normal',
            'status' => 'open',
            'sla_due_at' => now()->addDay(),
        ]);

        $this->actingAs($admin, 'admin')->get('/admin/tickets')->assertOk();
        $this->actingAs($admin, 'admin')->get("/admin/tickets/{$ticket->id}")->assertOk();

        $this->actingAs($admin, 'admin')
            ->post("/admin/tickets/{$ticket->id}/messages", ['message' => 'We are checking this.'])
            ->assertRedirect();

        $this->actingAs($admin, 'admin')
            ->patch("/admin/tickets/{$ticket->id}", [
                'status' => 'resolved',
                'priority' => 'high',
                'assigned_admin_id' => $admin->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
            'priority' => 'high',
            'assigned_admin_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'body' => 'We are checking this.',
        ]);
    }

    public function test_ai_answer_reports_capture_the_answer_and_require_session_ownership(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $session = AiSession::create([
            'student_id' => $owner->id,
            'source_type' => 'none',
            'messages' => [
                ['role' => 'user', 'content' => 'What is photosynthesis?'],
                ['role' => 'assistant', 'content' => 'Photosynthesis converts light into chemical energy.'],
            ],
        ]);

        $this->actingAs($owner, 'web')->post('/feedback', [
            'type' => 'answer_report',
            'target_type' => 'ai_answer',
            'target_id' => $session->id,
            'target_key' => '1',
            'message' => 'This answer missed an important detail.',
        ])->assertRedirect();

        $feedback = Feedback::firstOrFail();
        $this->assertSame('new', $feedback->status);
        $this->assertSame('Photosynthesis converts light into chemical energy.', $feedback->context['answer']);
        $this->assertSame(1, $feedback->context['answer_index']);

        $this->actingAs($other, 'web')->post('/feedback', [
            'type' => 'answer_report',
            'target_type' => 'ai_answer',
            'target_id' => $session->id,
            'target_key' => '1',
        ])->assertNotFound();

        $this->assertSame(1, Feedback::count());
    }

    public function test_admin_can_review_feedback_and_save_an_internal_note(): void
    {
        $student = $this->student();
        $admin = $this->admin();
        $feedback = Feedback::create([
            'student_id' => $student->id,
            'type' => 'feedback',
            'message' => 'The new practice page is helpful.',
            'status' => 'new',
        ]);

        $this->actingAs($admin, 'admin')->get('/admin/feedback')->assertOk();

        $this->actingAs($admin, 'admin')
            ->patch("/admin/feedback/{$feedback->id}", [
                'status' => 'reviewed',
                'admin_note' => 'Share with the product team.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback', [
            'id' => $feedback->id,
            'status' => 'reviewed',
            'admin_note' => 'Share with the product team.',
        ]);
    }
}
