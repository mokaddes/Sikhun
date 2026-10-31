<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Plan;
use App\Models\Student;

use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreePlanAndOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function student(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'name' => 'M3 Student',
            'email' => uniqid('m3-').'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'trial_ends_at' => now()->subDay(),
            'referral_code' => strtoupper(uniqid('M3')),
        ], $attributes));
    }

    private function plan(array $attributes = []): Plan
    {
        $data = array_merge([
            'name' => 'Standard',
            'slug' => uniqid('plan-'),
            'price_monthly' => 149,
            'ai_chat_minutes' => 150,
            'ai_exam_count' => 30,
            'trial_ai_minutes' => 10,
            'features' => [],
            'is_active' => true,
            'is_free' => false,
        ], $attributes);

        return Plan::updateOrCreate(['slug' => $data['slug']], $data);
    }

    public function test_plan_seeder_includes_the_free_plan_and_its_bilingual_features(): void
    {
        app(PlanSeeder::class)->run();

        $free = Plan::where('slug', 'free')->firstOrFail();

        $this->assertTrue($free->is_free);
        $this->assertSame(0.0, (float) $free->price_monthly);
        $this->assertSame(20, $free->ai_chat_minutes);
        $this->assertSame(2, $free->ai_exam_count);
        $this->assertNotEmpty($free->features);
    }

    public function test_free_plan_cannot_be_purchased_through_the_paid_subscription_endpoint(): void
    {
        $free = Plan::where('slug', 'free')->firstOrFail();
        $student = $this->student(['trial_ends_at' => now()->addDays(7)]);

        $this->actingAs($student, 'web')
            ->post(route('subscription.purchase'), [
                'plan_id' => $free->id,
                'months' => 1,
                'payment_method' => 'wallet',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('student_subscriptions', 0);
    }

    public function test_expired_trial_is_moved_to_free_without_deleting_student_or_subscription_history(): void
    {
        $free = $this->plan([
            'name' => 'Free',
            'slug' => 'free',
            'price_monthly' => 0,
            'ai_chat_minutes' => 20,
            'ai_exam_count' => 2,
            'is_free' => true,
        ]);
        $paid = $this->plan();
        $student = $this->student(['wallet_balance' => 32.50]);
        $oldSubscription = $student->subscriptions()->create([
            'plan_id' => $paid->id,
            'status' => 'active',
            'ai_chat_minutes_remaining' => 3,
            'ai_exam_count_remaining' => 1,
            'started_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('subscriptions:expire-lapsed')->assertExitCode(0);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'wallet_balance' => '32.50']);
        $this->assertDatabaseHas('student_subscriptions', ['id' => $oldSubscription->id, 'status' => 'expired']);
        $this->assertDatabaseCount('student_subscriptions', 2);
        $this->assertTrue($student->fresh()->activeSubscription->plan->is_free);
        $this->assertSame($free->id, $student->fresh()->activeSubscription->plan_id);
        $this->assertSame(20, $student->fresh()->activeSubscription->ai_chat_minutes_remaining);

        $this->artisan('subscriptions:expire-lapsed')->assertExitCode(0);
        $this->assertDatabaseCount('student_subscriptions', 2);
    }

    public function test_free_plan_limits_chat_access_and_renews_on_the_next_monthly_period(): void
    {
        $free = $this->plan([
            'name' => 'Free',
            'slug' => 'free',
            'price_monthly' => 0,
            'ai_chat_minutes' => 20,
            'ai_exam_count' => 2,
            'is_free' => true,
        ]);
        $activeStudent = $this->student([
            'email' => uniqid('active-free-').'@sikhun.test',
            'referral_code' => strtoupper(uniqid('AFREE')),
        ]);
        $activeStudent->subscriptions()->create([
            'plan_id' => $free->id,
            'status' => 'active',
            'ai_chat_minutes_remaining' => 0,
            'ai_exam_count_remaining' => 1,
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
        $this->assertFalse($activeStudent->fresh()->hasActiveAiChatAccess());
        $this->assertTrue($activeStudent->fresh()->hasActiveAiExamAccess());

        $student = $this->student();
        $student->subscriptions()->create([
            'plan_id' => $free->id,
            'status' => 'active',
            'ai_chat_minutes_remaining' => 0,
            'ai_exam_count_remaining' => 1,
            'started_at' => now()->subMonth(),
            'expires_at' => now()->subMinute(),
        ]);

        $this->assertFalse($student->fresh()->hasActiveAiChatAccess());

        $this->artisan('subscriptions:expire-lapsed')->assertExitCode(0);

        $this->assertSame(20, $student->fresh()->activeSubscription->ai_chat_minutes_remaining);
        $this->assertTrue($student->fresh()->activeSubscription->expires_at->isFuture());
    }

    public function test_onboarding_steps_are_saved_skippable_and_only_allow_free_published_books(): void
    {
        $student = $this->student(['trial_ends_at' => now()->addDays(7)]);
        $book = Book::create([
            'title' => 'Free Physics',
            'slug' => 'free-physics-m3',
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
        ]);
        $paidBook = Book::create([
            'title' => 'Paid Chemistry',
            'slug' => 'paid-chemistry-m3',
            'price' => 100,
            'is_free' => false,
            'is_published' => true,
        ]);

        $this->actingAs($student, 'web')
            ->get(route('onboarding.show'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Student/Onboarding/Index'));

        $this->post(route('onboarding.step'), ['step' => 1, 'goal' => 'hsc'])->assertRedirect(route('onboarding.show'));
        $this->post(route('onboarding.step'), ['step' => 3, 'book_id' => $paidBook->id])->assertStatus(422);
        $this->post(route('onboarding.step'), ['step' => 2, 'class_level' => 'HSC 1st year', 'subject' => 'Physics'])
            ->assertRedirect(route('onboarding.show'));
        $this->post(route('onboarding.step'), ['step' => 3, 'book_id' => $book->id])->assertRedirect(route('onboarding.show'));

        $saved = $student->fresh();
        $this->assertSame('hsc', $saved->onboarding_progress['goal']);
        $this->assertSame('HSC 1st year', $saved->onboarding_progress['class_level']);
        $this->assertSame('Physics', $saved->onboarding_progress['subject']);
        $this->assertSame($book->id, $saved->onboarding_progress['book_id']);

        $this->post(route('onboarding.step'), ['step' => 4, 'skip' => true])->assertRedirect(route('dashboard'));
        $this->assertNotNull($student->fresh()->onboarding_completed_at);

        $skippingStudent = $this->student([
            'email' => uniqid('skip-').'@sikhun.test',
            'referral_code' => strtoupper(uniqid('SKIP')),
            'trial_ends_at' => now()->addDays(7),
        ]);
        $this->actingAs($skippingStudent, 'web')
            ->post(route('onboarding.step'), ['step' => 1, 'skip' => true])
            ->assertRedirect(route('onboarding.show'));
        $this->assertSame(2, $skippingStudent->fresh()->onboarding_progress['current_step']);
        $this->post(route('onboarding.skip'))->assertRedirect(route('dashboard'));
        $this->assertNotNull($skippingStudent->fresh()->onboarding_completed_at);
    }

    public function test_successful_first_ai_chat_records_activation_only_within_first_24_hours(): void
    {
        $student = $this->student([
            'trial_ends_at' => now()->addDays(7),
            'onboarding_progress' => ['completed_steps' => [1, 2, 3]],
        ]);
        $student->recordSuccessfulAiChat();

        $activated = $student->fresh();
        $this->assertNotNull($activated->first_ai_chat_at);
        $this->assertNotNull($activated->activated_at);
        $this->assertTrue($activated->onboarding_progress['first_question_completed']);
        $this->assertNotNull($activated->onboarding_completed_at);

        $lateStudent = $this->student([
            'email' => uniqid('late-').'@sikhun.test',
            'referral_code' => strtoupper(uniqid('LATE')),
        ]);
        $lateStudent->forceFill(['created_at' => now()->subHours(25)])->save();
        $lateStudent->recordSuccessfulAiChat();

        $this->assertNotNull($lateStudent->fresh()->first_ai_chat_at);
        $this->assertNull($lateStudent->fresh()->activated_at);
        $this->assertNull($lateStudent->fresh()->onboarding_completed_at);
    }
}
