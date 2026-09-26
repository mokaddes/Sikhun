<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\MyBook;
use App\Services\Pdf\PageTextService;
use Illuminate\Console\Command;

/**
 * Warms page text for a book or a student's own upload so reader chat —
 * and any future RAG rebuild — never has to OCR a page on first ask.
 * Run in the background for scanned books: ~10–15s per page.
 *
 *  php artisan book:extract-page-text --book=13 --from=1 --to=60
 *  php artisan book:extract-page-text --my-book=5
 */
class ExtractBookPageText extends Command
{
    protected $signature = 'book:extract-page-text
        {--book= : Admin-catalog Book id}
        {--my-book= : Student-uploaded MyBook id}
        {--from=1 : First page}
        {--to= : Last page (defaults to total_pages)}';

    protected $description = 'Extract and store text for PDF pages (text layer, then OCR fallback)';

    public function handle(PageTextService $pages): int
    {
        $bookId = $this->option('book');
        $myBookId = $this->option('my-book');

        if ((bool) $bookId === (bool) $myBookId) {
            $this->error('Provide exactly one of --book= or --my-book=.');

            return self::INVALID;
        }

        $model = $bookId ? Book::find((int) $bookId) : MyBook::find((int) $myBookId);

        if (! $model) {
            $this->error('Book not found.');

            return self::FAILURE;
        }

        if (! $model->pdfFilePath()) {
            $this->error('Book has no PDF on the private disk.');

            return self::FAILURE;
        }

        $from = max(1, (int) $this->option('from'));
        $to = min(
            (int) $this->option('to') ?: (int) $model->total_pages,
            (int) $model->total_pages ?: PHP_INT_MAX,
        );

        if ($to < $from) {
            $this->error('--to must be >= --from.');

            return self::INVALID;
        }

        $this->info("Backfilling pages {$from}–{$to} of {$model->title}…");

        $done = 0;
        $started = now();

        foreach (range($from, $to) as $page) {
            $text = $pages->forPage($model, $page);

            if ($text !== null && trim($text) !== '') {
                $done++;
                $this->line("  page {$page}: ok (".mb_strlen($text).' chars)');
            } else {
                $this->warn("  page {$page}: no text (skipped)");
            }
        }

        $this->info("Done: {$done}/".($to - $from + 1)." pages stored in ".now()->diffForHumans($started, true).'.');

        return self::SUCCESS;
    }
}