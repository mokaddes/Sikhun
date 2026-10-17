<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\MyBook;
use App\Services\BookReaderService;
use Illuminate\Console\Command;

class PreRenderBookPages extends Command
{
    protected $signature = 'books:pre-render {book_id? : ID of the book to pre-render} {--my-books : Also pre-render student MyBooks} {--force : Re-render even if pages exist}';

    protected $description = 'Pre-render PDF pages of books into optimized JPEGs for instantaneous reader loading';

    public function handle(BookReaderService $reader): int
    {
        $bookId = $this->argument('book_id');
        $force = (bool) $this->option('force');

        $books = $bookId
            ? Book::where('id', $bookId)->get()
            : Book::whereNotNull('pdf_path')->get();

        foreach ($books as $book) {
            $this->info("Pre-rendering Book ID: {$book->id} - {$book->title}");
            $start = microtime(true);
            $count = $reader->preRenderAllPages($book, $force);
            if ($count > 0 && $book->total_pages !== $count) {
                $book->update(['total_pages' => $count]);
            }
            $this->info("  -> {$count} pages ready in " . round(microtime(true) - $start, 2) . "s");
        }

        if (! $bookId || $this->option('my-books')) {
            $myBooks = MyBook::whereNotNull('file_path')->get();
            foreach ($myBooks as $myBook) {
                $this->info("Pre-rendering MyBook ID: {$myBook->id} - {$myBook->title}");
                $start = microtime(true);
                $count = $reader->preRenderAllPages($myBook, $force);
                if ($count > 0 && $myBook->total_pages !== $count) {
                    $myBook->update(['total_pages' => $count]);
                }
                $this->info("  -> {$count} pages ready in " . round(microtime(true) - $start, 2) . "s");
            }
        }

        $this->info('All pre-rendering tasks completed.');

        return self::SUCCESS;
    }
}
