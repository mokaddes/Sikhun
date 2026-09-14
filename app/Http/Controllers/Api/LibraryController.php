<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Book;
use App\Models\BookChapter;
use App\Services\BookAccessService;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $books = Book::published()
            ->with(['author:id,name', 'category:id,name'])
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->when($request->subject, fn ($q) => $q->where('subject', $request->subject))
            ->when($request->free, fn ($q) => $q->where('is_free', true))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return $this->success($books);
    }

    public function show(Book $book, BookAccessService $access): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $book->load(['author', 'publication', 'category']);
        $data = $book->toArray();
        $data['access_type'] = $access->accessType(auth('sanctum')->user(), $book);
        $data['has_access'] = $access->hasAccess(auth('sanctum')->user(), $book);

        return $this->success($data);
    }

    public function purchase(PurchaseRequest $request, Book $book, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        try {
            $result = $purchases->purchaseBook(
                auth('sanctum')->user(),
                $book,
                $request->payment_method,
                $request->payment_method === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        return $this->success($result, $result['redirect_url'] ? 'Redirect to complete payment' : 'Purchased successfully');
    }

    /**
     * Chapter list for a book with per-chapter access state for the
     * caller — powers the chapter purchase UI. Only structural metadata
     * (titles, page ranges, price); no content.
     */
    public function chapters(Book $book, BookAccessService $access): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $student = auth('sanctum')->user();
        $hasFullAccess = $access->hasAccess($student, $book);
        // Transitive set (chapter + descendants) — sections of an owned
        // chapter are readable and must not display as locked.
        $owned = $hasFullAccess ? null : ($access->accessibleChapterIds($student, $book) ?? []);

        $chapters = $book->chapters()
            ->orderBy('sort_order')
            ->get(['id', 'parent_id', 'chapter_number', 'title', 'level', 'start_page', 'end_page', 'price'])
            ->map(function ($chapter) use ($owned, $hasFullAccess, $book) {
                return [
                    'id' => $chapter->id,
                    'parent_id' => $chapter->parent_id,
                    'chapter_number' => $chapter->chapter_number,
                    'title' => $chapter->title,
                    'level' => $chapter->level,
                    'start_page' => $chapter->start_page,
                    'end_page' => $chapter->end_page,
                    'price' => $chapter->price,
                    'purchasable' => $book->chapter_purchase_enabled && $chapter->price !== null && $chapter->level === 1,
                    'owned' => $hasFullAccess || in_array($chapter->id, $owned),
                ];
            });

        return $this->success([
            'chapter_purchase_enabled' => $book->chapter_purchase_enabled,
            'has_full_access' => $hasFullAccess,
            'chapters' => $chapters,
        ]);
    }

    /** Access summary for the caller on one book (full + chapter-level). */
    public function access(Book $book, BookAccessService $access): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $student = auth('sanctum')->user();

        return $this->success([
            'access_type' => $access->accessType($student, $book),
            'has_access' => $access->hasAccess($student, $book),
            'chapter_purchase_enabled' => $book->chapter_purchase_enabled,
            'accessible_chapter_ids' => $access->accessibleChapterIds($student, $book),
        ]);
    }

    public function purchaseChapter(Request $request, Book $book, BookChapter $chapter, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        abort_unless($chapter->book_id === $book->id, 404);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet,zinipay'],
        ]);

        try {
            $result = $purchases->purchaseChapter(
                auth('sanctum')->user(),
                $chapter,
                $validated['payment_method'],
                $validated['payment_method'] === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        return $this->success($result, $result['redirect_url'] ? 'Redirect to complete payment' : 'Chapter purchased');
    }
}
