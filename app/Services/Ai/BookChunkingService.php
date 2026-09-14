<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\BookElement;
use Illuminate\Support\Facades\DB;

/**
 * Structure-aware chunking: walks the book's parsed elements in reading
 * order and builds chunks that never cross a chapter boundary and never
 * split a table or formula mid-element. Each chunk records its chapter,
 * page, heading path and related tables/images/formulas in metadata so
 * the retrieval layer can build structured RAG context.
 *
 * Replaces the old "split raw page text at N chars" approach while
 * remaining 100% backward compatible — old books simply have no chapter
 * linkage on their chunks until reprocessed.
 */
class BookChunkingService
{
    public function __construct(
        private int $maxChars = 0,
        private int $overlap = 0,
    ) {
        $this->maxChars = (int) (config('pdf-parsing.chunk_max_chars') ?: 1200);
        $this->overlap = (int) (config('pdf-parsing.chunk_overlap_chars') ?: 150);

        // Overlap must stay strictly below maxChars or splitWithOverlap
        // would never advance the offset.
        $this->overlap = min($this->overlap, intdiv(max($this->maxChars, 2), 2));
    }

    /**
     * Rebuild all chunks for a book from its stored elements.
     *
     * @return int number of chunks created
     */
    public function rebuildForBook(Book $book): int
    {
        return DB::transaction(function () use ($book) {
            $book->chunks()->delete();

            $elements = $book->elements()
                ->orderBy('sort_order')
                ->get(['id', 'book_id', 'chapter_id', 'page_id', 'type', 'content', 'page_number', 'metadata', 'sort_order']);

            if ($elements->isEmpty()) {
                return 0;
            }

            $chunks = [];
            $current = null; // chunk under construction
            $headingPath = [];
            $chunkIndex = 0;

            foreach ($elements as $element) {
                if ($element->type === 'heading') {
                    $this->flushChunk($chunks, $current, $chunkIndex);
                    $headingPath[] = (string) $element->content;
                    $headingPath = array_slice($headingPath, -4);
                    continue;
                }

                $content = trim((string) $element->content);
                if ($content === '') {
                    continue;
                }

                // Tables and formulas travel whole — never split, even if
                // larger than maxChars.
                if (in_array($element->type, ['table', 'formula'], true)) {
                    $this->flushChunk($chunks, $current, $chunkIndex);
                    $chunks[] = $this->makeChunk($book, $element, $headingPath, $content, $chunkIndex++);
                    continue;
                }

                if ($current === null) {
                    $current = $this->newBuffer($element, $headingPath);
                } elseif ($current['chapter_id'] !== $element->chapter_id || $current['page_number'] !== $element->page_number) {
                    // New chapter/page context — flush and start fresh so
                    // every chunk stays traceable to exactly one page.
                    $this->flushChunk($chunks, $current, $chunkIndex);
                    $current = $this->newBuffer($element, $headingPath);
                }

                $this->appendContent($current, $content, $chunks, $chunkIndex);
            }

            $this->flushChunk($chunks, $current, $chunkIndex);

            if ($chunks) {
                foreach (array_chunk($chunks, 250) as $batch) {
                    $book->chunks()->createMany($batch);
                }
            }

            return count($chunks);
        });
    }

    private function newBuffer(BookElement $element, array $headingPath): array
    {
        return [
            'book_id' => $element->book_id,
            'chapter_id' => $element->chapter_id,
            'page_id' => $element->page_id,
            'page_number' => $element->page_number,
            'metadata' => ['heading_path' => $headingPath],
            'content' => '',
        ];
    }

    private function appendContent(array &$buffer, string $content, array &$chunks, int &$chunkIndex): void
    {
        $separator = $buffer['content'] === '' ? '' : "\n";
        $buffer['content'] .= $separator.$content;

        // Once the buffer overflows maxChars, emit it (flushChunk splits it
        // with overlap) and let the next element start a fresh buffer —
        // text is never silently truncated.
        if (mb_strlen($buffer['content']) > $this->maxChars) {
            $this->flushChunk($chunks, $buffer, $chunkIndex);
            $buffer = null;
        }
    }

    private function makeChunk(Book $book, BookElement $element, array $headingPath, string $content, int $index): array
    {
        return [
            'book_id' => $book->id,
            'chapter_id' => $element->chapter_id,
            'page_id' => $element->page_id,
            'page_number' => $element->page_number,
            'chunk_index' => $index,
            'content' => $content,
            'metadata' => [
                'heading_path' => $headingPath,
                'element_type' => $element->type,
            ],
        ];
    }

    /**
     * Push the buffer into the chunk list (possibly multiple times when the
     * buffer overflowed maxChars) and reset it.
     */
    private function flushChunk(array &$chunks, ?array &$buffer, int &$index): void
    {
        if ($buffer === null) {
            return;
        }

        $content = trim((string) $buffer['content']);
        if ($content === '') {
            $buffer = null;

            return;
        }

        // Text longer than maxChars becomes several chunks with overlap so
        // retrieval still hits boundary-spanning queries.
        $pieces = $this->splitWithOverlap($content);

        foreach ($pieces as $piece) {
            $chunks[] = [
                'book_id' => $buffer['book_id'],
                'chapter_id' => $buffer['chapter_id'],
                'page_id' => $buffer['page_id'],
                'page_number' => $buffer['page_number'],
                'chunk_index' => $index++,
                'content' => $piece,
                'metadata' => $buffer['metadata'],
            ];
        }

        $buffer = null;
    }

    /**
     * @return string[]
     */
    private function splitWithOverlap(string $content): array
    {
        if (mb_strlen($content) <= $this->maxChars) {
            return [$content];
        }

        $pieces = [];
        $offset = 0;

        while ($offset < mb_strlen($content)) {
            $piece = mb_substr($content, $offset, $this->maxChars);

            // Prefer breaking at the last sentence/paragraph edge inside
            // the piece rather than mid-word. PREG_OFFSET_CAPTURE returns
            // BYTE offsets — convert to characters before mb_substr, or
            // multibyte (Bengali) content truncates wrongly.
            if (mb_strlen($piece) === $this->maxChars && $offset + $this->maxChars < mb_strlen($content)) {
                if (preg_match('/[\n.!?।](?=[^\n.!?।]*$)/u', $piece, $m, PREG_OFFSET_CAPTURE)) {
                    $charPos = mb_strlen(substr($piece, 0, $m[0][1]));
                    $piece = mb_substr($piece, 0, $charPos + 1);
                }
            }

            $piece = trim($piece);
            if ($piece !== '') {
                $pieces[] = $piece;
            }

            $offset += max(1, mb_strlen($piece) - $this->overlap);
        }

        return $pieces;
    }
}
