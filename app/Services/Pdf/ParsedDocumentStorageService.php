<?php

namespace App\Services\Pdf;

use App\Contracts\ParsedDocument;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookElement;
use App\Models\BookFormula;
use App\Models\BookImage;
use App\Models\BookPage;
use App\Models\BookTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Persists a ParsedDocument into the structured tables (chapters → pages
 * → elements → tables/images/formulas) atomically. The whole replace runs
 * inside ONE transaction so a half-written reprocess can never coexist
 * with the old (stale) RAG data.
 */
class ParsedDocumentStorageService
{
    public function __construct(private ImageExtractionService $images) {}

    /**
     * @return array{pages: int, chapters: int, elements: int, tables: int, images: int, formulas: int}
     */
    public function replaceAll(Book $book, ParsedDocument $doc): array
    {
        return DB::transaction(function () use ($book, $doc) {
            // Purge extracted image binaries BEFORE the teardown: old rows
            // are deleted below and new files get fresh random names, so
            // skipping this would orphan the old files on disk forever.
            // This also cleans up files left behind by a rolled-back run.
            $this->images->purgeBookImages($book);

            // Chunks are rebuilt by the chunking stage AFTER this commit;
            // delete them here so no window exists where old chunks are
            // searchable alongside new structure.
            $book->chunks()->delete();

            // FK-safe teardown of previous parse.
            BookTable::where('book_id', $book->id)->delete();
            BookFormula::where('book_id', $book->id)->delete();
            BookImage::where('book_id', $book->id)->delete();
            BookElement::where('book_id', $book->id)->delete();
            BookPage::where('book_id', $book->id)->delete();
            BookChapter::where('book_id', $book->id)->delete();

            $chapterIds = $this->storeChapters($book, $doc);
            $pageIds = $this->storePages($book, $doc, $chapterIds);
            $elementIds = $this->storeElements($book, $doc, $chapterIds, $pageIds);

            $tableCount = $this->storeTables($book, $doc, $chapterIds, $pageIds, $elementIds);
            $imageCount = $this->storeImages($book, $doc, $chapterIds, $pageIds, $elementIds);
            $formulaCount = $this->storeFormulas($book, $doc, $chapterIds, $pageIds, $elementIds);

            return [
                'pages' => count($doc->pages),
                'chapters' => count($chapterIds),
                'elements' => count($elementIds),
                'tables' => $tableCount,
                'images' => $imageCount,
                'formulas' => $formulaCount,
            ];
        });
    }

    /** @return array<int, int> flat index → BookChapter id */
    private function storeChapters(Book $book, ParsedDocument $doc): array
    {
        // Pass 1: create all rows with parent unresolved.
        $ids = [];
        $rows = [];

        foreach ($doc->chapters as $i => $chapter) {
            $row = $book->chapters()->create([
                'parent_id' => null,
                'chapter_number' => $chapter['chapter_number'],
                'title' => $chapter['title'],
                'slug' => $this->uniqueSlug($chapter['title']),
                'level' => $chapter['level'] ?: 1,
                'start_page' => $chapter['start_page'],
                'end_page' => $chapter['end_page'],
                'sort_order' => $i,
                'metadata' => $chapter['metadata'],
            ]);
            $ids[$i] = $row->id;
            $rows[$i] = $row;
        }

        // Pass 2: resolve parent links + page ranges.
        foreach ($doc->chapters as $i => $chapter) {
            if ($chapter['parent_index'] !== null && isset($ids[$chapter['parent_index']])) {
                $rows[$i]->update(['parent_id' => $ids[$chapter['parent_index']]]);
            }
        }

        $this->backfillChapterPageRanges($rows);

        return $ids;
    }

    /**
     * Chapters without an explicit end_page get the page before the next
     * chapter (at any depth) starts — good enough for reader deep-links.
     * @param array<int, BookChapter> $rows
     */
    private function backfillChapterPageRanges(array $rows): void
    {
        $withStart = [];

        foreach ($rows as $i => $row) {
            if ($row->start_page !== null) {
                $withStart[$i] = $row->start_page;
            }
        }

        foreach ($rows as $i => $row) {
            if ($row->end_page === null && $row->start_page !== null) {
                $next = null;

                foreach ($withStart as $j => $startPage) {
                    if ($j > $i && $startPage > $row->start_page && ($next === null || $startPage < $next)) {
                        $next = $startPage;
                    }
                }

                $row->update(['end_page' => $next !== null ? $next - 1 : null]);
            }
        }
    }

    /** @return array<int, int> page number → BookPage id */
    private function storePages(Book $book, ParsedDocument $doc, array $chapterIds): array
    {
        $ids = [];

        foreach ($doc->pages as $page) {
            $row = $book->pages()->create([
                'page_number' => $page['page_number'],
                'chapter_id' => $this->chapterForPage($doc, $page['page_number'], $chapterIds),
                'content' => $page['content'],
                'metadata' => $page['metadata'] ?? null,
            ]);
            $ids[$page['page_number']] = $row->id;
        }

        if ($doc->pages) {
            // Authoritative: after a successful parse with a real page list,
            // total_pages always equals this PDF's page count. Using max() here
            // would leave the count stuck at a larger value from an older file
            // (e.g. replacing a 100-page PDF with a corrected 4-page one).
            $book->update(['total_pages' => count($doc->pages)]);
        }

        return $ids;
    }

    /**
     * The DEEPEST chapter whose page range covers this page — so a page in
     * section 2.1 maps to the 2.1 chapter row, not its "Chapter 2" parent.
     */
    private function chapterForPage(ParsedDocument $doc, int $pageNumber, array $chapterIds): ?int
    {
        $best = null;
        $bestLevel = 0;

        foreach ($doc->chapters as $i => $chapter) {
            $start = $chapter['start_page'];
            $end = $chapter['end_page'];

            if ($start === null || $pageNumber < $start || ($end !== null && $pageNumber > $end)) {
                continue;
            }

            if (($chapter['level'] ?: 1) >= $bestLevel) {
                $best = $chapterIds[$i] ?? null;
                $bestLevel = $chapter['level'] ?: 1;
            }
        }

        return $best;
    }

    /** @return array<int, int> flat element index → BookElement id */
    private function storeElements(Book $book, ParsedDocument $doc, array $chapterIds, array $pageIds): array
    {
        $ids = [];
        $sort = 0;
        $headingStack = []; // [level => title] trail for heading_path metadata

        foreach ($doc->elements as $i => $element) {
            $pageNumber = $element['page_number'];
            $chapterId = $this->chapterForPage($doc, $pageNumber, $chapterIds);

            if (($element['type'] ?? '') === 'heading' && $element['content'] !== null) {
                $headingStack = $this->pushHeading($headingStack, $element['content']);
            }

            $row = $book->elements()->create([
                'chapter_id' => $chapterId,
                'page_id' => $pageIds[$pageNumber] ?? null,
                'type' => $element['type'],
                'content' => $element['content'],
                'metadata' => array_merge(
                    $element['metadata'] ?? [],
                    ['heading_path' => array_values($headingStack)],
                ),
                'bbox' => $element['bbox'] ?? null,
                'page_number' => $pageNumber,
                'sort_order' => $sort++,
            ]);
            $ids[$i] = $row->id;
        }

        return $ids;
    }

    private function pushHeading(array $stack, string $title): array
    {
        // Heading levels within the stack are monotonic by page position;
        // a "1 Introduction" resets everything, "2.1" keeps "Chapter 2".
        if (preg_match('/^(\d+(?:\.\d+){0,3})[\s.]/', $title, $m)) {
            $depth = substr_count($m[1], '.') + 1;
        } else {
            $depth = 1;
        }

        $stack = array_slice($stack, 0, $depth - 1, true);
        $stack[$depth] = $title;

        return $stack;
    }

    private function storeTables(Book $book, ParsedDocument $doc, array $chapterIds, array $pageIds, array $elementIds): int
    {
        $count = 0;

        foreach ($doc->tables as $table) {
            $pageNumber = $table['page_number'];

            $book->tables()->create([
                'chapter_id' => $this->chapterForPage($doc, $pageNumber, $chapterIds),
                'page_id' => $pageIds[$pageNumber] ?? null,
                'element_id' => $table['element_index'] !== null ? ($elementIds[$table['element_index']] ?? null) : null,
                'title' => $table['title'],
                'content' => $table['content'],
                'markdown' => $table['markdown'] ?? $this->markdownFromStructured($table['content']),
                'html' => $table['html'],
                'metadata' => $table['metadata'] ?? null,
            ]);
            $count++;
        }

        return $count;
    }

    private function storeImages(Book $book, ParsedDocument $doc, array $chapterIds, array $pageIds, array $elementIds): int
    {
        $count = 0;

        foreach ($doc->images as $image) {
            $pageNumber = $image['page_number'];
            $path = isset($image['binary']) && $image['binary'] !== null
                ? $this->images->storeExtractedImage($book, $image['binary'], $image['extension'] ?? 'png', $pageNumber)
                : null;

            $book->images()->create([
                'chapter_id' => $this->chapterForPage($doc, $pageNumber, $chapterIds),
                'page_id' => $pageIds[$pageNumber] ?? null,
                'element_id' => $image['element_index'] !== null ? ($elementIds[$image['element_index']] ?? null) : null,
                'path' => $path,
                'alt_text' => $image['alt_text'] ?? null,
                'description' => $image['description'] ?? null,
                'ocr_text' => $image['ocr_text'] ?? null,
                'bbox' => $image['bbox'] ?? null,
                'metadata' => $image['metadata'] ?? null,
            ]);
            $count++;
        }

        return $count;
    }

    private function storeFormulas(Book $book, ParsedDocument $doc, array $chapterIds, array $pageIds, array $elementIds): int
    {
        $count = 0;

        foreach ($doc->formulas as $formula) {
            $pageNumber = $formula['page_number'];

            $book->formulas()->create([
                'chapter_id' => $this->chapterForPage($doc, $pageNumber, $chapterIds),
                'page_id' => $pageIds[$pageNumber] ?? null,
                'element_id' => $formula['element_index'] !== null ? ($elementIds[$formula['element_index']] ?? null) : null,
                'latex' => $formula['latex'],
                'content' => $formula['content'],
                'bbox' => $formula['bbox'] ?? null,
                'metadata' => $formula['metadata'] ?? null,
            ]);
            $count++;
        }

        return $count;
    }

    private function markdownFromStructured(?array $content): ?string
    {
        if (! $content || empty($content['headers']) || empty($content['rows'])) {
            return null;
        }

        $lines = ['| '.implode(' | ', $content['headers']).' |'];
        $lines[] = '|'.str_repeat(' --- |', count($content['headers']));

        foreach ($content['rows'] as $row) {
            $lines[] = '| '.implode(' | ', array_map(fn ($c) => (string) $c, $row)).' |';
        }

        return implode("\n", $lines);
    }

    private function uniqueSlug(string $title): string
    {
        return Str::slug(Str::limit($title, 80)) ?: 'chapter';
    }
}
