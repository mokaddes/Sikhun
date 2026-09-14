<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\Ai\BookChunkingService;
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

    public $timeout = 3600;
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
}
