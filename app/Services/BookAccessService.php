<?php

namespace App\Services;

use App\Models\Book;
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
}
