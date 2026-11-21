<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Student;
use App\Services\AccessGrantService;

/**
 * Single source of truth for "can this student read this book" — checked
 * in this exact priority order (REQ-LIB-06):
 *   1. Book is globally free
 *   2. Live free campaign or coupon grants full access (no package needed)
 *   3. Already on the student's bookshelf (bought, gifted, or free-added)
 *   4. Active subscription's gift list includes this book
 *   5. Otherwise: purchasable, gated on wallet balance
 *
 * Chapter-level rules are layered on top:
 *   - Full book access ⇒ every chapter
 *   - A chapter purchase unlocks that chapter (+ its descendant sections)
 *   - Grants (campaign/coupon) unlock every chapter too — they grant
 *     "full free access" by definition
 */
class BookAccessService
{
    public function __construct(private AccessGrantService $grants) {}

    public function isBookPaid(?Book $book): bool
    {
        if (! $book) {
            return false;
        }

        return ! $book->is_free && $book->price !== null && (float) $book->price > 0;
    }

    public function hasPricedChapters(?Book $book): bool
    {
        if (! $book || ! $book->chapter_purchase_enabled) {
            return false;
        }

        return $book->chapters()
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->exists();
    }

    public function hasAccess(?Student $student, ?Book $book): bool
    {
        if (! $book) {
            return false;
        }

        if (! $student) {
            return (! $this->isBookPaid($book)) && ! $this->hasPricedChapters($book);
        }

        // 1. Active site-wide access grant (campaign/coupon)
        if ($this->grants->hasActiveAccess($student)) {
            return true;
        }

        // 2. Subscription gift containing this book
        $subscription = $student->activeSubscription;
        if ($subscription && $subscription->expires_at->isFuture()
            && in_array($book->id, $subscription->plan->gift_book_ids ?? [], true)) {
            return true;
        }

        // 3. Completed book purchase with real payment (amount > 0)
        $hasPaidBookOrder = $student->orders()
            ->where('orderable_type', 'book')
            ->where('orderable_id', $book->id)
            ->where('status', 'completed')
            ->where('amount', '>', 0)
            ->exists();

        if ($hasPaidBookOrder) {
            return true;
        }

        // 4. Bookshelf record with source indicating purchase/gift/subscription
        $hasPurchasedShelf = $student->bookShelf()
            ->where('book_id', $book->id)
            ->whereIn('source', ['purchased', 'paid', 'gift', 'subscription'])
            ->exists();

        if ($hasPurchasedShelf) {
            return true;
        }

        // 5. If chapter purchase is enabled and book has priced chapters, check if student owns ALL priced chapters
        if ($this->hasPricedChapters($book)) {
            $unownedPricedCount = $book->chapters()
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->whereNotIn('id', function ($q) use ($student, $book) {
                    $q->select('chapter_id')
                        ->from('student_book_chapters')
                        ->where('student_id', $student->id)
                        ->where('book_id', $book->id);
                })
                ->count();

            return $unownedPricedCount === 0;
        }

        // 6. If book has price and is not owned, no full access
        if ($this->isBookPaid($book)) {
            return false;
        }

        // 7. Free / 0-price book without priced chapters
        return true;
    }

    public function accessType(?Student $student, ?Book $book): string
    {
        if (! $book) {
            return 'guest';
        }

        if (! $student) {
            return 'guest'; // must log in before purchasing, adding to shelf, or reading
        }

        if ($this->grants->hasActiveAccess($student)) {
            return 'granted';
        }

        $subscription = $student->activeSubscription;
        if ($subscription && $subscription->expires_at->isFuture()
            && in_array($book->id, $subscription->plan->gift_book_ids ?? [], true)) {
            return 'subscription_gift';
        }

        if ($this->hasPricedChapters($book)) {
            if ($this->hasAccess($student, $book)) {
                return 'owned';
            }

            if ($student->bookShelf()->where('book_id', $book->id)->exists()) {
                return 'freemium_shelf';
            }

            if ($book->price > 0 && $student->wallet_balance >= $book->price) {
                return 'purchasable';
            }

            return 'chapter_gated';
        }

        if ($book->is_free) {
            return 'free';
        }

        if ($student->bookShelf()->where('book_id', $book->id)->exists()) {
            return 'owned';
        }

        if ($student->wallet_balance >= $book->price) {
            return 'purchasable';
        }

        return 'insufficient_funds';
    }

    /**
     * Can the student read THIS CHAPTER? Chapters of a fully-accessible
     * book are always readable; chapters of a gated book are readable only
     * through ownership of the chapter itself OR any of its ancestors
     * (owning "Chapter 2" unlocks "Section 2.1.3").
     */
    public function canAccessChapter(?Student $student, BookChapter $chapter, ?Book $book = null): bool
    {
        $book = $book ?? $chapter->book ?? Book::find($chapter->book_id);
        if (! $book) {
            return false;
        }

        // Full book access covers every chapter.
        // Rule 1: "with book paid all chapter unlocked even chapter has price"
        if ($this->hasAccess($student, $book)) {
            return true;
        }

        // Check if student explicitly owns this chapter or its ancestor
        if ($student !== null) {
            $candidate = $chapter;
            $chapterIds = [$candidate->id];

            while ($candidate->parent_id !== null) {
                $candidate = BookChapter::find($candidate->parent_id);
                if (! $candidate) {
                    break;
                }
                $chapterIds[] = $candidate->id;
            }

            $ownsChapter = $student->ownedChapters()
                ->where('book_id', $book->id)
                ->whereIn('chapter_id', $chapterIds)
                ->exists();

            if ($ownsChapter) {
                return true;
            }
        }

        // Rule 1: If book has price, all chapters are locked unless the book is paid (or chapter was purchased above)
        if ($this->isBookPaid($book)) {
            return false;
        }

        // Rule 2: If book is free or price is 0, only free chapters can be read
        if ($chapter->price === null || (float) $chapter->price <= 0) {
            return true;
        }

        // Paid chapter on a free/0-price book requires payment
        return false;
    }

    /**
     * All chapter ids in this book the student can read (for reader
     * navigation and RAG scoping). Returns null when the book is entirely
     * inaccessible and the student owns no chapters in it.
     *
     * @return array<int, int>|null
     */
    public function accessibleChapterIds(?Student $student, Book $book): ?array
    {
        if ($this->hasAccess($student, $book)) {
            return $book->chapters()->pluck('id')->all();
        }

        // Rule 1: For a paid book, only explicitly owned chapters are accessible
        if ($this->isBookPaid($book)) {
            if ($student === null) {
                return null;
            }

            $owned = $student->ownedChapters()
                ->where('book_id', $book->id)
                ->pluck('chapter_id')
                ->all();

            if (empty($owned)) {
                return null;
            }

            $ids = $owned;
            do {
                $new = $book->chapters()
                    ->whereIn('parent_id', $ids)
                    ->whereNotIn('id', $ids)
                    ->pluck('id')
                    ->all();
                $ids = array_merge($ids, $new);
            } while ($new);

            return array_values(array_unique($ids));
        }

        // Rule 2: For a free or 0-price book, free chapters are accessible + any owned chapters
        $freeChapterIds = $book->chapters()
            ->where(function ($q) {
                $q->whereNull('price')->orWhere('price', '<=', 0);
            })
            ->pluck('id')
            ->all();

        $owned = [];
        if ($student !== null) {
            $owned = $student->ownedChapters()
                ->where('book_id', $book->id)
                ->pluck('chapter_id')
                ->all();
        }

        $accessible = array_values(array_unique(array_merge($freeChapterIds, $owned)));

        if (empty($accessible)) {
            return null;
        }

        // Expand downward transitively: owning a chapter unlocks every
        // descendant section, however deep the tree.
        $ids = $accessible;

        do {
            $new = $book->chapters()
                ->whereIn('parent_id', $ids)
                ->whereNotIn('id', $ids)
                ->pluck('id')
                ->all();
            $ids = array_merge($ids, $new);
        } while ($new);

        return array_values(array_unique($ids));
    }

    /**
     * Can the student read a specific PAGE of the book — the reader's
     * gate. When the book has parsed chapters and chapter-purchase is
     * enabled, a page belongs to the deepest chapter covering it.
     */
    public function canAccessPage(?Student $student, Book $book, int $page): bool
    {
        if ($this->hasAccess($student, $book)) {
            return true;
        }

        // Front matter (pages before the first indexed chapter starts: cover, TOC, preface) are always accessible
        $firstChapterStart = $book->chapters()->min('start_page');
        if ($firstChapterStart && $page < $firstChapterStart) {
            return true;
        }

        // No chapters (legacy book) → fallback to bookshelf check
        if (! $book->chapters()->exists()) {
            return $book->is_free || ($student && $student->bookShelf()->where('book_id', $book->id)->exists());
        }

        $chapter = $book->pages()
            ->where('page_number', $page)
            ->value('chapter_id');

        if (! $chapter) {
            $chapter = $book->chapters()
                ->where('start_page', '<=', $page)
                ->where(fn ($q) => $q->whereNull('end_page')->orWhere('end_page', '>=', $page))
                ->orderByDesc('level')
                ->value('id');
        }

        if (! $chapter) {
            return false;
        }

        return $this->canAccessChapter($student, BookChapter::find($chapter));
    }
}
