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
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends Controller
{
    // How many pages get signed URLs handed to the frontend up front, so the
    // first pages paint with zero extra requests. Farther pages are minted on
    // demand (fast, pure-HMAC) as the student flips.
    private const PRE_SIGNED_WINDOW = 18;

    /**
     * Stable signing expiry: URLs minted for a page/student stay identical for
     * the rest of the day, so browser HTTP caches and the Imagick cache are
     * actually reused instead of re-downloading/re-rendering on every visit.
     */
    private function signingExpiry(): \Illuminate\Support\Carbon
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
    public function show(Request $request, Book $book, BookAccessService $access): Response
    {
        $student = auth('web')->user();
        // Reader shell access: full book access OR ownership of ANY chapter.
        $canOpenReader = $access->hasAccess($student, $book)
            || ($student && $student->ownedChapters()->where('book_id', $book->id)->exists());
        abort_unless($canOpenReader, 403, 'You do not have access to this book.');

        // Which pages may this student turn to? (null = no restriction)
        $accessibleChapterIds = $access->accessibleChapterIds($student, $book);

        $accessiblePages = null;
        if ($accessibleChapterIds !== null && ! $access->hasAccess($student, $book)) {
            $accessiblePages = $book->pages()
                ->whereIn('chapter_id', $accessibleChapterIds)
                ->orderBy('page_number')
                ->pluck('page_number')
                ->all();
        }

        $session = ReadingSession::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->whereDate('created_at', today())
            ->first();

        if (! $session) {
            $session = ReadingSession::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'pages_read' => 0,
                'ip_address' => $request->ip(),
            ]);
            $book->increment('reading_count');
        }

        // Pre-sign the opening window so the first pages render immediately.
        $pageUrls = $this->preSignedWindow($book, $student, $accessiblePages);

        return Inertia::render('Student/Library/Reader', [
            'book' => $book->only(['id', 'title', 'slug', 'total_pages']),
            'accessiblePages' => $accessiblePages,
            'pageUrls' => $pageUrls,
            'chapters' => $book->topChapters()->get(['id', 'title', 'chapter_number', 'start_page']),
        ]);
    }

    private function preSignedWindow(Book $book, $student, ?array $accessiblePages): array
    {
        $limit = min(self::PRE_SIGNED_WINDOW, (int) $book->total_pages);

        $pages = $accessiblePages !== null
            ? collect($accessiblePages)->filter(fn (int $p) => $p <= $limit)->sort()->values()->all()
            : ($limit > 0 ? range(1, $limit) : []);

        $urls = [];
        foreach ($pages as $p) {
            $urls[$p] = $this->signedPageUrl($book->id, $p, $student->id);
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

        return $chat->stream($book->title, $contextPages, $validated['message']);
    }

    /**
     * The actual signed, rate-limited image endpoint (see routes/web.php:
     * `signed` + a 5-per-10-seconds throttle). Never linked to directly —
     * only ever reached via a URL minted by pageUrl() above.
     */
    public function servePage(Request $request, Book $book, int $page, BookReaderService $reader): HttpResponse
    {
        abort_unless((int) $request->query('student') === auth('web')->id(), 403);

        $student = auth('web')->user();
        $isSvgPlaceholder = $reader->isPlaceholder($book);
        $bytes = $reader->renderPage($book, $page, $student);

        return response($bytes, 200, [
            'Content-Type' => $isSvgPlaceholder ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'private, max-age=86400',
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
