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
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'chapter_number' => ['nullable', 'integer', 'min:1'],
            'start_page' => ['required', 'integer', 'min:1'],
            'end_page' => ['required', 'integer', 'gte:start_page'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($book, $validated) {
            $baseSlug = Str::slug($validated['title']) ?: 'chapter-' . ($validated['chapter_number'] ?? $validated['start_page']);
            $slug = $baseSlug;
            $counter = 1;
            while ($book->chapters()->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $chapter = $book->chapters()->create([
                'title' => $validated['title'],
                'chapter_number' => $validated['chapter_number'] ?? ($book->chapters()->count() + 1),
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
        });

        return back()->with('success', 'Chapter created and pages synchronized successfully.');
    }

    public function update(Request $request, Book $book, BookChapter $chapter): RedirectResponse
    {
        abort_unless($chapter->book_id === $book->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'chapter_number' => ['nullable', 'integer', 'min:1'],
            'start_page' => ['required', 'integer', 'min:1'],
            'end_page' => ['required', 'integer', 'gte:start_page'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($book, $chapter, $validated) {
            $chapter->update([
                'title' => $validated['title'],
                'chapter_number' => $validated['chapter_number'] ?? $chapter->chapter_number,
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
        });

        return back()->with('success', 'Chapter deleted successfully.');
    }

    /**
     * Batch sync all chapters for a book (divide book into chapters in one click).
     */
    public function sync(Request $request, Book $book): RedirectResponse
    {
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

                $baseSlug = Str::slug($item['title']) ?: 'chapter-' . ($item['chapter_number'] ?? ($index + 1));
                $slug = $baseSlug;

                if (! $chapter) {
                    $counter = 1;
                    while ($book->chapters()->where('slug', $slug)->exists()) {
                        $slug = "{$baseSlug}-{$counter}";
                        $counter++;
                    }

                    $chapter = $book->chapters()->create([
                        'title' => $item['title'],
                        'chapter_number' => $item['chapter_number'] ?? ($index + 1),
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
                        'chapter_number' => $item['chapter_number'] ?? ($index + 1),
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
        });

        return back()->with('success', count($validated['chapters']) . ' chapters synchronized successfully.');
    }
}
