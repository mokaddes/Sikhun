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
 * OpenDataLoader PDF via the Node.js worker (pdf-worker/parse.js).
 *
 * OpenDataLoader is a Java CLI wrapped by the official Node SDK
 * (@opendataloader/pdf) — it cannot run in-process inside PHP. This parser
 * spawns the worker subprocess with a safe argv array (never shell
 * strings) pointing at the book's private-disk PDF, then streams the
 * worker's per-page canonical JSON back into a ParsedDocument.
 *
 * The worker emits page-NNNN.json files (NOT one giant document), so a
 * 500 MB book is imported incrementally without ever loading the whole
 * parse result into PHP memory.
 */
class OpenDataLoaderWorkerParser implements PdfParserContract
{
    private const DEFAULT_TIMEOUT = 1200;

    private const MAX_PAGE_FILES = 50000; // sanity cap on page file count

    public function __construct(
        private ?string $nodePath = null,
        private ?string $workerPath = null,
        private ?int $timeout = null,
    ) {
        $this->nodePath ??= (string) (config('pdf-parsing.node_worker.node_path') ?: 'node');
        $this->workerPath ??= (string) (config('pdf-parsing.node_worker.worker_path') ?: base_path('pdf-worker/parse.js'));
        $this->timeout ??= (int) (config('pdf-parsing.node_worker.timeout') ?: self::DEFAULT_TIMEOUT);
    }

    public function isAvailable(): bool
    {
        if (! (bool) config('pdf-parsing.node_worker.enabled', true)) {
            return false;
        }

        if (! is_file($this->workerPath)) {
            return false;
        }

        try {
            return Process::timeout(10)->run([$this->nodePath, '--version'])->successful();
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
            throw new PdfParserException(
                'OpenDataLoader worker unavailable. Check PDF_NODE_WORKER_ENABLED, that node is on PATH, '
                .'and that pdf-worker/ exists with `npm install` run (requires Node 20+ and Java 11+).'
            );
        }

        $pdfPath = Storage::disk('private')->path($book->pdf_path);
        $workDir = $this->workDirectory($book->id);

        if (! is_dir($workDir) && ! @mkdir($workDir, 0775, true) && ! is_dir($workDir)) {
            throw new PdfParserException("Could not create worker output directory at {$workDir}.");
        }

        try {
            $args = [
                $this->nodePath,
                $this->workerPath,
                '--input', $pdfPath,
                '--output', $workDir,
            ];

            if ($ocrLang = config('pdf-parsing.opendataloader.ocr_lang')) {
                $args[] = '--ocr-lang';
                $args[] = (string) $ocrLang;
            }

            $result = Process::timeout($this->timeout)->run($args);

            if (! $result->successful()) {
                throw new PdfParserException(
                    'OpenDataLoader worker exited with code '.$result->exitCode().': '
                    .Str::limit($result->errorOutput(), 600)
                );
            }

            if (! $this->runHasPages($workDir)) {
                throw new PdfParserException('OpenDataLoader worker produced no page output.');
            }

            return $this->documentFromRunDir($workDir);
        } finally {
            $this->deleteDirectory($workDir);
        }
    }

    private function runHasPages(string $workDir): bool
    {
        return count($this->pageFiles($workDir)) > 0;
    }

    /** @return string[] absolute page file paths, numerically ordered */
    private function pageFiles(string $workDir): array
    {
        $files = glob($workDir.DIRECTORY_SEPARATOR.'page-*.json') ?: [];

        $files = array_filter($files, function (string $file) {
            return preg_match('/page-\d{4,}\.json$/', basename($file)) === 1;
        });

        usort($files, function (string $a, string $b) {
            return (int) preg_replace('/\D/', '', $a) <=> (int) preg_replace('/\D/', '', $b);
        });

        return array_slice(array_values($files), 0, self::MAX_PAGE_FILES);
    }

    /**
     * Stream the worker's per-page canonical JSON into a ParsedDocument.
     *
     * Public so the canonical-JSON → ParsedDocument mapping is testable
     * without Node/Java installed (no subprocess involved).
     */
    public function documentFromRunDir(string $workDir): ParsedDocument
    {
        $doc = new ParsedDocument;
        $doc->parserName = 'opendataloader';
        $doc->parserVersion = $this->manifestVersion($workDir) ?? 'unknown';

        $imagesDir = $workDir.DIRECTORY_SEPARATOR.'images';

        foreach ($this->pageFiles($workDir) as $pageFile) {
            $page = json_decode((string) file_get_contents($pageFile), true);

            if (! is_array($page)) {
                continue;
            }

            $pageNumber = (int) ($page['page'] ?? (int) basename($pageFile));
            $pageNumber = max(1, $pageNumber);

            $doc->pages[] = [
                'page_number' => $pageNumber,
                'content' => isset($page['text']) && is_string($page['text']) ? $page['text'] : null,
                'metadata' => isset($page['size']) ? ['size' => $page['size']] : null,
            ];

            foreach (($page['elements'] ?? []) as $element) {
                $this->importElement($doc, $imagesDir, $pageNumber, $element);
            }
        }

        return $doc;
    }

    private function importElement(ParsedDocument $doc, string $imagesDir, int $pageNumber, array $element): void
    {
        $type = $this->normalizeType($element['type'] ?? null);
        $content = $element['text'] ?? null;
        $sourceId = isset($element['id']) ? (string) $element['id'] : null;

        $metadata = $element['metadata'] ?? null;
        if (is_array($metadata)) {
            $metadata['source_id'] = $sourceId;
            $metadata['heading_level'] = $element['level'] ?? 1;
        } else {
            $metadata = ['source_id' => $sourceId, 'heading_level' => $element['level'] ?? 1];
        }

        $elementIndex = count($doc->elements);
        $doc->elements[] = [
            'type' => $type,
            'page_number' => $pageNumber,
            'content' => $content,
            'metadata' => $metadata,
            'bbox' => $element['bbox'] ?? null,
            'source_id' => $sourceId,
            'heading_path' => null,
        ];

        match ($type) {
            'table' => $doc->tables[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'title' => $element['title'] ?? null,
                'content' => $element['table'] ?? null,
                'markdown' => $element['markdown'] ?? null,
                'html' => $element['html'] ?? null,
                'metadata' => $metadata,
            ],
            'image' => $doc->images[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'binary' => $this->imageBinary($imagesDir, $element['file'] ?? null),
                'extension' => $this->imageExtension($element['file'] ?? null, $element['mime_type'] ?? null),
                'alt_text' => $element['alt'] ?? null,
                'description' => null,
                'ocr_text' => $element['ocr'] ?? null,
                'bbox' => $element['bbox'] ?? null,
                'metadata' => array_merge($metadata, array_filter([
                    'width' => $element['width'] ?? null,
                    'height' => $element['height'] ?? null,
                    'mime_type' => $element['mime_type'] ?? null,
                ])),
            ],
            'formula' => $doc->formulas[] = [
                'chapter_index' => null,
                'page_number' => $pageNumber,
                'element_index' => $elementIndex,
                'latex' => $element['latex'] ?? null,
                'content' => is_string($content) ? $content : null,
                'bbox' => $element['bbox'] ?? null,
                'metadata' => $metadata,
            ],
            default => null,
        };
    }

    private function imageBinary(string $imagesDir, ?string $file): ?string
    {
        if (! $file || ! is_dir($imagesDir)) {
            return null;
        }

        // Worker records RELATIVE paths (images/<id>.png); never follow
        // anything escaping the run dir's images/ folder.
        if (preg_match('#^images/[A-Za-z0-9_.-]+$#', $file) !== 1) {
            return null;
        }

        $path = $imagesDir.DIRECTORY_SEPARATOR.basename($file);

        return is_file($path) ? (string) file_get_contents($path) : null;
    }

    private function imageExtension(?string $file, mixed $mime): string
    {
        if ($mime && preg_match('#^image/([a-z0-9.+-]+)$#i', (string) $mime, $m)) {
            $ext = strtolower($m[1] === 'jpeg' ? 'jpg' : $m[1]);

            return preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
        }

        if ($file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            return preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
        }

        return 'png';
    }

    private function manifestVersion(string $workDir): ?string
    {
        $path = $workDir.DIRECTORY_SEPARATOR.'manifest.json';

        if (! is_file($path)) {
            return null;
        }

        $meta = json_decode((string) file_get_contents($path), true);

        if (! is_array($meta)) {
            return null;
        }

        foreach (['odl_version', 'sdk_version'] as $key) {
            $value = $meta[$key] ?? null;

            if (is_string($value) && $value !== '' && $value !== 'unknown') {
                return $value;
            }
        }

        return null;
    }

    private function normalizeType(mixed $raw): string
    {
        $type = strtolower(trim((string) $raw));

        return match ($type) {
            'heading', 'paragraph', 'text', 'table', 'image', 'formula',
            'list', 'list_item', 'caption', 'quote', 'code', 'page_header', 'page_footer' => $type,
            default => 'other',
        };
    }

    private function workDirectory(int $bookId): string
    {
        $base = rtrim((string) (config('pdf-parsing.node_worker.output_path') ?: storage_path('app/pdf-parsing')), '/\\');

        return $base.DIRECTORY_SEPARATOR.'book-'.$bookId.'-'.Str::random(8);
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = glob($dir.DIRECTORY_SEPARATOR.'*') ?: [];

        foreach ($items as $item) {
            is_dir($item) ? $this->deleteDirectory($item) : @unlink($item);
        }

        @rmdir($dir);
    }
}
