<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\Ai\BookChunkRetrievalService;
use App\Services\BookAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

/**
 * Chapter-access and RAG-security tests (spec §38). The critical invariant:
 * a student who owns ONLY Chapter 1 must never retrieve Chapter 2's
 * content — through keyword search, semantic search, or citations.
 */
class ChapterAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeBookWithChapters(array $chapterTexts): Book
    {
        $book = Book::create([
            'title' => 'Physics',
            'slug' => 'physics-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'chapter_purchase_enabled' => true,
        ]);

        foreach ($chapterTexts as $i => $text) {
            $chapter = $book->chapters()->create([
                'chapter_number' => (string) ($i + 1),
                'title' => 'Chapter '.($i + 1),
                'slug' => 'ch-'.($i + 1).'-'.uniqid(),
                'level' => 1,
                'start_page' => $i * 10 + 1,
                'end_page' => $i * 10 + 10,
                'sort_order' => $i,
                'price' => 50,
            ]);

            $page = $book->pages()->create([
                'page_number' => $i * 10 + 1,
                'chapter_id' => $chapter->id,
                'content' => $text,
            ]);

            $book->chunks()->create([
                'chapter_id' => $chapter->id,
                'page_id' => $page->id,
                'chunk_index' => $i,
                'page_number' => $page->page_number,
                'content' => $text,
            ]);
        }

        return $book->refresh();
    }

    private function student(): Student
    {
        return Student::create([
            'name' => 'Test Student',
            'email' => 'test-'.uniqid().'@sikhun.test',
            'password' => 'password',
        ]);
    }

    private function retrievalWithMockedEmbeddings(): BookChunkRetrievalService
    {
        // Retrieval must degrade gracefully with no embedding provider.
        $embeddings = Mockery::mock('App\Services\Ai\EmbeddingService');
        $embeddings->shouldReceive('embedQuery')->andThrow(new \RuntimeException('no provider'));
        $embeddings->shouldReceive('embedBookChunks')->andReturn(0);

        return new BookChunkRetrievalService(
            new BookAccessService(app(AccessGrantService::class)),
            $embeddings,
        );
    }

    public function test_full_book_owner_accesses_all_chapters(): void
    {
        $book = $this->makeBookWithChapters(['gravity text', 'thermodynamics text']);
        $student = $this->student();

        $student->bookShelf()->create(['book_id' => $book->id, 'source' => 'purchased']);

        $access = app(BookAccessService::class);

        $this->assertTrue($access->hasAccess($student, $book));
        $this->assertTrue($access->canAccessChapter($student, $book->chapters[0]));
        $this->assertTrue($access->canAccessChapter($student, $book->chapters[1]));
    }

    public function test_chapter_owner_accesses_only_purchased_chapter(): void
    {
        $book = $this->makeBookWithChapters(['gravity text', 'thermodynamics text']);
        $student = $this->student();

        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $book->chapters[0]->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $access = app(BookAccessService::class);

        $this->assertFalse($access->hasAccess($student, $book));
        $this->assertTrue($access->canAccessChapter($student, $book->chapters[0]));
        $this->assertFalse($access->canAccessChapter($student, $book->chapters[1]));
    }

    public function test_non_owner_denied(): void
    {
        $book = $this->makeBookWithChapters(['gravity text', 'thermodynamics text']);
        $student = $this->student();

        $access = app(BookAccessService::class);

        $this->assertFalse($access->hasAccess($student, $book));
        $this->assertFalse($access->canAccessChapter($student, $book->chapters[0]));
    }

    public function test_guest_denied_on_paid_chapters(): void
    {
        $book = $this->makeBookWithChapters(['gravity text']);

        $access = app(BookAccessService::class);

        $this->assertFalse($access->canAccessChapter(null, $book->chapters[0]));
    }

    public function test_free_book_allows_all_chapters(): void
    {
        $book = $this->makeBookWithChapters(['gravity text']);
        $book->update(['is_free' => true]);
        $student = $this->student();

        $access = app(BookAccessService::class);

        $this->assertTrue($access->canAccessChapter($student, $book->chapters[0]));
    }

    /**
     * THE critical RAG security test: owns Chapter 1, asks about Chapter 2
     * content → Chapter 2 content must NOT be retrieved.
     */
    public function test_rag_never_retrieves_unowned_chapter_content(): void
    {
        $book = $this->makeBookWithChapters([
            'Newton s laws of motion describe the relationship between force and mass',
            'Entropy always increases in an isolated thermodynamic system',
        ]);
        $student = $this->student();

        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $book->chapters[0]->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $retrieval = $this->retrievalWithMockedEmbeddings();

        // Ask directly about Chapter 2's topic.
        $chunks = $retrieval->relevantChunks($book, 'entropy thermodynamic system', $student);

        foreach ($chunks as $chunk) {
            $this->assertStringNotContainsString(
                'Entropy',
                $chunk['content'],
                'Chapter 2 content leaked into RAG results for a Chapter-1-only owner.'
            );
        }

        // And the accessible-chapter scoping returns only chapter 1.
        $accessible = app(BookAccessService::class)->accessibleChapterIds($student, $book);
        $this->assertEquals([$book->chapters[0]->id], $accessible);
    }

    public function test_rag_retrieves_owned_chapter_content(): void
    {
        $book = $this->makeBookWithChapters([
            'Newton s laws of motion describe the relationship between force and mass',
            'Entropy always increases in an isolated thermodynamic system',
        ]);
        $student = $this->student();

        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $book->chapters[0]->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $retrieval = $this->retrievalWithMockedEmbeddings();

        $chunks = $retrieval->relevantChunks($book, 'Newton laws force', $student);

        $this->assertNotEmpty($chunks, 'Owned chapter content should be retrievable.');
        $this->assertStringContainsString('Newton', $chunks[0]['content']);
    }

    public function test_rag_returns_nothing_for_non_owner(): void
    {
        $book = $this->makeBookWithChapters(['gravity text', 'thermodynamics text']);
        $student = $this->student(); // owns nothing

        $retrieval = $this->retrievalWithMockedEmbeddings();

        $this->assertSame([], $retrieval->relevantChunks($book, 'gravity', $student));
    }

    public function test_duplicate_chapter_ownership_impossible(): void
    {
        $book = $this->makeBookWithChapters(['gravity text']);
        $student = $this->student();
        $chapter = $book->chapters[0];

        $student->ownedChapters()->create([
            'book_id' => $book->id, 'chapter_id' => $chapter->id,
            'source' => 'purchased', 'price' => 50, 'purchased_at' => now(),
        ]);

        // Simulate a concurrent second insert hitting the unique constraint.
        $this->expectException(\Illuminate\Database\QueryException::class);

        $student->ownedChapters()->create([
            'book_id' => $book->id, 'chapter_id' => $chapter->id,
            'source' => 'purchased', 'price' => 50, 'purchased_at' => now(),
        ]);
    }

    public function test_owning_chapter_grants_descendant_sections(): void
    {
        $book = $this->makeBookWithChapters(['gravity text']);

        $section = $book->chapters()->create([
            'parent_id' => $book->chapters[0]->id,
            'chapter_number' => '1.1',
            'title' => 'Section 1.1',
            'slug' => 'sec-11-'.uniqid(),
            'level' => 2,
            'start_page' => 2,
            'end_page' => 5,
            'sort_order' => 1,
        ]);

        $student = $this->student();

        $student->ownedChapters()->create([
            'book_id' => $book->id, 'chapter_id' => $book->chapters[0]->id,
            'source' => 'purchased', 'price' => 50, 'purchased_at' => now(),
        ]);

        $access = app(BookAccessService::class);

        $this->assertTrue($access->canAccessChapter($student, $section));
    }
}
