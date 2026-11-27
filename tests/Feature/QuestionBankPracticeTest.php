<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\Plan;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class QuestionBankPracticeTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Test Student',
            'email' => 'student-' . uniqid() . '@sikhun.test',
            'password' => bcrypt('password'),
            'type' => 'job',
            'is_active' => true,
            'referral_code' => 'REF' . strtoupper(uniqid()),
        ], $attributes));
    }

    private function createSubject(array $attributes = []): QuestionSubject
    {
        return QuestionSubject::create(array_merge([
            'name_bn' => 'বাংলা',
            'name_en' => 'Bangla',
            'slug' => 'bangla-' . uniqid(),
            'segment' => 'both',
            'is_active' => true,
        ], $attributes));
    }

    public function test_student_can_view_question_bank_index(): void
    {
        $student = $this->createStudent(['type' => 'job']);
        $subject = $this->createSubject();

        QuestionBank::create([
            'segment' => 'job',
            'allowed_student_types' => ['all'],
            'subject_id' => $subject->id,
            'exam_name' => '45th BCS Preliminary',
            'origin_tag' => 'BCS-45',
            'year' => 2023,
            'question_text' => 'বাংলা সাহিত্যের প্রাচীনতম নিদর্শন কোনটি?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'চর্যাপদ'],
                ['key' => 'B', 'text' => 'শ্রীকৃষ্ণকীর্তন'],
                ['key' => 'C', 'text' => 'পদ্মাবতী'],
                ['key' => 'D', 'text' => 'মেঘনাদবধ'],
            ],
            'correct_option' => 'A',
            'explanation' => 'হরপ্রসাদ শাস্ত্রী ১৯০৭ সালে চর্যাপদ আবিষ্কার করেন।',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'web')
            ->get(route('student.question-bank.index', ['mode' => 'exam', 'origin_tag' => 'BCS-45']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Student/QuestionBank/Index')
            ->has('questions.data', 1)
            ->has('subjects')
            ->has('exams')
            ->where('isPaid', false)
            ->where('studentType', 'job')
            ->where('filters.origin_tag', 'BCS-45')
        );
    }

    public function test_ai_explain_returns_existing_explanation_if_present(): void
    {
        $student = $this->createStudent();
        $subject = $this->createSubject();

        $question = QuestionBank::create([
            'segment' => 'job',
            'origin_tag' => 'BCS-45',
            'subject_id' => $subject->id,
            'question_text' => 'What is photosynthesis?',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Process']],
            'correct_option' => 'A',
            'explanation' => 'Existing explanation here.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.ai-explain', $question->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'explanation' => 'Existing explanation here.',
            ]);
    }

    public function test_ai_explain_generates_and_persists_explanation_when_absent(): void
    {
        $student = $this->createStudent();
        $subject = $this->createSubject();

        $question = QuestionBank::create([
            'segment' => 'job',
            'origin_tag' => 'BCS-45',
            'subject_id' => $subject->id,
            'question_text' => 'What is Newton second law?',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'F=ma']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $mockAiService = Mockery::mock(QuestionAiStructuringService::class);
        $mockAiService->shouldReceive('generateAiExplanation')
            ->once()
            ->andReturn('F=ma is the formula.');
        $this->app->instance(QuestionAiStructuringService::class, $mockAiService);

        $response = $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.ai-explain', $question->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'explanation' => 'F=ma is the formula.',
            ]);

        $question->refresh();
        $this->assertEquals('F=ma is the formula.', $question->meta['ai_explanation'] ?? null);
    }

    public function test_generate_test_blocks_unpaid_students(): void
    {
        $student = $this->createStudent();

        $response = $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.generate-test', [
                'question_count' => 10,
            ]));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'requires_upgrade' => true,
            ]);
    }

    public function test_generate_test_creates_exam_session_for_paid_student(): void
    {
        $student = $this->createStudent(['type' => 'job']);

        $plan = Plan::create([
            'name' => 'Job Premium',
            'slug' => 'job-premium',
            'price_monthly' => 500,
            'is_active' => true,
        ]);

        StudentSubscription::create([
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $subject = $this->createSubject(['name_bn' => 'সাধারণ জ্ঞান', 'name_en' => 'General Knowledge']);

        QuestionBank::create([
            'segment' => 'job',
            'allowed_student_types' => ['all'],
            'subject_id' => $subject->id,
            'origin_tag' => 'BCS-45',
            'question_text' => 'Sample Question 1',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'Option A'],
                ['key' => 'B', 'text' => 'Option B'],
            ],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.generate-test', [
                'subject_id' => $subject->id,
                'question_count' => 5,
                'duration_minutes' => 15,
            ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'exam_id',
                'redirect_url',
            ]);

        $examId = $response->json('exam_id');
        $session = ExamSession::find($examId);
        $this->assertNotNull($session);
        $this->assertEquals($student->id, $session->student_id);
        $this->assertEquals('exam', $session->mode);
        $this->assertEquals('in_progress', $session->status);
        $this->assertCount(1, $session->questions);
        $this->assertEquals(1, $session->total);
        $this->assertStringContainsString('/exams/' . $session->id, $response->json('redirect_url'));
    }

    public function test_hsc_student_sees_hsc_questions_and_shared_admission_segment_subjects(): void
    {
        $student = $this->createStudent(['type' => 'hsc']);
        $physics = $this->createSubject([
            'name_bn' => 'পদার্থবিজ্ঞান',
            'name_en' => 'Physics',
            'slug' => 'physics-test-' . uniqid(),
            'segment' => 'admission',
        ]);

        QuestionBank::create([
            'segment' => 'hsc',
            'allowed_student_types' => ['all'],
            'subject_id' => $physics->id,
            'exam_name' => 'HSC Physics 1st Paper Board Test',
            'origin_tag' => 'HSC-PHY-TEST',
            'year' => 2024,
            'question_text' => 'ভেক্টর রাশি কোনটি?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'বেগ'],
                ['key' => 'B', 'text' => 'দ্রুতি'],
                ['key' => 'C', 'text' => 'কাজ'],
                ['key' => 'D', 'text' => 'শক্তি'],
            ],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'web')
            ->get(route('student.question-bank.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Student/QuestionBank/Index')
            ->where('isUnavailable', false)
            ->where('lockedToGoal', false)
            ->where('activeSector', 'hsc')
            ->has('questions.data', 1)
            ->has('subjects', 1)
            ->where('subjects.0.id', $physics->id)
        );
    }

    public function test_ssc_and_university_students_can_browse_and_switch_sectors(): void
    {
        $subject = $this->createSubject(['segment' => 'admission']);

        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $subject->id,
            'origin_tag' => 'HSC-SAMPLE',
            'question_text' => 'HSC Sample MCQ',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt A'], ['key' => 'B', 'text' => 'Opt B']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        QuestionBank::create([
            'segment' => 'admission',
            'subject_id' => $subject->id,
            'origin_tag' => 'DU-A',
            'question_text' => 'Admission Sample MCQ',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt A'], ['key' => 'B', 'text' => 'Opt B']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        foreach (['ssc', 'university'] as $type) {
            $student = $this->createStudent(['type' => $type]);

            $this->actingAs($student, 'web')
                ->get(route('student.question-bank.index'))
                ->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->where('isUnavailable', false)
                    ->where('lockedToGoal', false)
                );

            $this->actingAs($student, 'web')
                ->get(route('student.question-bank.index', ['sector' => 'hsc']))
                ->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->where('activeSector', 'hsc')
                    ->has('questions.data', 1)
                );

            $this->actingAs($student, 'web')
                ->get(route('student.question-bank.index', ['sector' => 'admission']))
                ->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->where('activeSector', 'admission')
                    ->has('questions.data', 1)
                );
        }
    }

    public function test_hsc_student_can_ai_explain_any_question_and_generate_hsc_mock_test(): void
    {
        $student = $this->createStudent(['type' => 'hsc']);

        $plan = Plan::create([
            'name' => 'HSC Pro',
            'slug' => 'hsc-pro-' . uniqid(),
            'price_monthly' => 300,
            'is_active' => true,
        ]);

        StudentSubscription::create([
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $subject = $this->createSubject(['segment' => 'admission']);

        $admissionQuestion = QuestionBank::create([
            'segment' => 'admission',
            'subject_id' => $subject->id,
            'origin_tag' => 'DU-A',
            'question_text' => 'Admission Q',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt A']],
            'correct_option' => 'A',
            'explanation' => 'Admission explanation.',
            'status' => 'active',
        ]);

        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $subject->id,
            'origin_tag' => 'HSC-TEST',
            'question_text' => 'HSC Q',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt A'], ['key' => 'B', 'text' => 'Opt B']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.ai-explain', $admissionQuestion->id))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'explanation' => 'Admission explanation.',
            ]);

        $testRes = $this->actingAs($student, 'web')
            ->postJson(route('student.question-bank.generate-test', [
                'sector' => 'hsc',
                'subject_id' => $subject->id,
                'question_count' => 5,
                'duration_minutes' => 10,
            ]));

        $testRes->assertStatus(200)
            ->assertJsonStructure(['success', 'exam_id', 'redirect_url']);

        $session = ExamSession::find($testRes->json('exam_id'));
        $this->assertNotNull($session);
        $this->assertCount(1, $session->questions);
    }
}
