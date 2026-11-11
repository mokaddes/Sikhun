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

    public function test_student_can_view_question_bank_index(): void
    {
        $student = $this->createStudent(['type' => 'job']);

        $subject = QuestionSubject::create([
            'name_bn' => 'বাংলা',
            'name_en' => 'Bangla',
            'slug' => 'bangla',
            'segment' => 'both',
            'is_active' => true,
        ]);

        $question = QuestionBank::create([
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
        $subject = QuestionSubject::create([
            'name_bn' => 'বিজ্ঞান',
            'name_en' => 'Science',
            'slug' => 'science',
            'segment' => 'both',
        ]);

        $question = QuestionBank::create([
            'segment' => 'job',
            'subject_id' => $subject->id,
            'origin_tag' => 'TEST-01',
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
        $subject = QuestionSubject::create([
            'name_bn' => 'পদার্থবিজ্ঞান',
            'name_en' => 'Physics',
            'slug' => 'physics',
            'segment' => 'both',
        ]);

        $question = QuestionBank::create([
            'segment' => 'job',
            'subject_id' => $subject->id,
            'origin_tag' => 'TEST-02',
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

        $subject = QuestionSubject::create([
            'name_bn' => 'সাধারণ জ্ঞান',
            'name_en' => 'General Knowledge',
            'slug' => 'gk',
            'segment' => 'both',
            'is_active' => true,
        ]);

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
    }
}
