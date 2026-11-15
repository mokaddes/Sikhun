<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingSession;
use App\Services\Ai\PageChatService;
use App\Services\BookAccessService;
use App\Services\BookReaderService;
use App\Services\Pdf\PageTextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends Controller
{
    // Pre-sign up to 1000 pages so almost any book requires zero extra round-trips
    // to mint URLs. Farther pages are minted on demand.
    private const PRE_SIGNED_WINDOW = 1000;

    /**
     * Stable signing expiry: URLs minted for a page/student stay identical for
     * the rest of the day, so browser HTTP caches and the Imagick cache are
     * actually reused instead of re-downloading/re-rendering on every visit.
     */
    private function signingExpiry(): Carbon
    {
        return now()->startOfDay()->addDay();
    }

    private function signedPageUrl(int $bookId, int $page, int $studentId): string
    {
        return URL::temporarySignedRoute('reader.page', $this->signingExpiry(), [
            'book' => $bookId,
            'page' => $page,
            'student' => $studentId,
        ]);
    }

    /**
     * The reader shell itself — Inertia page that mounts FlipReader.vue.
     * Starts (or resumes) today's ReadingSession row for this student+book.
     */
    public function show(Request $request, Book $book, BookAccessService $access, BookReaderService $reader): Response
    {
        $student = auth('web')->user();
        // Reader shell access: full book access, ownership of ANY chapter, book on shelf, accessible free chapters, or globally free book.
        $hasAccessibleChapters = $access->accessibleChapterIds($student, $book) !== null;
        $canOpenReader = $access->hasAccess($student, $book)
            || ($student && $student->ownedChapters()->where('book_id', $book->id)->exists())
            || ($student && $student->bookShelf()->where('book_id', $book->id)->exists())
            || $hasAccessibleChapters
            || $book->is_free;
        abort_unless($canOpenReader, 403, 'You do not have access to this book.');

        if ((int) $book->total_pages <= 0 && $book->pdf_path && \Illuminate\Support\Facades\Storage::disk('private')->exists($book->pdf_path)) {
            $detected = $reader->detectPageCount(\Illuminate\Support\Facades\Storage::disk('private')->path($book->pdf_path));
            if ($detected !== null && $detected > 0) {
                $book->update(['total_pages' => $detected]);
            }
        }

        // Which pages may this student turn to? (null = no restriction)
        $hasFullAccess = $access->hasAccess($student, $book);
        $accessibleChapterIds = $access->accessibleChapterIds($student, $book);

        $accessiblePages = null;
        if (! $hasFullAccess) {
            $accessibleIds = $accessibleChapterIds ?? [];
            $pageNumbersFromDb = $book->pages()
                ->whereIn('chapter_id', $accessibleIds)
                ->pluck('page_number')
                ->all();

            $chapterRanges = $book->chapters()
                ->whereIn('id', $accessibleIds)
                ->get(['start_page', 'end_page']);

            $pagesFromRanges = [];
            // Include front matter (cover, TOC, preface) before the first chapter start page
            $firstChapterStart = $book->chapters()->min('start_page');
            if ($firstChapterStart && (int) $firstChapterStart > 1) {
                for ($p = 1; $p < (int) $firstChapterStart; $p++) {
                    $pagesFromRanges[] = $p;
                }
            }

            foreach ($chapterRanges as $ch) {
                if ($ch->start_page) {
                    $start = (int) $ch->start_page;
                    $end = (int) ($ch->end_page ?: $start);
                    for ($p = $start; $p <= $end; $p++) {
                        $pagesFromRanges[] = $p;
                    }
                }
            }

            $accessiblePages = array_values(array_unique(array_merge($pageNumbersFromDb, $pagesFromRanges)));
            sort($accessiblePages);
        }

        $session = ReadingSession::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->whereDate('created_at', today())
            ->first();

        // Resume where the student last stopped (most recent session for this
        // book, regardless of day). Supports explicit ?page= parameter as well.
        $lastPage = $request->query('page') ?: ReadingSession::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->whereNotNull('last_page')
            ->latest('id')
            ->value('last_page');

        $initialPage = $lastPage
            ? max(1, min((int) $lastPage, (int) ($book->total_pages ?: $lastPage)))
            : 1;

        if ($accessiblePages !== null && ! empty($accessiblePages) && ! in_array($initialPage, $accessiblePages, true)) {
            $initialPage = $accessiblePages[0];
        }

        if (! $session) {
            $session = ReadingSession::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'pages_read' => 0,
                'last_page' => $initialPage,
                'last_activity_at' => now(),
                'ip_address' => $request->ip(),
            ]);
            $book->increment('reading_count');
        } elseif ($initialPage && ! $session->last_page) {
            $session->update(['last_page' => $initialPage]);
        }

        // Pre-sign the opening window so the first pages render immediately.
        $pageUrls = $this->preSignedWindow($book, $student, $accessiblePages);

        $chaptersData = $book->chapters()
            ->orderByRaw('CASE WHEN start_page IS NOT NULL AND start_page > 0 THEN start_page ELSE 999999 END ASC')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'parent_id', 'title', 'chapter_number', 'level', 'start_page', 'end_page', 'price'])
            ->map(function ($ch) use ($access, $student) {
                $isPaid = $ch->price !== null && (float) $ch->price > 0;
                $canAccess = $access->canAccessChapter($student, $ch);
                return [
                    'id' => $ch->id,
                    'parent_id' => $ch->parent_id,
                    'title' => $ch->title,
                    'chapter_number' => $ch->chapter_number,
                    'level' => $ch->level,
                    'start_page' => $ch->start_page,
                    'end_page' => $ch->end_page,
                    'price' => $ch->price ? (float) $ch->price : 0,
                    'is_free' => ! $isPaid,
                    'is_locked' => $isPaid && ! $canAccess,
                    'is_owned' => $canAccess,
                ];
            });

        return Inertia::render('Student/Library/Reader', [
            'book' => $book->only(['id', 'title', 'slug', 'total_pages']),
            'accessiblePages' => $accessiblePages,
            'pageUrls' => $pageUrls,
            'initialPage' => $initialPage,
            'chapters' => $chaptersData,
            'walletBalance' => (float) ($student ? $student->wallet_balance : 0.0),
            'hasFullAccess' => $hasFullAccess,
            'hasPricedChapters' => $access->hasPricedChapters($book),
        ]);
    }

    private function preSignedWindow(Book $book, $student, ?array $accessiblePages): array
    {
        $limit = min(self::PRE_SIGNED_WINDOW, max((int) $book->total_pages, 1));

        $pages = $accessiblePages !== null
            ? collect($accessiblePages)->filter(fn (int $p) => $p <= $limit)->sort()->values()->all()
            : ($limit > 0 ? range(1, $limit) : []);

        $urls = [];
        foreach ($pages as $p) {
            $urls[$p] = $this->signedPageUrl($book->id, $p, $student->id);
        }

        // Always ensure every chapter's start and second page are signed so clicking chapters is instant
        foreach ($book->chapters as $chapter) {
            if ($chapter->start_page && ($accessiblePages === null || in_array($chapter->start_page, $accessiblePages, true))) {
                $sp = (int) $chapter->start_page;
                if (! isset($urls[$sp])) {
                    $urls[$sp] = $this->signedPageUrl($book->id, $sp, $student->id);
                }
                if ($sp + 1 <= (int) $book->total_pages && (! isset($urls[$sp + 1])) && ($accessiblePages === null || in_array($sp + 1, $accessiblePages, true))) {
                    $urls[$sp + 1] = $this->signedPageUrl($book->id, $sp + 1, $student->id);
                }
            }
        }

        return $urls;
    }

    /**
     * Returns a freshly-signed URL for one page — called by the frontend
     * every time the student flips to a new page, rather than pre-signing
     * a whole book up front. This is a normal session-authenticated JSON
     * endpoint; the URL it hands back is what's actually signed+throttled.
     */
    public function pageUrl(Request $request, Book $book, int $page, BookAccessService $access): JsonResponse
    {
        $student = auth('web')->user();
        // Chapter-aware gate: full book access OR the page's chapter is owned.
        abort_unless($access->canAccessPage($student, $book, $page), 403);

        $this->trackProgress($student->id, $book->id, $page);

        return response()->json(['url' => $this->signedPageUrl($book->id, $page, $student->id)]);
    }

    /**
     * Persist the page the student is currently looking at, so reopening the
     * reader resumes here. Called (debounced) by the frontend on every page
     * turn — unlike pageUrl() this is *not* triggered by background prefetch,
     * so it records the true reading position rather than the furthest one.
     */
    public function saveProgress(Request $request, Book $book, BookAccessService $access): JsonResponse
    {
        $student = auth('web')->user();

        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $page = (int) $validated['page'];

        // Silently ignore pages this student can't actually read (e.g. a stray
        // jump target) instead of surfacing an error mid-read.
        if (! $access->canAccessPage($student, $book, $page)) {
            return response()->json(['saved' => false]);
        }

        $session = ReadingSession::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        if ($session) {
            $session->update([
                'last_page' => $page,
                'pages_read' => max((int) $session->pages_read, $page),
                'last_activity_at' => now(),
            ]);
        } else {
            ReadingSession::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'pages_read' => $page,
                'last_page' => $page,
                'last_activity_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json(['saved' => true]);
    }

    /**
     * Streaming chat for the reader widget — grounded in the CURRENT page
     * (plus one page either side for context) instead of whole-book RAG.
     * Page text that the parse pipeline left empty (e.g. scanned PDFs) is
     * extracted on demand, so answers are always about the page the
     * student is actually looking at.
     */
    public function chat(Request $request, Book $book, BookAccessService $access, PageChatService $chat, PageTextService $pageText): StreamedResponse
    {
        $student = auth('web')->user();

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'page' => ['required', 'integer', 'min:1'],
            'session_id' => ['nullable', 'integer'],
        ]);

        $target = (int) $validated['page'];

        abort_unless($access->canAccessPage($student, $book, $target), 403);

        $contextPages = [];
        foreach ([$target - 1, $target, $target + 1] as $p) {
            if ($p < 1 || ($book->total_pages !== null && $p > $book->total_pages)) {
                continue;
            }

            $text = $pageText->forPage($book, $p);
            if ($text !== null && trim($text) !== '') {
                $contextPages[] = ['page_number' => $p, 'content' => mb_substr($text, 0, 8000)];
            }
        }

        $chapter = $book->chapters()->where('start_page', '<=', $target)
            ->where(fn ($q) => $q->whereNull('end_page')->orWhere('end_page', '>=', $target))
            ->orderByDesc('level')->first();

        // Attach to existing AiSession or create a new session named after Book and Page
        $session = null;
        if (! empty($validated['session_id']) && $student) {
            $session = $student->aiSessions()->find($validated['session_id']);
        }

        if (! $session && $student) {
            $bnPage = \App\Models\BookChapter::toBengaliDigits($target);
            $sessionTitle = "{$book->title} - পৃষ্ঠা {$bnPage}";

            $session = $student->aiSessions()->create([
                'source_type' => 'book',
                'source_book_id' => $book->id,
                'title' => $sessionTitle,
                'messages' => [],
                'tokens_used' => 0,
            ]);
        }

        return $chat->stream($book->title, $contextPages, $validated['message'], $chapter?->title, $session);
    }

    /**
     * The actual signed, rate-limited image endpoint (see routes/web.php:
     * `signed` + a 5-per-10-seconds throttle). Never linked to directly —
     * only ever reached via a URL minted by pageUrl() above.
     */
    public function servePage(Request $request, Book $book, int $page, BookReaderService $reader, BookAccessService $access): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless((int) $request->query('student') === auth('web')->id(), 403);

        $student = auth('web')->user();
        abort_unless($access->canAccessPage($student, $book, $page), 403);

        $filePath = $reader->renderPageFile($book, $page, $student);

        if ($filePath && file_exists($filePath)) {
            return response()->file($filePath, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=604800, immutable',
            ]);
        }

        $bytes = $reader->renderPage($book, $page, $student);
        $isSvg = str_starts_with(ltrim($bytes), '<svg');

        return response($bytes, 200, [
            'Content-Type' => $isSvg ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    private function trackProgress(int $studentId, int $bookId, int $page): void
    {
        $session = ReadingSession::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        if (! $session) {
            return;
        }

        $session->update([
            'pages_read' => max($session->pages_read, $page),
            // MySQL round-trips `timestamp` columns through the connection's
            // timezone, which can differ from PHP's app timezone (Asia/Dhaka),
            // making created_at read back "in the future" and the diff negative.
            // duration_seconds is UNSIGNED, so clamp to a non-negative integer
            // to prevent a WRITE failure crashing the reader.
            'duration_seconds' => max(0, (int) now()->diffInSeconds($session->created_at)),
            'last_activity_at' => now(),
        ]);
    }
}
