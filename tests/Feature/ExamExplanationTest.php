<?php

namespace Tests\Feature;

use App\Http\Controllers\Student\ExamController;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookPage;
use App\Models\ExamSession;
use App\Models\Plan;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExamExplanationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/_test/exams/{exam}/questions/{question}/explanation', [ExamController::class, 'explanation']);
    }

    private function studentWithPlan(bool $free): Student
    {
        $plan = Plan::create([
            'name' => $free ? 'Free' : 'Standard',
            'slug' => uniqid('explain-plan-'),
            'price_monthly' => $free ? 0 : 149,
            'ai_chat_minutes' => 20,
            'ai_exam_count' => 2,
            'trial_ai_minutes' => 0,
            'features' => [],
            'is_active' => true,
            'is_free' => $free,
        ]);

        $student = Student::create([
            'name' => 'Explanation Student',
            'email' => uniqid('explain-').'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'trial_ends_at' => now()->subDay(),
            'referral_code' => strtoupper(uniqid('EXP')),
        ]);

        $student->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'ai_chat_minutes_remaining' => 10,
            'ai_exam_count_remaining' => 1,
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        return $student;
    }

    private function completedExam(Student $student, array $overrides = []): ExamSession
    {
        return $student->examSessions()->create(array_merge([
            'source_type' => 'topic',
            'source_text' => 'Newton’s first law describes inertia.',
            'config' => ['type' => 'mcq', 'count' => 1],
            'questions' => [[
                'id' => 1,
                'question' => 'What does the first law describe?',
                'correct_answer' => 'Inertia',
                'explanation' => 'Objects resist changes in motion.',
            ]],
            'answers' => [1 => 'Gravity'],
            'score' => 0,
            'total' => 1,
            'percentage' => 0,
            'mode' => 'practice',
            'status' => 'completed',
            'completed_at' => now(),
        ], $overrides));
    }

    public function test_free_plan_is_denied_ai_explanations(): void
    {
        $student = $this->studentWithPlan(free: true);
        $exam = $this->completedExam($student);

        $this->actingAs($student, 'web')
            ->post('/_test/exams/'.$exam->id.'/questions/1/explanation')
            ->assertForbidden();
    }

    public function test_only_the_exam_owner_can_request_an_explanation(): void
    {
        $owner = $this->studentWithPlan(free: false);
        $otherStudent = $this->studentWithPlan(free: false);
        $exam = $this->completedExam($owner);

        $this->actingAs($otherStudent, 'web')
            ->post('/_test/exams/'.$exam->id.'/questions/1/explanation')
            ->assertForbidden();
    }

    public function test_readable_book_citation_links_to_its_page_in_the_reader(): void
    {
        $student = $this->studentWithPlan(free: true);
        $book = Book::create([
            'title' => 'Open Physics',
            'slug' => 'open-physics-'.uniqid(),
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
        ]);
        $chapter = BookChapter::create([
            'book_id' => $book->id,
            'title' => 'Motion',
            'slug' => 'motion',
            'start_page' => 7,
            'end_page' => 12,
        ]);
        BookPage::create([
            'book_id' => $book->id,
            'chapter_id' => $chapter->id,
            'page_number' => 7,
            'content' => 'Open content about inertia.',
        ]);
        $chunk = $book->chunks()->create([
            'chapter_id' => $chapter->id,
            'page_number' => 7,
            'chunk_index' => 0,
            'content' => 'Open content about inertia.',
        ]);
        $exam = $this->completedExam($student, [
            'source_type' => 'book',
            'source_book_id' => $book->id,
            'source_text' => null,
            'questions' => [[
                'id' => 1,
                'question' => 'What does the law describe?',
                'correct_answer' => 'Inertia',
                'book_ref' => ['chunk_id' => $chunk->id],
            ]],
        ]);

        $this->actingAs($student, 'web')
            ->get(route('exams.result', $exam))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Student/Exams/Result')
                ->where('exam.questions.0.book_ref.chapter', 'Motion')
                ->where('exam.questions.0.book_ref.page', 7)
                ->where('exam.questions.0.book_ref.reader_url', route('reader.show', ['book' => $book->id, 'page' => 7]))
                ->where('canExplain', false));
    }

    public function test_book_citation_content_requires_current_page_access(): void
    {
        $student = $this->studentWithPlan(free: false);
        $book = Book::create([
            'title' => 'Restricted Physics',
            'slug' => 'restricted-physics-'.uniqid(),
            'price' => 100,
            'is_free' => false,
            'is_published' => true,
        ]);
        $chapter = BookChapter::create([
            'book_id' => $book->id,
            'title' => 'Motion',
            'slug' => 'motion',
            'start_page' => 7,
            'end_page' => 12,
        ]);
        BookPage::create([
            'book_id' => $book->id,
            'chapter_id' => $chapter->id,
            'page_number' => 7,
            'content' => 'A restricted passage about inertia.',
        ]);
        $book->chunks()->create([
            'chapter_id' => $chapter->id,
            'page_number' => 7,
            'chunk_index' => 0,
            'content' => 'A restricted passage about inertia.',
        ]);
        $exam = $this->completedExam($student, [
            'source_type' => 'book',
            'source_book_id' => $book->id,
            'source_text' => null,
            'questions' => [[
                'id' => 1,
                'question' => 'What does the law describe?',
                'correct_answer' => 'Inertia',
                'book_ref' => ['chunk_id' => $book->chunks()->value('id')],
            ]],
        ]);

        $this->actingAs($student, 'web')
            ->post('/_test/exams/'.$exam->id.'/questions/1/explanation')
            ->assertForbidden();
    }
}
