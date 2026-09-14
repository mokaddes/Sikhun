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

    public function hasAccess(?Student $student, Book $book): bool
    {
        return in_array($this->accessType($student, $book), ['free', 'granted', 'owned', 'subscription_gift'], true);
    }

    public function accessType(?Student $student, Book $book): string
    {
        if ($book->is_free) {
            return 'free';
        }

        if (! $student) {
            return 'guest'; // must log in before purchasing or reading
        }

        if ($this->grants->hasActiveAccess($student)) {
            return 'granted';
        }

        if ($student->bookShelf()->where('book_id', $book->id)->exists()) {
            return 'owned';
        }

        $subscription = $student->activeSubscription;
        if ($subscription && $subscription->expires_at->isFuture()
            && in_array($book->id, $subscription->plan->gift_book_ids ?? [], true)) {
            return 'subscription_gift';
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
    public function canAccessChapter(?Student $student, BookChapter $chapter): bool
    {
        $book = $chapter->book;

        // Full book access covers every chapter.
        if ($this->hasAccess($student, $book)) {
            return true;
        }

        if ($student === null) {
            return false;
        }

        // Walk the ancestor chain: the chapter, its parent, grandparent…
        $candidate = $chapter;
        $chapterIds = [$candidate->id];

        while ($candidate->parent_id !== null) {
            $candidate = BookChapter::find($candidate->parent_id);
            if (! $candidate) {
                break;
            }
            $chapterIds[] = $candidate->id;
        }

        return $student->ownedChapters()
            ->where('book_id', $book->id)
            ->whereIn('chapter_id', $chapterIds)
            ->exists();
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

        if ($student === null) {
            return null;
        }

        $owned = $student->ownedChapters()
            ->where('book_id', $book->id)
            ->pluck('chapter_id')
            ->all();

        if (! $owned) {
            return null;
        }

        // Expand downward transitively: owning a chapter unlocks every
        // descendant section, however deep the tree.
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

        if ($student === null) {
            return false;
        }

        // No chapters (legacy book) → no partial access possible.
        if (! $book->chapters()->exists()) {
            return false;
        }

        $chapter = $book->pages()
            ->where('page_number', $page)
            ->value('chapter_id');

        if (! $chapter) {
            return false;
        }

        return $this->canAccessChapter($student, BookChapter::find($chapter));
    }
}
