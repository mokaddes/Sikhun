<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AiProviderController;
use App\Models\Book;
use App\Models\Student;
use App\Services\Ai\FlashcardSourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FlashcardBookCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(): Student
    {
        return Student::create([
            'name' => 'Flashcard Student',
            'email' => 'flashcard-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'referral_code' => 'FLASH'.strtoupper(uniqid()),
        ]);
    }

    private function createBook(string $status = 'completed'): Book
    {
        return Book::create([
            'title' => 'Coverage Physics',
            'slug' => 'coverage-physics-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'processing_status' => $status,
            'chapter_purchase_enabled' => true,
        ]);
    }

    private function addChapterChunks(Book $book, string $title, int $chapterNumber, int $chunkCount = 1)
    {
        $chapter = $book->chapters()->create([
            'chapter_number' => (string) $chapterNumber,
            'title' => $title,
            'slug' => strtolower($title).'-'.uniqid(),
            'level' => 1,
            'start_page' => $chapterNumber * 10,
            'end_page' => $chapterNumber * 10 + 9,
            'sort_order' => $chapterNumber,
            'price' => 50,
        ]);

        for ($i = 0; $i < $chunkCount; $i++) {
            $book->chunks()->create([
                'chapter_id' => $chapter->id,
                'chunk_index' => ($chapterNumber * 100) + $i,
                'page_number' => $chapterNumber * 10 + $i,
                'content' => "{$title} content {$i}",
            ]);
        }

        return $chapter;
    }

    public function test_book_flashcards_sample_across_accessible_chapters_with_valid_provenance(): void
    {
        $book = $this->createBook();
        $first = $this->addChapterChunks($book, 'Mechanics', 1, 8);
        $second = $this->addChapterChunks($book, 'Thermodynamics', 2, 8);
        $student = $this->createStudent();
        $student->bookShelf()->create(['book_id' => $book->id, 'source' => 'purchased']);

        $source = app(FlashcardSourceService::class)->fromBook($book, $student);

        $this->assertSame($book->title, $source['source_label']);
        $this->assertStringContainsString('[chunk ', $source['content']);
        $this->assertStringContainsString('chapter Mechanics', $source['content']);
        $this->assertStringContainsString('chapter Thermodynamics', $source['content']);
        $this->assertCount(16, $source['references']);
        $this->assertSame($first->id, $source['references'][array_key_first($source['references'])]['chapter_id']);
        $this->assertSame(10, $source['references'][array_key_first($source['references'])]['page']);
        $this->assertSame($second->id, end($source['references'])['chapter_id']);
    }

    public function test_chapter_only_access_never_includes_unowned_book_content(): void
    {
        $book = $this->createBook();
        $owned = $this->addChapterChunks($book, 'Owned', 1);
        $private = $this->addChapterChunks($book, 'Private', 2);
        $student = $this->createStudent();
        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $owned->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $source = app(FlashcardSourceService::class)->fromBook($book, $student);

        $this->assertStringContainsString('Owned content', $source['content']);
        $this->assertStringNotContainsString('Private content', $source['content']);
        $this->assertSame([$owned->id], array_values(array_unique(array_column($source['references'], 'chapter_id'))));
        $this->assertSame($private->id, $book->chapters()->where('title', 'Private')->value('id'));
    }

    public function test_unprocessed_and_empty_books_are_rejected_with_clear_errors(): void
    {
        $student = $this->createStudent();
        $unprocessed = $this->createBook('processing');
        $unprocessed->update(['is_free' => true]);

        try {
            app(FlashcardSourceService::class)->fromBook($unprocessed, $student);
            $this->fail('Unprocessed books should be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertStringContainsString('not fully processed', $exception->getMessage());
        }

        $empty = $this->createBook();
        $empty->update(['is_free' => true]);

        try {
            app(FlashcardSourceService::class)->fromBook($empty, $student);
            $this->fail('Books without chunks should be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertStringContainsString('no searchable text chunks', $exception->getMessage());
        }
    }

    public function test_chapter_selection_cannot_expand_access(): void
    {
        $book = $this->createBook();
        $owned = $this->addChapterChunks($book, 'Owned', 1);
        $private = $this->addChapterChunks($book, 'Private', 2);
        $student = $this->createStudent();
        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $owned->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $this->expectException(HttpException::class);
        app(FlashcardSourceService::class)->fromBook($book, $student, [$private->id]);
    }

    public function test_ai_provider_page_exposes_recommendations_for_each_planned_use_case(): void
    {
        $response = app(AiProviderController::class)->index();
        $props = (new \ReflectionProperty($response, 'props'))->getValue($response);

        $this->assertSame(
            ['book_chat', 'exam_gen', 'flashcard_gen', 'essay_grade', 'schedule_gen', 'support_bot', 'book_ocr', 'blog_rewrite'],
            array_keys($props['recommendations']),
        );
        $this->assertContains('Google — Gemini Flash', $props['recommendations']['flashcard_gen']['models']);
    }
}
