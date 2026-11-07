<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookChunk;
use App\Models\BookPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookChapterController extends Controller
{
    public function store(Request $request, Book $book): RedirectResponse
    {
        $rawChapterNum = $request->input('chapter_number');
        $parsedNum = ($rawChapterNum !== null && $rawChapterNum !== '')
            ? BookChapter::parseNumber($rawChapterNum)
            : null;

        if ($parsedNum !== null) {
            $request->merge(['chapter_number' => $parsedNum]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'chapter_number' => ['nullable', 'integer', 'min:1'],
            'start_page' => ['required', 'integer', 'min:1'],
            'end_page' => ['required', 'integer', 'gte:start_page'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($book, $validated) {
            $chapterNumber = $validated['chapter_number'] ?? null;
            if ($chapterNumber === null) {
                // Auto-determine next chapter number based on existing chapters
                $existing = $book->chapters()->get();
                $maxNum = 0;
                foreach ($existing as $c) {
                    $n = BookChapter::parseNumber($c->chapter_number);
                    if ($n && $n > $maxNum) {
                        $maxNum = $n;
                    }
                }
                $chapterNumber = $maxNum > 0 ? $maxNum + 1 : ($existing->count() + 1);
            }

            $baseSlug = Str::slug($validated['title']) ?: 'chapter-' . ($chapterNumber ?? $validated['start_page']);
            $slug = $baseSlug;
            $counter = 1;
            while ($book->chapters()->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $chapter = $book->chapters()->create([
                'title' => $validated['title'],
                'chapter_number' => (string) $chapterNumber,
                'slug' => $slug,
                'level' => 1,
                'start_page' => $validated['start_page'],
                'end_page' => $validated['end_page'],
                'price' => $validated['price'] ?? null,
                'sort_order' => $validated['start_page'],
            ]);

            // Synchronize book_pages
            BookPage::where('book_id', $book->id)
                ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                ->update(['chapter_id' => $chapter->id]);

            // Synchronize book_chunks
            BookChunk::where('book_id', $book->id)
                ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                ->update(['chapter_id' => $chapter->id]);

            // Resequence all chapters in chronological order (1, 2, 3...)
            BookChapter::resequenceForBook($book->id);
        });

        return back()->with('success', 'Chapter created and pages synchronized successfully.');
    }

    public function update(Request $request, Book $book, BookChapter $chapter): RedirectResponse
    {
        abort_unless($chapter->book_id === $book->id, 404);

        $rawChapterNum = $request->input('chapter_number');
        $parsedNum = ($rawChapterNum !== null && $rawChapterNum !== '')
            ? BookChapter::parseNumber($rawChapterNum)
            : null;

        if ($parsedNum !== null) {
            $request->merge(['chapter_number' => $parsedNum]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'chapter_number' => ['nullable', 'integer', 'min:1'],
            'start_page' => ['required', 'integer', 'min:1'],
            'end_page' => ['required', 'integer', 'gte:start_page'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($book, $chapter, $validated) {
            $chapterNumber = isset($validated['chapter_number']) && $validated['chapter_number'] !== null
                ? (string) $validated['chapter_number']
                : $chapter->chapter_number;

            $chapter->update([
                'title' => $validated['title'],
                'chapter_number' => $chapterNumber,
                'start_page' => $validated['start_page'],
                'end_page' => $validated['end_page'],
                'price' => $validated['price'] ?? null,
                'sort_order' => $validated['start_page'],
            ]);

            // Re-assign pages within the new range
            BookPage::where('book_id', $book->id)
                ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                ->update(['chapter_id' => $chapter->id]);

            // Re-assign chunks
            BookChunk::where('book_id', $book->id)
                ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                ->update(['chapter_id' => $chapter->id]);

            // Resequence all chapters in chronological order (1, 2, 3...)
            BookChapter::resequenceForBook($book->id);
        });

        return back()->with('success', 'Chapter updated and pages synchronized successfully.');
    }

    public function destroy(Book $book, BookChapter $chapter): RedirectResponse
    {
        abort_unless($chapter->book_id === $book->id, 404);

        DB::transaction(function () use ($book, $chapter) {
            // Unlink pages & chunks
            BookPage::where('book_id', $book->id)
                ->where('chapter_id', $chapter->id)
                ->update(['chapter_id' => null]);

            BookChunk::where('book_id', $book->id)
                ->where('chapter_id', $chapter->id)
                ->update(['chapter_id' => null]);

            $chapter->delete();

            // Resequence remaining chapters
            BookChapter::resequenceForBook($book->id);
        });

        return back()->with('success', 'Chapter deleted successfully.');
    }

    /**
     * Batch sync all chapters for a book (divide book into chapters in one click).
     */
    public function sync(Request $request, Book $book): RedirectResponse
    {
        $rawChapters = $request->input('chapters', []);
        if (is_array($rawChapters)) {
            foreach ($rawChapters as $idx => $raw) {
                if (isset($raw['chapter_number']) && $raw['chapter_number'] !== null && $raw['chapter_number'] !== '') {
                    $rawChapters[$idx]['chapter_number'] = BookChapter::parseNumber($raw['chapter_number']);
                }
            }
            $request->merge(['chapters' => $rawChapters]);
        }

        $validated = $request->validate([
            'chapters' => ['required', 'array'],
            'chapters.*.id' => ['nullable', 'integer'],
            'chapters.*.title' => ['required', 'string', 'max:255'],
            'chapters.*.chapter_number' => ['nullable', 'integer'],
            'chapters.*.start_page' => ['required', 'integer', 'min:1'],
            'chapters.*.end_page' => ['required', 'integer', 'gte:chapters.*.start_page'],
            'chapters.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($book, $validated) {
            $submittedIds = [];

            foreach ($validated['chapters'] as $index => $item) {
                $chapter = null;
                if (! empty($item['id'])) {
                    $chapter = $book->chapters()->find($item['id']);
                }

                $chapterNum = isset($item['chapter_number']) && $item['chapter_number'] !== null
                    ? (string) $item['chapter_number']
                    : (string) ($index + 1);

                $baseSlug = Str::slug($item['title']) ?: 'chapter-' . $chapterNum;
                $slug = $baseSlug;

                if (! $chapter) {
                    $counter = 1;
                    while ($book->chapters()->where('slug', $slug)->exists()) {
                        $slug = "{$baseSlug}-{$counter}";
                        $counter++;
                    }

                    $chapter = $book->chapters()->create([
                        'title' => $item['title'],
                        'chapter_number' => $chapterNum,
                        'slug' => $slug,
                        'level' => 1,
                        'start_page' => $item['start_page'],
                        'end_page' => $item['end_page'],
                        'price' => $item['price'] ?? null,
                        'sort_order' => $item['start_page'],
                    ]);
                } else {
                    $chapter->update([
                        'title' => $item['title'],
                        'chapter_number' => $chapterNum,
                        'start_page' => $item['start_page'],
                        'end_page' => $item['end_page'],
                        'price' => $item['price'] ?? null,
                        'sort_order' => $item['start_page'],
                    ]);
                }

                $submittedIds[] = $chapter->id;

                // Sync pages for this chapter
                BookPage::where('book_id', $book->id)
                    ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                    ->update(['chapter_id' => $chapter->id]);

                // Sync chunks for this chapter
                BookChunk::where('book_id', $book->id)
                    ->whereBetween('page_number', [$chapter->start_page, $chapter->end_page])
                    ->update(['chapter_id' => $chapter->id]);
            }

            // Remove chapters that were omitted from the sync list
            if (! empty($submittedIds)) {
                $removedChapters = $book->chapters()->whereNotIn('id', $submittedIds)->get();
                foreach ($removedChapters as $rm) {
                    BookPage::where('book_id', $book->id)
                        ->where('chapter_id', $rm->id)
                        ->update(['chapter_id' => null]);

                    BookChunk::where('book_id', $book->id)
                        ->where('chapter_id', $rm->id)
                        ->update(['chapter_id' => null]);

                    $rm->delete();
                }
            }

            // Resequence all chapters
            BookChapter::resequenceForBook($book->id);
        });

        return back()->with('success', count($validated['chapters']) . ' chapters synchronized successfully.');
    }
}
