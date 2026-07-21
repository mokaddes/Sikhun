<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingSession;
use App\Services\BookAccessService;
use App\Services\BookReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class ReaderController extends Controller
{
    /**
     * The reader shell itself — Inertia page that mounts FlipReader.vue.
     * Starts (or resumes) today's ReadingSession row for this student+book.
     */
    public function show(Request $request, Book $book, BookAccessService $access): Response
    {
        $student = auth('web')->user();
        abort_unless($access->hasAccess($student, $book), 403, 'You do not have access to this book.');

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

        return Inertia::render('Student/Library/Reader', [
            'book' => $book->only(['id', 'title', 'slug', 'total_pages']),
        ]);
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
        abort_unless($access->hasAccess($student, $book), 403);

        $this->trackProgress($student->id, $book->id, $page);

        $url = URL::temporarySignedRoute(
            'reader.page',
            now()->addMinutes(15),
            ['book' => $book->id, 'page' => $page, 'student' => $student->id]
        );

        return response()->json(['url' => $url]);
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
            'Cache-Control' => 'private, max-age=900',
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
            'duration_seconds' => now()->diffInSeconds($session->created_at),
            'last_activity_at' => now(),
        ]);
    }
}
