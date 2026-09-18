<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookChunk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Debug "chat with book" (RAG) by dumping what is actually stored for a
 * book versus what its PDF implies: processing state, page text lengths,
 * chapter tree, chunk counts/content and embedding coverage. Run on the
 * live server against the problem book and the cause is usually obvious —
 * e.g. "0 pages have text" (scanned PDF) or "chunks contain only lines X"
 * instead of the CV content the admin expected.
 */
class InspectBook extends Command
{
    protected $signature = 'book:inspect {book_id : Database id of the book to inspect}';

    protected $description = 'Dump a book\'s stored PDF/parse/RAG state to debug chat-with-book';

    public function handle(): int
    {
        $book = Book::withCount(['pages', 'chapters', 'elements', 'chunks'])->find($this->argument('book_id'));

        if (! $book) {
            $this->error("Book #{$this->argument('book_id')} not found.");

            return self::FAILURE;
        }

        $ready = Storage::disk('private')->exists($book->pdf_path ?? '');
        $size = $ready ? Storage::disk('private')->size($book->pdf_path) : null;
        $diskHash = $ready ? hash_file('sha256', Storage::disk('private')->path($book->pdf_path)) : null;

        $this->info("Book: {$book->title}");
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $book->id],
                ['PDF path', $book->pdf_path ?? '(none)'],
                ['PDF on disk', $ready ? 'yes' : 'NO'],
                ['PDF size (bytes)', $size ?? '(n/a)'],
                ['Disk hash == saved hash', $diskHash === $book->pdf_content_hash ? 'yes' : ($diskHash ? 'NO' : '(no saved hash)')],
                ['processing_status', $book->processing_status],
                ['processing_error', $book->processing_error ?? '(none)'],
                ['parser', $book->parser_name.($book->parser_version ? ' '.$book->parser_version : '')],
                ['parsed_at', $book->parsed_at?->toDateTimeString() ?? '(never)'],
                ['total_pages (stored)', $book->total_pages],
            ]
        );

        $this->info('Stored structure:');
        $this->table(
            ['Page count', 'Chapters', 'Elements', 'Chunks', 'Chunks with embedding'],
            [[
                $book->pages_count,
                $book->chapters_count,
                $book->elements_count,
                $book->chunks_count,
                BookChunk::where('book_id', $book->id)->whereNotNull('embedding')->count(),
            ]]
        );

        // Page-level text health — empty pages explain "chat found nothing".
        $pages = $book->pages()->orderBy('page_number')->get(['page_number', 'content']);
        $textRows = [];
        foreach ($pages as $page) {
            $len = mb_strlen(trim((string) $page->content));
            $textRows[] = [
                $page->page_number,
                $len,
                $len > 0 ? mb_substr(preg_replace('/\s+/u', ' ', (string) $page->content) ?: '', 0, 70) : '(EMPTY)',
            ];
        }
        $this->info('Pages ('.count($textRows).'; pages are the raw per-page extraction):');
        $this->table(['#', 'chars', 'first 70 chars'], $textRows);

        // Chapter outline — the same list chat receives for citations.
        $chapterRows = $book->chapters()->orderBy('sort_order')->get(['title', 'level', 'start_page', 'end_page'])
            ->map(fn ($c) => [$c->title, 'L'.$c->level, $c->start_page ?? '-', $c->end_page ?? '-'])
            ->all();
        $this->info('Chapters ('.count($chapterRows).' — this is what chat uses for the outline):');
        $this->table(['title', 'level', 'start_page', 'end_page'], $chapterRows);

        // Chunk samples — confirm chunks hold the expected content.
        $chunks = $book->chunks()->orderBy('chunk_index')->take(5)->get(['chunk_index', 'page_number', 'content']);
        $chunkRows = [];
        foreach ($chunks as $chunk) {
            $chunkRows[] = [
                $chunk->chunk_index,
                $chunk->page_number ?? '-',
                mb_substr(preg_replace('/\s+/u', ' ', $chunk->content) ?: '', 0, 70),
            ];
        }
        $this->info('First 5 chunks (chunks are what AI Chat searches):');
        $this->table(['chunk_index', 'page', 'first 70 chars'], $chunkRows);

        if (($book->processing_status ?? null) === 'completed' && $book->chunks_count === 0) {
            $this->warn('Book is "completed" but has ZERO chunks — usable text was never stored (scanned PDF, or elements were all classified as headings).');
        }

        return self::SUCCESS;
    }
}