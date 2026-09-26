<?php

namespace App\Jobs;

use App\Contracts\ParsedDocument;
use App\Models\Book;
use App\Services\Ai\BookChunkingService;
use App\Services\Ai\BookStructureDetectionService;
use App\Services\Ai\EmbeddingService;
use App\Services\Pdf\ParsedDocumentStorageService;
use App\Services\Pdf\PdfParserException;
use App\Services\Pdf\PdfParserManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Full PDF → structured-document → RAG pipeline, replacing the old
 * text-only ProcessBookChunking job. Stages:
 *
 *   parse (OpenDataLoader or smalot fallback)
 *     → store chapters/pages/elements/tables/images/formulas
 *     → rebuild structure-aware chunks
 *     → generate embeddings (non-fatal when no provider configured)
 *
 * Book-level state lives on books.processing_status so the admin UI can
 * show progress and offer a retry. Skipping logic: when the PDF content
 * hash is unchanged AND the previous run completed, we do nothing — an
 * unchanged file never reprocesses.
 */
class ProcessBookPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // A 440-page scanned book OCRs at ~10–15s/page (JVM + pdftoppm +
    // tesseract), so a full parse can take ~1.5h. Queue retry_after must
    // stay above this or a long job gets re-delivered mid-run.
    public $timeout = 10800;
    public $tries = 2;

    public function __construct(
        private int $bookId,
        private bool $force = false,
    ) {
        $this->onQueue('default');
    }

    public function handle(
        PdfParserManager $parsers,
        ParsedDocumentStorageService $storage,
        BookChunkingService $chunking,
        EmbeddingService $embeddings,
        BookStructureDetectionService $structure,
    ): void {
        $book = Book::find($this->bookId);

        if (! $book || ! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
            Log::warning('ProcessBookPdf skipped: book or PDF missing.', ['book_id' => $this->bookId]);

            return;
        }

        // Skip when nothing changed since the last successful parse.
        $contentHash = hash_file('sha256', Storage::disk('private')->path($book->pdf_path));

        if (! $this->force
            && $book->processing_status === 'completed'
            && $book->pdf_content_hash === $contentHash
            && $book->chunks()->exists()) {
            Log::info('ProcessBookPdf skipped: PDF unchanged since last successful parse.', ['book_id' => $book->id]);

            return;
        }

        $book->forceFill([
            'processing_status' => 'processing',
            'processing_error' => null,
            'processing_started_at' => now(),
        ])->save();

        try {
            $parser = $parsers->resolve();
            $doc = $parser->parse($book);

            // A completed run MUST contain text — otherwise the book would
            // silently end up "completed" with empty chapters/chunks and both
            // the reader and chat return nothing while the admin sees success.
            // Scanned/image-only PDFs (common for CV exports) extract no text;
            // the OpenDataLoader worker already retried those with tesseract
            // OCR. Only when text is STILL empty do we fail, with the reason
            // tailored to whether the OCR pass ran or was unavailable.
            $pagesWithText = $this->countPagesWithText($doc);

            if ($pagesWithText === 0) {
                throw new PdfParserException($this->emptyTextFailure($doc));
            }

            $structure->detect($doc);

            $stats = $storage->replaceAll($book, $doc);

            $chunkCount = $chunking->rebuildForBook($book);

            $book->forceFill([
                'processing_status' => 'completed',
                'processing_error' => null,
                'processing_completed_at' => now(),
                'parser_name' => $doc->parserName,
                'parser_version' => $doc->parserVersion,
                'parsed_at' => now(),
                'pdf_content_hash' => $contentHash,
                'total_pages' => $doc->pages ? count($doc->pages) : $book->total_pages,
            ])->save();

            Log::info('ProcessBookPdf completed', array_merge(['book_id' => $book->id], $stats, [
                'chunks' => $chunkCount,
                'ocr_used' => $doc->ocrUsed,
                'ocr_tool' => $doc->ocrTool,
                'ocr_lang' => $doc->ocrLang,
                'ocr_pages' => $doc->ocrPages,
            ]));

            // Embeddings are an enhancement: a missing embedding provider
            // must never mark the book as failed.
            try {
                $embedded = $embeddings->embedBookChunks($book->id);
                Log::info('ProcessBookPdf embeddings done', ['book_id' => $book->id, 'embedded' => $embedded]);
            } catch (\Throwable $e) {
                Log::warning('ProcessBookPdf embeddings skipped: '.$e->getMessage(), ['book_id' => $book->id]);
            }
        } catch (PdfParserException $e) {
            $this->failBook($book, 'Parser failure: '.$e->getMessage());

            return;
        } catch (\Throwable $e) {
            $this->failBook($book, get_class($e).': '.$e->getMessage());

            return;
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($book = Book::find($this->bookId)) {
            $this->failBook($book, 'Job failure: '.$e->getMessage());
        }
    }

    private function failBook(Book $book, string $error): void
    {
        $book->forceFill([
            'processing_status' => 'failed',
            'processing_error' => mb_substr($error, 0, 2000),
            'processing_completed_at' => now(),
        ])->save();

        Log::error('ProcessBookPdf failed', ['book_id' => $book->id, 'error' => $error]);
    }

    private function countPagesWithText(ParsedDocument $doc): int
    {
        return collect($doc->pages)
            ->filter(fn (array $page) => mb_strlen(trim((string) ($page['content'] ?? ''))) > 0)
            ->count();
    }

    /**
     * Failure message for a parse run with zero text. The wording depends on
     * whether the OpenDataLoader worker already attempted its tesseract OCR
     * fallback — telling the admin what to actually change.
     */
    private function emptyTextFailure(ParsedDocument $doc): string
    {
        if ($doc->ocrUsed) {
            return 'No text could be extracted even after OCR. The document is likely a low-quality scan, or no '
                .'OCR language is installed for this document’s script ('.(string) $doc->ocrLang.
                '). Install the matching tesseract language pack (e.g. tesseract-ocr-ben), set PDF_OCR_LANG, '
                .'and press Retry — or re-export the PDF with selectable text.';
        }

        return 'No text could be extracted from this PDF. It is likely a scanned or image-based document, and the '
            .'OCR fallback could not run. Install poppler-utils and tesseract-ocr on the server (plus language '
            .'packs, e.g. tesseract-ocr-ben), ensure PDF_OCR_ENABLED=true, then press Retry — or re-export the '
            .'PDF as a text PDF.'
            .($doc->ocrTool
                ? ' (OCR attempted with '.$doc->ocrTool.($doc->ocrLang ? ' / '.$doc->ocrLang : '').', no usable text.)'
                : '');
    }
}
