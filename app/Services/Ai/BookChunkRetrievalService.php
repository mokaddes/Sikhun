<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\BookChunk;
use Illuminate\Support\Facades\DB;

/**
 * Retrieval half of "RAG" for AI Chat. Uses MySQL FULLTEXT search rather
 * than vector similarity (see the book_chunks migration for why) — good
 * enough to ground answers in the book's actual text without adding a
 * vector database to the stack.
 */
class BookChunkRetrievalService
{
    public function relevantChunks(Book $book, string $question, int $limit = 5): array
    {
        $chunks = BookChunk::where('book_id', $book->id)
            ->whereRaw('MATCH(content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$question])
            ->limit($limit)
            ->pluck('content');

        // FULLTEXT can return nothing for very short/stopword-only queries —
        // fall back to the book's first few chunks so chat still has *some*
        // grounding rather than none.
        if ($chunks->isEmpty()) {
            $chunks = BookChunk::where('book_id', $book->id)->orderBy('chunk_index')->limit($limit)->pluck('content');
        }

        return $chunks->all();
    }
}
