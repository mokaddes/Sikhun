<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Reset Student',
            'email' => 'reset-'.uniqid().'@sikhun.test',
            'password' => 'old-password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'RST'.strtoupper(uniqid()),
        ]);
    }

    public function test_forgot_password_page_renders(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_reset_link_notification_is_sent_for_existing_account(): void
    {
        Notification::fake();

        $student = $this->student();

        $this->post('/forgot-password', ['email' => $student->email])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($student, ResetPasswordNotification::class);
    }

    public function test_unknown_email_gets_the_same_response_to_prevent_enumeration(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@sikhun.test'])
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $student = $this->student();
        $token = Password::broker('students')->createToken($student);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $student->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $student->fresh()->password));
    }

    public function test_invalid_token_is_rejected(): void
    {
        $student = $this->student();

        $this->post('/reset-password', [
            'token' => 'not-a-real-token',
            'email' => $student->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('old-password', $student->fresh()->password));
    }
}
