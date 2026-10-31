<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Verify Student',
            'email' => 'verify-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'ssc',
            'status' => 'active',
            'referral_code' => 'VRF'.strtoupper(uniqid()),
        ]);
    }

    public function test_new_students_start_unverified(): void
    {
        $this->assertFalse($this->student()->hasVerifiedEmail());
    }

    public function test_verification_notice_renders_for_unverified_student(): void
    {
        $student = $this->student();

        $this->actingAs($student, 'web')
            ->get('/email/verify')
            ->assertOk();
    }

    public function test_verification_notification_can_be_resent(): void
    {
        Notification::fake();

        $student = $this->student();

        $this->actingAs($student, 'web')
            ->post('/email/verification-notification')
            ->assertRedirect();

        Notification::assertSentTo($student, VerifyEmailNotification::class);
        $this->assertFalse($student->fresh()->hasVerifiedEmail());
    }

    public function test_signed_link_verifies_the_email(): void
    {
        $student = $this->student();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $student->id,
            'hash' => sha1($student->email),
        ]);

        $this->actingAs($student, 'web')->get($url)->assertRedirect(route('dashboard'));

        $this->assertTrue($student->fresh()->hasVerifiedEmail());
    }

    public function test_link_cannot_verify_a_different_signed_in_account(): void
    {
        $owner = $this->student();
        $other = $this->student();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $owner->id,
            'hash' => sha1($owner->email),
        ]);

        $this->actingAs($other, 'web')->get($url)->assertForbidden();

        $this->assertFalse($owner->fresh()->hasVerifiedEmail());
    }

    public function test_link_with_wrong_hash_is_rejected(): void
    {
        $student = $this->student();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $student->id,
            'hash' => sha1('someone-else@sikhun.test'),
        ]);

        $this->actingAs($student, 'web')->get($url)->assertForbidden();

        $this->assertFalse($student->fresh()->hasVerifiedEmail());
    }

    public function test_unsigned_link_is_rejected(): void
    {
        $student = $this->student();

        $this->actingAs($student, 'web')
            ->get("/email/verify/{$student->id}/".sha1($student->email))
            ->assertForbidden();
    }
}
