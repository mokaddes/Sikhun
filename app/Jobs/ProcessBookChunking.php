<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\BookChunk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ProcessBookChunking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'default';

    public $timeout = 300;

    private const CHUNK_SIZE_CHARS = 1200;

    public function __construct(private int $bookId) {}

    public function handle(): void
    {
        $book = Book::find($this->bookId);

        if (! $book || ! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
            return;
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile(Storage::disk('private')->path($book->pdf_path));

            $book->chunks()->delete();
            $index = 0;

            foreach ($pdf->getPages() as $pageNumber => $page) {
                $text = trim(preg_replace('/\s+/', ' ', $page->getText()));

                if ($text === '') {
                    continue;
                }

                foreach (str_split($text, self::CHUNK_SIZE_CHARS) as $piece) {
                    BookChunk::create([
                        'book_id' => $book->id,
                        'chunk_index' => $index++,
                        'page_number' => $pageNumber + 1,
                        'content' => $piece,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Book chunking failed for book {$book->id}: ".$e->getMessage());
        }
    }
}
