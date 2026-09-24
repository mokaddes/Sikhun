<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookAccessService;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LibraryController extends Controller
{
    public function index(Request $request, \App\Services\SeoService $seo): Response
    {
        $sort = $request->input('sort', 'newest');

        $books = Book::published()
            ->with(['author:id,name', 'category:id,name'])
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->when($request->subject, fn ($q) => $q->where('subject', $request->subject))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->free, fn ($q) => $q->where('is_free', true))
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', "%{$request->search}%")
                ->orWhere('subject', 'like', "%{$request->search}%")
            ))
            ->when($sort === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('reading_count'))
            ->when($sort === 'newest', fn ($q) => $q->latest())
            ->paginate(16)
            ->withQueryString();

        // Real, derived from actual book data — not a hardcoded list, so it
        // always reflects whatever subjects your catalog actually has.
        $subjects = Book::published()->whereNotNull('subject')->distinct()->orderBy('subject')->pluck('subject');

        return Inertia::render('Student/Library/Index', [
            'seo' => $seo->forLibrary(),
            'books' => $books,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'subjects' => $subjects,
            'filters' => $request->only('level', 'subject', 'category_id', 'free', 'search', 'sort'),
        ]);
    }

    public function show(Book $book, BookAccessService $access, \App\Services\SeoService $seo): Response
    {
        abort_unless($book->is_published, 404);

        $student = auth('web')->user();

        // Chapter list with per-chapter access state (for chapter purchase UI).
        // Transitive set (chapter + descendants) — sections of an owned
        // chapter are readable and must not display as locked.
        $hasFullAccess = $access->hasAccess($student, $book);
        $ownedChapterIds = $hasFullAccess ? null : ($access->accessibleChapterIds($student, $book) ?? []);

        $chapters = collect();
        if ($book->chapter_purchase_enabled && $book->chapters()->exists()) {
            $chapters = $book->chapters()
                ->orderBy('sort_order')
                ->get(['id', 'parent_id', 'chapter_number', 'title', 'level', 'start_page', 'end_page', 'price'])
                ->map(function ($chapter) use ($ownedChapterIds, $hasFullAccess) {
                    return [
                        'id' => $chapter->id,
                        'parent_id' => $chapter->parent_id,
                        'chapter_number' => $chapter->chapter_number,
                        'title' => $chapter->title,
                        'level' => $chapter->level,
                        'start_page' => $chapter->start_page,
                        'end_page' => $chapter->end_page,
                        'price' => $chapter->price,
                        'owned' => $hasFullAccess || in_array($chapter->id, $ownedChapterIds),
                        'purchasable' => $chapter->level === 1 && $chapter->price !== null,
                    ];
                });
        }

        return Inertia::render('Student/Library/Show', [
            'book' => $book->load(['author', 'publication', 'category']),
            'accessType' => $access->accessType($student, $book),
            'onShelf' => $student && $student->bookShelf()->where('book_id', $book->id)->exists(),
            'chapters' => $chapters,
            'seo' => $seo->forBook($book),
        ]);
    }

    /**
     * One-click bookshelf add for FREE books only — no payment flow, the
     * row is created directly (idempotent via the unique student/book pair).
     */
    public function addToShelf(Book $book): \Illuminate\Http\RedirectResponse
    {
        abort_unless($book->is_published && $book->is_free, 403, 'Only free books can be added directly to your bookshelf.');

        $student = auth('web')->user();

        $student->bookShelf()->firstOrCreate(
            ['book_id' => $book->id],
            ['source' => 'free', 'added_at' => now()]
        );

        return back()->with('success', 'Book added to your bookshelf!');
    }

    public function purchaseChapter(Request $request, Book $book, \App\Models\BookChapter $chapter, PurchaseService $purchases, ZinipayService $zinipay): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($chapter->book_id === $book->id, 404);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet,zinipay'],
        ]);

        $student = auth('web')->user();

        try {
            $result = $purchases->purchaseChapter(
                $student,
                $chapter,
                $validated['payment_method'],
                $validated['payment_method'] === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['redirect_url']) {
            return Inertia::location($result['redirect_url']);
        }

        return back()->with('success', 'Chapter purchased!');
    }

    public function purchase(PurchaseRequest $request, Book $book, PurchaseService $purchases, ZinipayService $zinipay): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();

        try {
            $result = $purchases->purchaseBook(
                $student,
                $book,
                $request->payment_method,
                $request->payment_method === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['redirect_url']) {
            return Inertia::location($result['redirect_url']);
        }

        return redirect()->route('bookshelf.index')->with('success', 'Book added to your bookshelf!');
    }
}
