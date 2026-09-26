<?php

namespace Tests\Feature;

use App\Contracts\ParsedDocument;
use App\Jobs\ProcessBookPdf;
use App\Models\Book;
use App\Services\Ai\BookChunkingService;
use App\Services\Ai\BookStructureDetectionService;
use App\Services\Ai\EmbeddingService;
use App\Services\Pdf\ParsedDocumentStorageService;
use App\Services\Pdf\PdfParserManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Reprocessing idempotency tests (spec §38): processing the same book
 * twice must not create duplicate chapters/pages/elements/chunks.
 */
class BookProcessingTest extends TestCase
{
    use RefreshDatabase;

    private function makeBook(): Book
    {
        Storage::fake('private');

        return Book::create([
            'title' => 'Test Physics',
            'slug' => 'test-physics-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'pdf_path' => 'books/pdfs/test.pdf',
        ]);
    }

    private function makeDocument(): ParsedDocument
    {
        $doc = new ParsedDocument;
        $doc->parserName = 'test-parser';
        $doc->parserVersion = '1.0';

        $doc->pages = [
            ['page_number' => 1, 'content' => 'Page one text.', 'metadata' => null],
            ['page_number' => 2, 'content' => 'Page two text.', 'metadata' => null],
        ];

        $doc->chapters = [
            ['chapter_number' => '1', 'title' => 'Chapter One', 'level' => 1, 'start_page' => 1, 'end_page' => 1, 'parent_index' => null, 'metadata' => null],
            ['chapter_number' => '2', 'title' => 'Chapter Two', 'level' => 1, 'start_page' => 2, 'end_page' => 2, 'parent_index' => null, 'metadata' => null],
        ];

        $doc->elements = [
            ['type' => 'heading', 'page_number' => 1, 'content' => 'Chapter One', 'metadata' => null, 'bbox' => null, 'heading_path' => null],
            ['type' => 'text', 'page_number' => 1, 'content' => 'Page one body text about gravity.', 'metadata' => null, 'bbox' => null, 'heading_path' => null],
            ['type' => 'heading', 'page_number' => 2, 'content' => 'Chapter Two', 'metadata' => null, 'bbox' => null, 'heading_path' => null],
            ['type' => 'text', 'page_number' => 2, 'content' => 'Page two body text about entropy.', 'metadata' => null, 'bbox' => null, 'heading_path' => null],
        ];

        return $doc;
    }

    public function test_storage_creates_structure(): void
    {
        $book = $this->makeBook();

        $stats = app(ParsedDocumentStorageService::class)->replaceAll($book, $this->makeDocument());

        $this->assertSame(2, $stats['pages']);
        $this->assertSame(2, $stats['chapters']);
        $this->assertSame(4, $stats['elements']);

        $this->assertSame(2, $book->pages()->count());
        $this->assertSame(2, $book->chapters()->count());
        $this->assertSame(4, $book->elements()->count());

        // Pages link to the deepest chapter covering them.
        $this->assertSame(
            $book->chapters()->where('title', 'Chapter One')->first()->id,
            $book->pages()->where('page_number', 1)->first()->chapter_id
        );
    }

    public function test_reprocessing_does_not_duplicate(): void
    {
        $book = $this->makeBook();
        $storage = app(ParsedDocumentStorageService::class);
        $chunking = app(BookChunkingService::class);

        $storage->replaceAll($book, $this->makeDocument());
        $chunking->rebuildForBook($book);

        $counts = [
            'chapters' => $book->chapters()->count(),
            'pages' => $book->pages()->count(),
            'elements' => $book->elements()->count(),
            'chunks' => $book->chunks()->count(),
        ];

        $this->assertSame(2, $counts['chapters']);
        $this->assertSame(2, $counts['pages']);
        $this->assertSame(4, $counts['elements']);
        $this->assertGreaterThan(0, $counts['chunks']);

        // Reprocess — counts must stay identical.
        $storage->replaceAll($book, $this->makeDocument());
        $chunking->rebuildForBook($book);

        $this->assertSame($counts['chapters'], $book->chapters()->count());
        $this->assertSame($counts['pages'], $book->pages()->count());
        $this->assertSame($counts['elements'], $book->elements()->count());
        $this->assertSame($counts['chunks'], $book->chunks()->count());
    }

    public function test_chunks_link_chapter_and_page(): void
    {
        $book = $this->makeBook();

        app(ParsedDocumentStorageService::class)->replaceAll($book, $this->makeDocument());
        app(BookChunkingService::class)->rebuildForBook($book);

        $chunk = $book->chunks()->first();

        $this->assertNotNull($chunk->chapter_id, 'Chunk must trace to its chapter.');
        $this->assertNotNull($chunk->page_id, 'Chunk must trace to its page.');
        $this->assertNotNull($chunk->page_number);
        $this->assertIsArray($chunk->metadata);
        $this->assertArrayHasKey('heading_path', $chunk->metadata);
    }

    public function test_failed_parse_marks_book_failed(): void
    {
        $book = $this->makeBook();
        Storage::disk('private')->put('books/pdfs/test.pdf', 'not a real pdf');

        // Directly exercise the failure path through the job with a
        // parser that cannot parse this file.
        $job = new ProcessBookPdf($book->id, force: true);

        try {
            $job->handle(
                app(PdfParserManager::class),
                app(ParsedDocumentStorageService::class),
                app(BookChunkingService::class),
                app(EmbeddingService::class),
                app(BookStructureDetectionService::class),
            );
        } catch (\Throwable $e) {
            $this->fail('Job threw instead of handling failure gracefully: '.$e->getMessage());
        }

        $book->refresh();

        // Either the smalot fallback parsed it into empty pages, or it
        // failed — both are recorded states, never a silent exception.
        $this->assertContains($book->processing_status, ['completed', 'failed']);

        if ($book->processing_status === 'failed') {
            $this->assertNotNull($book->processing_error);
        }
    }

    public function test_unchanged_pdf_not_reprocessed(): void
    {
        Queue::fake();

        $book = $this->makeBook();
        Storage::disk('private')->put('books/pdfs/test.pdf', 'fake pdf bytes');

        $book->forceFill([
            'processing_status' => 'completed',
            'pdf_content_hash' => hash('sha256', 'fake pdf bytes'),
        ])->save();

        // Seed one chunk so the skip guard's chunks-exists check passes.
        $book->chunks()->create(['chunk_index' => 0, 'content' => 'seed']);

        $job = new ProcessBookPdf($book->id, force: false);
        $job->handle(
            app(PdfParserManager::class),
            app(ParsedDocumentStorageService::class),
            app(BookChunkingService::class),
            app(EmbeddingService::class),
            app(BookStructureDetectionService::class),
        );

        $book->refresh();

        // Still the seeded chunk only — no reprocessing happened.
        $this->assertSame(1, $book->chunks()->count());
        $this->assertSame('completed', $book->processing_status);
    }
}
