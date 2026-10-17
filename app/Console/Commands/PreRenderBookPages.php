<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PreRenderBookPages extends Command
{
    protected $signature = 'books:pre-render {book_id? : ID of the book to pre-render} {--force : Re-render even if pages exist}';

    protected $description = 'Pre-render PDF pages of books into optimized JPEGs for instantaneous reader loading';

    public function handle(): int
    {
        $bookId = $this->argument('book_id');
        $force = (bool) $this->option('force');

        $books = $bookId
            ? Book::where('id', $bookId)->get()
            : Book::whereNotNull('pdf_path')->get();

        if ($books->isEmpty()) {
            $this->warn('No books found matching the criteria.');
            return self::SUCCESS;
        }

        foreach ($books as $book) {
            $this->renderBook($book, $force);
        }

        $this->info('All pre-rendering tasks completed.');
        return self::SUCCESS;
    }

    protected function renderBook(Book $book, bool $force): void
    {
        $this->info("Processing Book ID: {$book->id} - {$book->title}");

        if (! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
            $this->warn("  [SKIP] PDF file does not exist: {$book->pdf_path}");
            return;
        }

        $pdfPath = Storage::disk('private')->path($book->pdf_path);
        $renderedDir = Storage::disk('private')->path("books/rendered/{$book->id}");

        if (! is_dir($renderedDir)) {
            @mkdir($renderedDir, 0775, true);
        }

        $existingCount = count(glob("{$renderedDir}/*.jpg"));
        if (! $force && $existingCount > 0 && ($book->total_pages && $existingCount >= $book->total_pages)) {
            $this->line("  [OK] Already fully rendered ({$existingCount} pages). Use --force to re-render.");
            return;
        }

        $this->line("  Rendering pages via pdftoppm (120 DPI)...");
        $startTime = microtime(true);

        $tempPrefix = "{$renderedDir}/page";
        $cmd = sprintf(
            'pdftoppm -jpeg -r 120 %s %s 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($tempPrefix)
        );

        $output = [];
        $code = 0;
        @exec($cmd, $output, $code);

        if ($code !== 0) {
            $this->error("  [FAILED] pdftoppm exited with code {$code}: " . implode("\n", $output));
            return;
        }

        // Rename generated files: page-001.jpg -> 1.jpg
        $generated = glob("{$renderedDir}/page-*.jpg");
        foreach ($generated as $file) {
            if (preg_match('/page-0*([0-9]+)\.jpg$/', $file, $matches)) {
                $pageNum = (int) $matches[1];
                $target = "{$renderedDir}/{$pageNum}.jpg";
                @rename($file, $target);
            }
        }

        $totalRendered = count(glob("{$renderedDir}/*.jpg"));
        $duration = round(microtime(true) - $startTime, 2);

        if ($book->total_pages !== $totalRendered && $totalRendered > 0) {
            $book->update(['total_pages' => $totalRendered]);
        }

        $this->info("  [DONE] Rendered {$totalRendered} pages in {$duration}s.");
    }
}
