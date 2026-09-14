<?php

namespace App\Services\Pdf\OpenDataLoader;

use App\Contracts\ParsedDocument;
use App\Contracts\PdfParserContract;
use App\Models\Book;
use App\Services\Pdf\PdfParserException;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * OpenDataLoader-PDF integration (https://github.com/opendataloader-project/opendataloader-pdf).
 *
 * OpenDataLoader is a Rust CLI — it cannot run in-process inside PHP, so
 * this implementation shells out to the binary via Symfony Process (Laravel
 * Process facade), pointing it at the book's private-disk PDF, and reads
 * back the structured JSON it emits.
 *
 * IMPORTANT — this implementation is deliberately defensive: OpenDataLoader
 * output schema details (exact JSON keys) are mapped centrally in
 * mapOutputToDocument() so schema drift or minor differences only require
 * touching that one method.
 */
class OpenDataLoaderPdfParser implements PdfParserContract
{
    private const DEFAULT_TIMEOUT = 600;

    public function __construct(
        private ?string $binaryPath = null,
        private ?int $timeout = null,
    ) {
        $this->binaryPath ??= (string) config('pdf-parsing.opendataloader.binary_path', 'opendataloader-pdf');
        $this->timeout ??= (int) (config('pdf-parsing.opendataloader.timeout') ?: self::DEFAULT_TIMEOUT);
    }

    public function isAvailable(): bool
    {
        if ($this->binaryPath && file_exists($this->binaryPath)) {
            return true;
        }

        // Otherwise assume it's on PATH (array form — no shell escaping
        // needed). A missing binary makes proc_open fail to START (it
        // throws ProcessStartFailedException rather than returning a failed
        // result, especially on Windows) — catch that so availability is
        // always a boolean and the smalot fallback stays reachable.
        try {
            $result = Process::timeout(10)->run([$this->binaryPath, '--version']);

            return $result->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    public function parse(Book $book): ParsedDocument
    {
        if (! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
            throw new PdfParserException("Book {$book->id} has no PDF on the private disk.");
        }

        if (! $this->isAvailable()) {
            throw new PdfParserException('OpenDataLoader binary not found at: '.$this->binaryPath);
        }

        $pdfPath = Storage::disk('private')->path($book->pdf_path);
        $outputFile = $this->outputPath($book);

        try {
            $result = Process::timeout($this->timeout)->run(array_merge([
                $this->binaryPath,
                '--output', $outputFile,
                '--format', 'json',
            ], $this->extraArgs(), [$pdfPath]));

            if (! $result->successful()) {
                throw new PdfParserException('OpenDataLoader exited with code '.$result->exitCode().': '.Str::limit($result->errorOutput(), 500));
            }

            if (! file_exists($outputFile)) {
                throw new PdfParserException("OpenDataLoader produced no JSON output at {$outputFile}.");
            }

            $json = json_decode((string) file_get_contents($outputFile), true);
            if (! is_array($json)) {
                throw new PdfParserException("OpenDataLoader output at {$outputFile} is not valid JSON.");
            }

            return $this->mapOutputToDocument($json);
        } finally {
            @unlink($outputFile);
        }
    }

    /**
     * Vendor-specific mapping — THE ONLY place that knows OpenDataLoader's
     * JSON schema. Kept tolerant: missing keys degrade to nulls/skips,
     * never exceptions.
     */
    private function mapOutputToDocument(array $json): ParsedDocument
    {
        $doc = new ParsedDocument();
        $doc->parserName = 'opendataloader';
        $doc->parserVersion = (string) ($json['version'] ?? 'unknown');

        $pages = $json['pages'] ?? $json['documents'] ?? [];

        foreach ($pages as $page) {
            $pageNumber = $page['page'] ?? $page['page_number'] ?? $page['number'] ?? null;
            if ($pageNumber === null) {
                continue; // skip unidentifiable page entries
            }

            $this->mapPage($doc, $pageNumber, $page);
        }

        return $doc;
    }

    private function mapPage(ParsedDocument $doc, int $pageNumber, array $page): void
    {
        $doc->pages[] = [
            'page_number' => $pageNumber,
            'content' => $page['text'] ?? $page['content'] ?? null,
            'metadata' => isset($page['size']) ? ['size' => $page['size']] : null,
        ];

        foreach ($page['elements'] ?? $page['blocks'] ?? [] as $element) {
            $this->mapElement($doc, $pageNumber, $element);
        }
    }

    private function mapElement(ParsedDocument $doc, int $pageNumber, array $element): void
    {
        $type = $this->normalizeType($element['type'] ?? null);
        $content = $element['text'] ?? $element['content'] ?? null;
        $bbox = $this->normalizeBbox($element['bbox'] ?? $element['box'] ?? $element['bounding_box'] ?? null);
        $metadata = array_diff_key($element, array_flip(['type', 'text', 'content', 'bbox', 'box', 'bounding_box']));

        $elementIndex = count($doc->elements);
        $doc->elements[] = [
            'type' => $type,
            'page_number' => $pageNumber,
            'content' => $content,
            'metadata' => $metadata ?: null,
            'bbox' => $bbox,
            'heading_path' => null, // filled by chapter assignment pass
        ];

        // Promote specialized element types into their dedicated structures.
        match ($type) {
            'table' => $doc->tables[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'title' => $element['title'] ?? $element['caption'] ?? null,
                'content' => $this->normalizeTableContent($element),
                'markdown' => $element['markdown'] ?? null,
                'html' => $element['html'] ?? null,
                'metadata' => $metadata ?: null,
            ],
            'image' => $doc->images[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'binary' => $element['image'] ?? $element['data'] ?? null,
                'extension' => $element['extension'] ?? (isset($element['mime_type']) ? ltrim(strchr($element['mime_type'], '/'), '/') : 'png'),
                'alt_text' => $element['alt'] ?? $element['alt_text'] ?? null,
                'description' => $element['description'] ?? null,
                'ocr_text' => $element['ocr'] ?? $element['ocr_text'] ?? null,
                'bbox' => $bbox,
                'metadata' => $metadata ?: null,
            ],
            'formula' => $doc->formulas[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'latex' => $element['latex'] ?? null,
                'content' => is_string($content) ? $content : null,
                'bbox' => $bbox,
                'metadata' => $metadata ?: null,
            ],
            default => null,
        };
    }

    private function normalizeType(?string $raw): string
    {
        $type = strtolower(trim((string) $raw));

        return match (true) {
            in_array($type, ['text', 'paragraph', 'body'], true) => 'text',
            in_array($type, ['heading', 'title', 'section_header', 'section-header'], true) => 'heading',
            $type === 'table' => 'table',
            $type === 'image' || $type === 'figure' => 'image',
            $type === 'formula' || $type === 'equation' => 'formula',
            $type === 'list' => 'list',
            $type === 'caption' => 'caption',
            $type === 'quote' => 'quote',
            default => 'other',
        };
    }

    private function normalizeBbox($raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        // Accept both {x, y, width, height} and {x0, y0, x1, y1} shapes.
        if (isset($raw['x1'], $raw['y1'])) {
            return [
                'x' => (int) $raw['x0'],
                'y' => (int) $raw['y0'],
                'width' => (int) ($raw['x1'] - $raw['x0']),
                'height' => (int) ($raw['y1'] - $raw['y0']),
            ];
        }

        return isset($raw['x'], $raw['y'], $raw['width'], $raw['height']) ? array_map('intval', $raw) : null;
    }

    private function normalizeTableContent(array $element): ?array
    {
        $content = $element['table'] ?? $element['content'] ?? $element['rows'] ?? null;

        if (is_array($content)) {
            return $content;
        }

        // Parser only gave markdown/html — keep them; storage layer renders
        // a structured fallback from the markdown if needed.
        return null;
    }

    private function outputPath(Book $book): string
    {
        $dir = storage_path('app/pdf-parsing');

        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return $dir.DIRECTORY_SEPARATOR.'book-'.$book->id.'-'.Str::random(8).'.json';
    }

    private function extraArgs(): array
    {
        $args = [];

        if ($ocr = config('pdf-parsing.opendataloader.ocr_lang')) {
            $args[] = '--ocr-lang';
            $args[] = $ocr;
        }

        return $args;
    }
}
