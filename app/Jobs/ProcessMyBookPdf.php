<?php

namespace App\Jobs;

use App\Models\MyBook;
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
 * Parses a student-uploaded PDF (own book / notes) into per-page text so
 * the student can later chat about the document. Only page-level text is
 * retained — the rich chapter/element/tables store stays admin-catalog
 * only. Reading does not depend on this job: pages render straight from
 * the stored PDF via the same signed-image pipeline as catalog books.
 */
class ProcessMyBookPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10800;
    public $tries = 2;

    public function __construct(private int $myBookId)
    {
        $this->onQueue('default');
    }

    public function handle(PdfParserManager $parsers): void
    {
        $myBook = MyBook::find($this->myBookId);

        if (! $myBook || ! $myBook->file_path || ! Storage::disk('private')->exists($myBook->file_path)) {
            Log::warning('ProcessMyBookPdf skipped: book or PDF missing.', ['my_book_id' => $this->myBookId]);

            return;
        }

        $contentHash = hash_file('sha256', Storage::disk('private')->path($myBook->file_path));

        if ($myBook->processing_status === 'completed' && $myBook->pdf_content_hash === $contentHash) {
            return;
        }

        $myBook->update([
            'processing_status' => 'processing',
            'processing_error' => null,
        ]);

        try {
            $doc = $parsers->resolve()->parse($myBook);

            $pages = collect($doc->pages)
                ->map(fn (array $page) => [
                    'page_number' => max(1, (int) $page['page_number']),
                    'content' => $page['content'],
                ])
                ->keyBy('page_number');

            if ($pages->isEmpty()) {
                throw new PdfParserException('No pages could be extracted from this PDF.');
            }

            $myBook->pages()->delete();

            foreach ($pages as $page) {
                $myBook->pages()->create($page);
            }

            $myBook->update([
                'processing_status' => 'completed',
                'processing_error' => null,
                'total_pages' => $pages->keys()->max(),
                'pdf_content_hash' => $contentHash,
            ]);

            Log::info('ProcessMyBookPdf completed', [
                'my_book_id' => $myBook->id,
                'pages' => $pages->count(),
            ]);
        } catch (PdfParserException $e) {
            $this->failBook($myBook, 'Parser failure: '.$e->getMessage());
        } catch (\Throwable $e) {
            $this->failBook($myBook, get_class($e).': '.$e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($myBook = MyBook::find($this->myBookId)) {
            $this->failBook($myBook, 'Job failure: '.$e->getMessage());
        }
    }

    private function failBook(MyBook $myBook, string $error): void
    {
        $myBook->update([
            'processing_status' => 'failed',
            'processing_error' => mb_substr($error, 0, 2000),
        ]);

        Log::error('ProcessMyBookPdf failed', ['my_book_id' => $myBook->id, 'error' => $error]);
    }
}