<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\BookChunk;
use App\Models\Student;
use App\Services\BookAccessService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Access-aware retrieval for AI Chat — the security-critical half of RAG.
 *
 * THE RULE (spec §15): access filtering happens as EARLY as possible, in
 * the SQL itself. Candidate chunks are ALWAYS restricted to chapters the
 * student can read BEFORE any ranking, so paid content can never leak
 * into an AI context — not through keyword search, not through semantic
 * re-ranking, not through citation building.
 *
 * Ranking is hybrid: MySQL FULLTEXT finds candidates, then (when the
 * query and chunks both have embeddings) cosine similarity re-ranks them.
 * Chunks from legacy books without embeddings fall back to FULLTEXT-only
 * ordering, keeping old books fully functional.
 */
class BookChunkRetrievalService
{
    private const CANDIDATE_MULTIPLIER = 4; // candidates fetched before re-rank

    public function __construct(
        private BookAccessService $access,
        private EmbeddingService $embeddings,
    ) {}

    /**
     * Retrieve chunks relevant to a question, restricted to what the
     * student may read.
     *
     * @return array<int, array{
     *     content: string, chapter_id: ?int, page_id: ?int, page_number: ?int,
     *     metadata: ?array, element_ids: ?array, score: float
     * }>
     */
    public function relevantChunks(Book $book, string $question, ?Student $student = null, int $limit = 5): array
    {
        // Access scope resolved FIRST by the single authority on access.
        $accessibleChapterIds = $this->access->accessibleChapterIds($student, $book);

        if ($accessibleChapterIds === null) {
            return []; // book not accessible at all
        }

        $candidates = $this->fulltextCandidates($book, $question, $accessibleChapterIds, $limit * self::CANDIDATE_MULTIPLIER);

        // FULLTEXT can return nothing for very short/stopword-only queries
        // (Bengali question words, "CV", book titles, …). Falling back to the
        // first N chunks is fine for keyword-ish questions but useless for
        // book-level ones ("whose CV is this?") when the answer sits deeper
        // in the document — so sample the opening PLUS evenly spaced chunks
        // across the whole book instead of only pages 1..N.
        if ($candidates->isEmpty()) {
            $base = BookChunk::query()
                ->where('book_id', $book->id)
                ->when($accessibleChapterIds !== [], fn ($q) => $q->whereIn('chapter_id', $accessibleChapterIds))
                ->when($accessibleChapterIds === [], fn ($q) => $q->whereNull('chapter_id'))
                ->orderBy('chunk_index');

            $sample = $this->spreadSample($base->pluck('id')->all(), $limit * self::CANDIDATE_MULTIPLIER);

            if ($sample) {
                $order = array_flip(array_values($sample));

                // Order in PHP: FIELD() is MySQL-only (sqlite tests lack it),
                // and the id list is always small enough to sort in memory.
                $candidates = BookChunk::query()
                    ->whereIn('id', array_values($sample))
                    ->get(['id', 'chapter_id', 'page_id', 'page_number', 'content', 'metadata', 'embedding', 'element_ids'])
                    ->sortBy(fn (BookChunk $chunk) => $order[$chunk->id] ?? PHP_INT_MAX)
                    ->values();
            }
        }

        if ($candidates->isEmpty()) {
            return [];
        }

        return $this->rank($candidates, $question, $limit);
    }

    /**
     * Coverage-oriented retrieval for generated exams. Unlike relevantChunks,
     * this samples evenly from each selected or accessible chapter rather
     * than ranking toward a query, so a book-level exam covers the whole book.
     *
     * @return array<int, array{chunk_id: int, chapter_id: ?int, chapter: ?string, page: ?int, content: string}>
     */
    public function coverageChunks(Book $book, ?Student $student = null, array $selectedChapterIds = [], int $limit = 48): array
    {
        $accessibleChapterIds = $this->access->accessibleChapterIds($student, $book);

        if ($accessibleChapterIds === null || $limit < 1) {
            return [];
        }

        $selectedChapterIds = array_values(array_unique(array_map('intval', $selectedChapterIds)));
        $hasSelection = $selectedChapterIds !== [];

        if ($hasSelection && array_diff($selectedChapterIds, $accessibleChapterIds)) {
            return [];
        }

        $chapters = $book->chapters()->get(['id', 'parent_id', 'title', 'sort_order'])->keyBy('id');

        if ($hasSelection) {
            // Selecting a chapter includes its accessible descendant sections.
            $chapterIds = $selectedChapterIds;
            do {
                $children = $chapters
                    ->filter(fn ($chapter) => $chapter->parent_id !== null && in_array((int) $chapter->parent_id, $chapterIds, true))
                    ->pluck('id')
                    ->all();
                $newIds = array_diff($children, $chapterIds);
                $chapterIds = array_merge($chapterIds, $newIds);
            } while ($newIds);

            $chapterIds = array_values(array_intersect($chapterIds, $accessibleChapterIds));
            if (! $chapterIds) {
                return [];
            }
        } else {
            $chapterIds = $accessibleChapterIds;
        }

        // Keep every chapter/null-chapter alternative inside the book-scoped
        // relationship query so an OR can never escape the requested book.
        $includeLegacyChunks = ! $hasSelection && $chapterIds && $this->access->hasAccess($student, $book);
        $query = $book->chunks()->where(function ($q) use ($chapterIds, $includeLegacyChunks) {
            if ($chapterIds) {
                $q->whereIn('chapter_id', $chapterIds);
                if ($includeLegacyChunks) {
                    $q->orWhereNull('chapter_id');
                }
            } else {
                $q->whereNull('chapter_id');
            }
        });

        $groups = $query->orderBy('chunk_index')->get(['id', 'chapter_id', 'page_number', 'chunk_index'])
            ->groupBy(fn (BookChunk $chunk) => $chunk->chapter_id ?? 0)
            ->map(fn ($chunks) => $chunks->pluck('id')->all())
            ->all();

        if (! $groups) {
            return [];
        }

        $groupKeys = array_keys($groups);
        $quotas = array_fill_keys($groupKeys, 0);
        $remaining = min($limit, array_sum(array_map('count', $groups)));

        while ($remaining > 0) {
            $madeProgress = false;
            foreach ($groupKeys as $key) {
                if ($quotas[$key] >= count($groups[$key]) || $remaining === 0) {
                    continue;
                }
                $quotas[$key]++;
                $remaining--;
                $madeProgress = true;
            }
            if (! $madeProgress) {
                break;
            }
        }

        $sampledIds = [];
        foreach ($groups as $key => $ids) {
            $sampledIds = array_merge($sampledIds, $this->spreadSample($ids, $quotas[$key]));
        }

        $chunks = $book->chunks()->whereIn('id', $sampledIds)
            ->where(function ($q) use ($chapterIds, $includeLegacyChunks) {
                if ($chapterIds) {
                    $q->whereIn('chapter_id', $chapterIds);
                    if ($includeLegacyChunks) {
                        $q->orWhereNull('chapter_id');
                    }
                } else {
                    $q->whereNull('chapter_id');
                }
            })
            ->get(['id', 'chapter_id', 'page_number', 'chunk_index', 'content'])
            ->sortBy(fn (BookChunk $chunk) => [$chunk->chunk_index, $chunk->id])
            ->values();

        return $chunks->map(function (BookChunk $chunk) use ($chapters) {
            $trail = [];
            $node = $chunk->chapter_id ? ($chapters[$chunk->chapter_id] ?? null) : null;
            while ($node) {
                array_unshift($trail, $node->title);
                $node = $node->parent_id ? ($chapters[$node->parent_id] ?? null) : null;
            }

            return [
                'chunk_id' => (int) $chunk->id,
                'chapter_id' => $chunk->chapter_id ? (int) $chunk->chapter_id : null,
                'chapter' => $trail ? implode(' / ', $trail) : null,
                'page' => $chunk->page_number,
                'content' => $chunk->content,
            ];
        })->all();
    }

    /**
     * Structured RAG context for the LLM: chunk text plus provenance plus
     * related tables/images/formulas (only ones from accessible chapters).
     *
     * @return array<int, array{
     *     book: string, chapter: ?string, section: ?string, page: ?int,
     *     content: string, element_ids: ?array, tables: array, images: array, formulas: array
     * }>
     */
    public function buildContext(Book $book, string $question, ?Student $student = null, int $limit = 5): array
    {
        $chunks = $this->relevantChunks($book, $question, $student, $limit);

        if (! $chunks) {
            // Empty context = chat degrades to "no info", which reads as a
            // bug to users — log WHY so operators can tell apart "book not
            // processed yet" from a retrieval failure.
            Log::info('AiChat buildContext returned no chunks', [
                'book_id' => $book->id,
                'title' => $book->title,
                'chunks' => $book->chunks()->count(),
                'chapters' => $book->chapters()->count(),
                'processing_status' => $book->processing_status,
                'paginate' => [
                    'student' => $student?->id,
                    'accessible' => $this->access->accessibleChapterIds($student, $book),
                ],
            ]);

            return [];
        }

        $chapterIds = array_values(array_unique(array_filter(array_column($chunks, 'chapter_id'))));
        $pageIds = array_values(array_unique(array_filter(array_column($chunks, 'page_id'))));

        $tables = $this->relatedTables($book, $chapterIds, $pageIds);
        $images = $this->relatedImages($book, $chapterIds, $pageIds);
        $formulas = $this->relatedFormulas($book, $chapterIds, $pageIds);

        $chapterTitles = $book->chapters()->get(['id', 'parent_id', 'title', 'level'])->keyBy('id');

        $context = [];

        foreach ($chunks as $chunk) {
            $trail = [];
            $node = $chunk['chapter_id'] ? ($chapterTitles[$chunk['chapter_id']] ?? null) : null;
            while ($node) {
                array_unshift($trail, $node->title);
                $node = $node->parent_id ? ($chapterTitles[$node->parent_id] ?? null) : null;
            }

            $context[] = [
                'book' => $book->title,
                'chapter' => $trail[0] ?? null,
                'section' => count($trail) > 1 ? implode(' / ', array_slice($trail, 1)) : (($chunk['metadata']['heading_path'] ?? []) ? implode(' / ', $chunk['metadata']['heading_path']) : null),
                'page' => $chunk['page_number'],
                'content' => $chunk['content'],
                'element_ids' => $chunk['element_ids'] ?? null,
                'tables' => $tables,
                'images' => $images,
                'formulas' => $formulas,
            ];
        }

        return $context;
    }

    /**
     * @return Collection<int, BookChunk>
     */
    private function fulltextCandidates(Book $book, string $question, array $accessibleChapterIds, int $limit)
    {
        try {
            return BookChunk::query()
                ->where('book_id', $book->id)
                ->when($accessibleChapterIds, fn ($q) => $q->whereIn('chapter_id', $accessibleChapterIds))
                ->when(! $accessibleChapterIds, fn ($q) => $q->whereNull('chapter_id'))
                ->whereRaw('MATCH(content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$question])
                ->limit($limit)
                ->get(['id', 'chapter_id', 'page_id', 'page_number', 'content', 'metadata', 'embedding', 'element_ids']);
        } catch (\Throwable $e) {
            // FULLTEXT is MySQL-only (sqlite test DBs lack it) and can also
            // misbehave on corrupted indexes — degrade to the ordering
            // fallback below rather than failing the chat.
            return collect();
        }
    }

    /**
     * Hybrid ranking: cosine similarity when embeddings exist on both
     * sides, FULLTEXT-order as the floor for legacy chunks.
     *
     * @return array<int, array{content: string, chapter_id: ?int, page_id: ?int, page_number: ?int, metadata: ?array, element_ids: ?array, score: float}>
     */
    private function rank($candidates, string $question, int $limit): array
    {
        $queryVector = null;

        try {
            $queryVector = $this->embeddings->embedQuery($question);
        } catch (\Throwable $e) {
            // Embeddings are an enhancement, never a dependency — chat
            // must keep working when no embedding provider is configured.
            Log::info('Query embedding unavailable, falling back to FULLTEXT order.', ['error' => $e->getMessage()]);
        }

        $scored = $candidates->map(function (BookChunk $chunk, $i) use ($queryVector) {
            $score = 1.0 - ($i * 0.01); // FULLTEXT relevance order preserved as weak prior

            if ($queryVector !== null && ! empty($chunk->embedding)) {
                $score = $this->embeddings->cosineSimilarity($queryVector, $chunk->embedding);
            }

            return [
                'content' => $chunk->content,
                'chapter_id' => $chunk->chapter_id,
                'page_id' => $chunk->page_id,
                'page_number' => $chunk->page_number,
                'metadata' => $chunk->metadata,
                'element_ids' => $chunk->element_ids,
                'score' => $score,
            ];
        });

        return $scored->sortByDesc('score')->take($limit)->values()->all();
    }

    /**
     * Pick `want` chunks from an ordered id list with the widest possible
     * coverage: always the first, always the last, and evenly spaced in
     * between (deduplicated, reading order preserved).
     *
     * @param  array<int, int>  $orderedIds
     * @return array<int, int>
     */
    private function spreadSample(array $orderedIds, int $want): array
    {
        $count = count($orderedIds);

        if ($count === 0) {
            return [];
        }

        if ($count <= $want) {
            return $orderedIds;
        }

        $picked = [];

        for ($i = 0; $i < $want; $i++) {
            $picked[] = $orderedIds[(int) round($i * ($count - 1) / max($want - 1, 1))];
        }

        return array_values(array_unique($picked));
    }

    private function relatedTables(Book $book, array $chapterIds, array $pageIds): array
    {
        if (! $chapterIds && ! $pageIds) {
            return [];
        }

        return $book->tables()
            ->where(fn ($q) => $q
                ->whereIn('chapter_id', $chapterIds)
                ->orWhereIn('page_id', $pageIds))
            ->limit(3)
            ->get(['title', 'content', 'markdown', 'page_id'])
            ->map(fn ($t) => [
                'title' => $t->title,
                'structured' => $t->content,
                'markdown' => $t->markdown,
            ])
            ->all();
    }

    private function relatedImages(Book $book, array $chapterIds, array $pageIds): array
    {
        if (! $chapterIds && ! $pageIds) {
            return [];
        }

        // Descriptions only — never send binary or public URLs to the LLM.
        return $book->images()
            ->where(fn ($q) => $q
                ->whereIn('chapter_id', $chapterIds)
                ->orWhereIn('page_id', $pageIds))
            ->whereNotNull('description')
            ->limit(3)
            ->get(['alt_text', 'description', 'page_id'])
            ->map(fn ($i) => [
                'alt_text' => $i->alt_text,
                'description' => $i->description,
            ])
            ->all();
    }

    private function relatedFormulas(Book $book, array $chapterIds, array $pageIds): array
    {
        if (! $chapterIds && ! $pageIds) {
            return [];
        }

        return $book->formulas()
            ->where(fn ($q) => $q
                ->whereIn('chapter_id', $chapterIds)
                ->orWhereIn('page_id', $pageIds))
            ->limit(5)
            ->get(['latex', 'content', 'page_id'])
            ->map(fn ($f) => [
                'latex' => $f->latex,
                'content' => $f->content,
            ])
            ->all();
    }
}
