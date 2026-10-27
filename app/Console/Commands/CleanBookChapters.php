<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookChapter;
use App\Services\Ai\BookStructureDetectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanBookChapters extends Command
{
    protected $signature = 'books:clean-chapters {book_id? : Optional specific book ID}';
    protected $description = 'Clean up running header duplicates and normalize chapter titles for books';

    public function handle(BookStructureDetectionService $structureService): int
    {
        $bookId = $this->argument('book_id');
        $query = Book::query();
        if ($bookId) {
            $query->where('id', $bookId);
        }

        $books = $query->whereHas('chapters')->get();
        if ($books->isEmpty()) {
            $this->info('No books with chapters found.');
            return 0;
        }

        foreach ($books as $book) {
            $this->info("Processing book #{$book->id}: {$book->title}");
            $rawChapters = $book->chapters()->orderBy('start_page')->orderBy('level')->get();
            $this->line("  Found {$rawChapters->count()} raw chapters.");

            $headings = $rawChapters->map(fn ($c) => [
                'title' => $c->title,
                'chapter_number' => $c->chapter_number,
                'level' => $c->level,
                'start_page' => $c->start_page,
            ])->all();

            $cleaned = $structureService->deduplicateAndCleanHeadings($headings);
            $this->info("  Cleaned down to " . count($cleaned) . " genuine chapters.");

            if (empty($cleaned)) {
                continue;
            }

            DB::transaction(function () use ($book, $cleaned) {
                // Delete existing duplicate chapters
                $book->chapters()->delete();

                $lastPage = $book->total_pages ?: 1;
                foreach ($cleaned as $i => $h) {
                    $level = $h['level'];
                    $end = $lastPage;
                    for ($j = $i + 1; $j < count($cleaned); $j++) {
                        if ($cleaned[$j]['level'] <= $level) {
                            $end = $cleaned[$j]['start_page'] - 1;
                            break;
                        }
                    }

                    $book->chapters()->create([
                        'parent_id' => null,
                        'chapter_number' => $h['chapter_number'],
                        'title' => $h['title'],
                        'slug' => \Illuminate\Support\Str::slug($h['title']) ?: "chapter-{$i}",
                        'level' => $level,
                        'start_page' => $h['start_page'],
                        'end_page' => max($h['start_page'], $end),
                        'sort_order' => $i,
                        'metadata' => ['cleaned_at' => now()->toIso8601String()],
                    ]);
                }
            });

            $this->info("  Successfully rebuilt chapters for book #{$book->id}!");
        }

        return 0;
    }
}
