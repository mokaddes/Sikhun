<?php

namespace Tests\Feature;

use App\Jobs\GenerateExamQuestions;
use App\Models\Book;
use App\Models\ExamSession;
use App\Models\Student;
use App\Services\Ai\BookChunkRetrievalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ExamBookCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function createExamSession(string $processingStatus, bool $withChunk): ExamSession
    {
        $student = Student::create([
            'name' => 'Exam Student',
            'email' => 'exam-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'referral_code' => 'EXAM'.strtoupper(uniqid()),
        ]);
        $book = Book::create([
            'title' => 'Test Book',
            'slug' => 'test-book-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'processing_status' => $processingStatus,
        ]);

        if ($withChunk) {
            $book->chunks()->create(['chunk_index' => 0, 'content' => 'sample text']);
        }

        return $student->examSessions()->create([
            'source_type' => 'book',
            'source_book_id' => $book->id,
            'config' => ['type' => 'mcq', 'count' => 5, 'chapter_ids' => []],
            'mode' => 'practice',
            'status' => 'generating',
            'total' => 5,
        ]);
    }

    public function test_generation_fails_clearly_when_book_is_not_processed(): void
    {
        $session = $this->createExamSession('processing', true);
        $retrieval = Mockery::mock(BookChunkRetrievalService::class);
        $retrieval->shouldNotReceive('coverageChunks');

        (new GenerateExamQuestions($session->id))->handle($retrieval);

        $this->assertSame('failed', $session->fresh()->status);
    }

    public function test_generation_fails_when_completed_book_has_no_chunks(): void
    {
        $session = $this->createExamSession('completed', false);
        $retrieval = Mockery::mock(BookChunkRetrievalService::class);
        $retrieval->shouldNotReceive('coverageChunks');

        (new GenerateExamQuestions($session->id))->handle($retrieval);

        $this->assertSame('failed', $session->fresh()->status);
    }
}
