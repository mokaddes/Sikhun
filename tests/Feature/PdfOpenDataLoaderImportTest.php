<?php

namespace Tests\Feature;

use App\Contracts\ParsedDocument;
use App\Models\Book;
use App\Services\Ai\BookChunkingService;
use App\Services\Pdf\OpenDataLoader\OpenDataLoaderWorkerParser;
use App\Services\Pdf\ParsedDocumentStorageService;
use App\Services\Pdf\PdfParserException;
use App\Services\Pdf\PdfParserManager;
use App\Services\Pdf\Php\SmalotPdfParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * OpenDataLoader worker integration (spec §4/§7): the canonical-JSON →
 * ParsedDocument mapping, page/source traceability, and the worker's safe
 * degradation. No Node/Java required — the mapping is exercised against a
 * fixture run directory.
 */
class PdfOpenDataLoaderImportTest extends TestCase
{
    use RefreshDatabase;

    private string $runDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runDir = storage_path('framework/testing/pdf-worker-'.uniqid());
        File::ensureDirectoryExists($this->runDir.'/images');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->runDir);

        parent::tearDown();
    }

    private function writePage(int $page, array $elements, ?string $text = null): void
    {
        File::put($this->runDir.'/page-'.str_pad((string) $page, 4, '0', STR_PAD_LEFT).'.json', json_encode([
            'page' => $page,
            'text' => $text,
            'size' => ['width' => 600, 'height' => 800],
            'elements' => $elements,
        ]));
    }

    public function test_canonical_json_maps_into_parsed_document(): void
    {
        File::put($this->runDir.'/manifest.json', json_encode(['odl_version' => '2.5.0', 'pages' => 1]));
        File::put($this->runDir.'/images/pic.png', 'PNG-BYTES');

        $this->writePage(1, [
            ['id' => 'e1', 'type' => 'heading', 'text' => 'Chapter 1', 'level' => 1, 'bbox' => ['x' => 0, 'y' => 0, 'width' => 100, 'height' => 20]],
            ['id' => 'e2', 'type' => 'text', 'text' => 'Gravity pulls things down.', 'bbox' => [0, 20, 100, 40]],
            ['id' => 'e3', 'type' => 'table', 'table' => ['headers' => ['a', 'b'], 'rows' => [['1', '2']]], 'markdown' => '| a | b |'],
            ['id' => 'e4', 'type' => 'formula', 'latex' => 'E=mc^2', 'text' => 'E=mc^2'],
            ['id' => 'e5', 'type' => 'image', 'file' => 'images/pic.png', 'alt' => 'diagram', 'width' => 10, 'height' => 10],
        ], 'Chapter 1 Gravity pulls things down.');

        $doc = (new OpenDataLoaderWorkerParser)->documentFromRunDir($this->runDir);

        $this->assertSame('opendataloader', $doc->parserName);
        $this->assertSame('2.5.0', $doc->parserVersion);

        $this->assertCount(1, $doc->pages);
        $this->assertSame(1, $doc->pages[0]['page_number']);
        $this->assertSame('Chapter 1 Gravity pulls things down.', $doc->pages[0]['content']);

        $this->assertCount(5, $doc->elements);
        $this->assertSame(
            ['heading', 'text', 'table', 'formula', 'image'],
            array_column($doc->elements, 'type'),
        );
        $this->assertSame(
            ['e1', 'e2', 'e3', 'e4', 'e5'],
            array_column($doc->elements, 'source_id'),
        );

        $this->assertCount(1, $doc->tables);
        $this->assertSame(['a', 'b'], $doc->tables[0]['content']['headers']);
        $this->assertSame('| a | b |', $doc->tables[0]['markdown']);

        $this->assertCount(1, $doc->formulas);
        $this->assertSame('E=mc^2', $doc->formulas[0]['latex']);

        $this->assertCount(1, $doc->images);
        $this->assertSame('PNG-BYTES', $doc->images[0]['binary']);
        $this->assertSame('png', $doc->images[0]['extension']);
        $this->assertSame('diagram', $doc->images[0]['alt_text']);
    }

    public function test_ocr_fallback_provenance_surfaces_from_manifest(): void
    {
        File::put($this->runDir.'/manifest.json', json_encode([
            'odl_version' => '2.5.0',
            'pages' => 3,
            'ocr_used' => true,
            'ocr_tool' => 'tesseract',
            'ocr_lang' => 'eng+ben',
            'ocr_pages' => 3,
        ]));

        $this->writePage(1, [
            ['id' => 'p1-l1', 'type' => 'text', 'text' => 'Line one from OCR.', 'metadata' => ['source' => 'ocr']],
        ], 'Line one from OCR.');

        $doc = (new OpenDataLoaderWorkerParser)->documentFromRunDir($this->runDir);

        $this->assertTrue($doc->ocrUsed);
        $this->assertSame('tesseract', $doc->ocrTool);
        $this->assertSame('eng+ben', $doc->ocrLang);
        $this->assertSame(3, $doc->ocrPages);
    }

    public function test_worker_unavailable_degrades_and_forced_parser_fails_loudly(): void
    {
        config()->set('pdf-parsing.node_worker.enabled', false);

        // auto → smalot fallback, never an exception.
        $this->assertSame(
            SmalotPdfParser::class,
            get_class(app(PdfParserManager::class)->resolve()),
        );

        // Explicitly forcing opendataloader on a host where it cannot run is
        // an operator error and must be surfaced, not silently downgraded.
        config()->set('pdf-parsing.parser', 'opendataloader');

        $this->expectException(PdfParserException::class);

        app(PdfParserManager::class)->resolve();
    }

    public function test_source_and_element_traceability_survives_storage_and_chunking(): void
    {
        Storage::fake('private');

        $book = Book::create([
            'title' => 'Trace Book',
            'slug' => 'trace-'.uniqid(),
            'price' => 0,
            'is_published' => true,
        ]);

        $doc = new ParsedDocument;
        $doc->parserName = 'opendataloader';
        $doc->parserVersion = '2.5.0';
        $doc->pages = [
            ['page_number' => 1, 'content' => 'one', 'metadata' => null],
            ['page_number' => 2, 'content' => 'two', 'metadata' => null],
        ];
        $doc->chapters = [
            ['chapter_number' => '1', 'title' => 'Chapter One', 'level' => 1, 'start_page' => 1, 'end_page' => 1, 'parent_index' => null, 'metadata' => null],
            ['chapter_number' => '2', 'title' => 'Chapter Two', 'level' => 1, 'start_page' => 2, 'end_page' => 2, 'parent_index' => null, 'metadata' => null],
        ];
        $doc->elements = [
            ['type' => 'heading', 'page_number' => 1, 'content' => 'Chapter One', 'metadata' => null, 'bbox' => null, 'heading_path' => null, 'source_id' => 'p1-h1'],
            ['type' => 'text', 'page_number' => 1, 'content' => 'Body about gravity.', 'metadata' => null, 'bbox' => null, 'heading_path' => null, 'source_id' => 'p1-t1'],
            ['type' => 'heading', 'page_number' => 2, 'content' => 'Chapter Two', 'metadata' => null, 'bbox' => null, 'heading_path' => null, 'source_id' => 'p2-h1'],
            ['type' => 'text', 'page_number' => 2, 'content' => 'Body about entropy.', 'metadata' => null, 'bbox' => null, 'heading_path' => null, 'source_id' => 'p2-t1'],
        ];

        app(ParsedDocumentStorageService::class)->replaceAll($book, $doc);

        // source_id is persisted for every element.
        $this->assertSame('p1-h1', $book->elements()->orderBy('sort_order')->first()->source_id);

        app(BookChunkingService::class)->rebuildForBook($book);

        $first = $book->chunks()->orderBy('chunk_index')->first();
        $last = $book->chunks()->orderByDesc('chunk_index')->first();

        $this->assertIsArray($first->element_ids);

        $headingOne = $book->elements()->where('source_id', 'p1-h1')->first()->id;
        $textOne = $book->elements()->where('source_id', 'p1-t1')->first()->id;
        $headingTwo = $book->elements()->where('source_id', 'p2-h1')->first()->id;
        $textTwo = $book->elements()->where('source_id', 'p2-t1')->first()->id;

        $this->assertContains($headingOne, $first->element_ids);
        $this->assertContains($textOne, $first->element_ids);
        $this->assertContains($headingTwo, $last->element_ids);
        $this->assertContains($textTwo, $last->element_ids);
    }
}
