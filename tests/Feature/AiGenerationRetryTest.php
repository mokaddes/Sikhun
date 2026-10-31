<?php

namespace Tests\Feature;

use App\Jobs\GenerateExamQuestions;
use App\Jobs\GenerateFlashcards;
use App\Jobs\GenerateStudySchedule;
use App\Jobs\GradeEssay;
use App\Models\EssaySubmission;
use App\Models\ExamSession;
use App\Models\FlashcardSet;
use App\Models\Student;
use App\Models\StudySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AiGenerationRetryTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Retry Student',
            'email' => 'retry-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'RT'.strtoupper(uniqid()),
        ]);
    }

    public function test_owner_can_retry_failed_exam_generation(): void
    {
        Queue::fake();
        $student = $this->student();
        $exam = ExamSession::create([
            'student_id' => $student->id,
            'source_type' => 'topic',
            'source_text' => 'Newton laws of motion',
            'config' => ['type' => 'mcq', 'count' => 5, 'duration' => 0, 'mode' => 'practice'],
            'mode' => 'practice',
            'status' => 'failed',
            'questions' => [['id' => 1, 'question' => 'old partial result']],
            'answers' => ['1' => 'A'],
            'score' => 1,
            'total' => 5,
        ]);

        $this->actingAs($student, 'web')
            ->post("/exams/{$exam->id}/retry")
            ->assertRedirect(route('exams.show', $exam));

        $this->assertDatabaseHas('exam_sessions', [
            'id' => $exam->id,
            'status' => 'generating',
            'questions' => null,
            'answers' => null,
            'total' => 5,
        ]);
        Queue::assertPushed(GenerateExamQuestions::class);
    }

    public function test_owner_can_retry_failed_flashcard_generation_from_saved_source(): void
    {
        Queue::fake();
        $student = $this->student();
        $set = FlashcardSet::create([
            'student_id' => $student->id,
            'title' => 'Retry my flashcards',
            'generation_status' => 'failed',
            'generation_data' => [
                'source_type' => 'text',
                'source_text' => 'A force changes the motion of an object.',
                'count' => 5,
            ],
        ]);

        $this->actingAs($student, 'web')
            ->post("/flashcards/{$set->id}/retry")
            ->assertRedirect(route('flashcards.show', $set));

        $this->assertSame('generating', $set->fresh()->generation_status);
        $this->assertSame(1, $set->fresh()->generation_attempts);
        Queue::assertPushed(GenerateFlashcards::class);
    }

    public function test_owner_can_retry_failed_study_schedule_generation(): void
    {
        Queue::fake();
        $student = $this->student();
        $schedule = StudySchedule::create([
            'student_id' => $student->id,
            'exam_date' => today()->addMonth(),
            'config' => [
                'subjects' => ['Physics'],
                'weak_subjects' => [],
                'hours_per_day' => 3,
                'style' => 'balanced',
                'include_weekends' => true,
            ],
            'schedule_data' => null,
            'status' => 'failed',
        ]);

        $this->actingAs($student, 'web')
            ->post("/schedules/{$schedule->id}/retry")
            ->assertRedirect(route('schedules.show', $schedule));

        $this->assertSame('generating', $schedule->fresh()->status);
        Queue::assertPushed(GenerateStudySchedule::class);
    }

    public function test_owner_can_retry_failed_essay_grading(): void
    {
        Queue::fake();
        $student = $this->student();
        $essay = EssaySubmission::create([
            'student_id' => $student->id,
            'grading_type' => 'general',
            'essay_text' => str_repeat('Education helps people build a better future. ', 4),
            'status' => 'failed',
            'result' => null,
        ]);

        $this->actingAs($student, 'web')
            ->post("/essays/{$essay->id}/retry")
            ->assertRedirect(route('essays.show', $essay));

        $this->assertSame('grading', $essay->fresh()->status);
        Queue::assertPushed(GradeEssay::class);
    }

    public function test_student_cannot_retry_another_students_exam(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $exam = ExamSession::create([
            'student_id' => $owner->id,
            'source_type' => 'topic',
            'source_text' => 'A saved source topic',
            'config' => ['type' => 'mcq', 'count' => 5, 'duration' => 0, 'mode' => 'practice'],
            'mode' => 'practice',
            'status' => 'failed',
            'total' => 5,
        ]);

        $this->actingAs($other, 'web')
            ->post("/exams/{$exam->id}/retry")
            ->assertForbidden();
    }
}
