<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\ReadingSession;
use App\Services\Ai\PageChatService;
use App\Services\BookAccessService;
use App\Services\BookReaderService;
use App\Services\Pdf\PageTextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends BaseApiController
{
    public function show(Request $request, Book $book, BookAccessService $access, BookReaderService $reader): JsonResponse
    {
        $student = auth('sanctum')->user();
        $canOpenReader = $access->hasAccess($student, $book)
            || ($student && $student->ownedChapters()->where('book_id', $book->id)->exists());
        abort_unless($canOpenReader, 403, 'You do not have access to this book.');

        if ((int) $book->total_pages <= 0 && $book->pdf_path && Storage::disk('private')->exists($book->pdf_path)) {
            $detected = $reader->detectPageCount(Storage::disk('private')->path($book->pdf_path));
            if ($detected !== null && $detected > 0) {
                $book->update(['total_pages' => $detected]);
            }
        }

        $accessibleChapterIds = $access->accessibleChapterIds($student, $book);
        $accessiblePages = null;
        if ($accessibleChapterIds !== null && ! $access->hasAccess($student, $book)) {
            $pageNumbersFromDb = $book->pages()
                ->whereIn('chapter_id', $accessibleChapterIds)
                ->pluck('page_number')
                ->all();

            $chapterRanges = $book->chapters()
                ->whereIn('id', $accessibleChapterIds)
                ->get(['start_page', 'end_page']);

            $pagesFromRanges = [];
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

        return $this->success([
            'book' => $book->only(['id', 'title', 'slug', 'total_pages']),
            'accessible_pages' => $accessiblePages,
            'initial_page' => $initialPage,
            'chapters' => $book->chapters()
                ->orderByRaw('CASE WHEN start_page IS NOT NULL AND start_page > 0 THEN start_page ELSE 999999 END ASC')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'parent_id', 'title', 'chapter_number', 'level', 'start_page', 'end_page']),
        ]);
    }

    public function saveProgress(Request $request, Book $book): JsonResponse
    {
        $student = auth('sanctum')->user();
        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $page = max(1, min((int) $validated['page'], (int) ($book->total_pages ?: $validated['page'])));

        $session = ReadingSession::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->whereDate('created_at', today())
            ->first();

        if (! $session) {
            ReadingSession::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'pages_read' => 1,
                'last_page' => $page,
                'last_activity_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        } else {
            $session->update([
                'last_page' => $page,
                'pages_read' => max((int) $session->pages_read, $page),
                'last_activity_at' => now(),
            ]);
        }

        return $this->success(['saved' => true, 'last_page' => $page], 'Progress saved');
    }

    public function chat(Request $request, Book $book, BookAccessService $access, PageChatService $chat, PageTextService $pageText): StreamedResponse
    {
        $student = auth('sanctum')->user();
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'page' => ['required', 'integer', 'min:1'],
            'session_id' => ['nullable', 'integer'],
        ]);

        $target = (int) $validated['page'];
        abort_unless($access->canAccessPage($student, $book, $target), 403);

        $contextPages = [];
        foreach ([$target - 1, $target, $target + 1] as $p) {
            if ($p < 1 || ($book->total_pages && $p > (int) $book->total_pages)) {
                continue;
            }
            if (! $access->canAccessPage($student, $book, $p)) {
                continue;
            }
            $text = $pageText->forPage($book, $p);
            if ($text !== null && trim($text) !== '') {
                $contextPages[] = ['page_number' => $p, 'content' => mb_substr($text, 0, 8000)];
            }
        }

        $session = null;
        if (! empty($validated['session_id'])) {
            $session = $student->aiSessions()->find($validated['session_id']);
        }
        if (! $session) {
            $bnPage = BookChapter::toBengaliDigits($target);
            $session = $student->aiSessions()->create([
                'source_type' => 'book',
                'source_book_id' => $book->id,
                'title' => "{$book->title} - পৃষ্ঠা {$bnPage}",
                'messages' => [],
            ]);
        }

        return $chat->stream($book, $student, $validated['message'], $target, $contextPages, $session);
    }
    public function pageUrl(Request $request, Book $book, int $page, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        // Chapter-aware gate: full book access OR the page's chapter is owned.
        abort_unless($access->canAccessPage($student, $book, $page), 403);

        $session = ReadingSession::where('student_id', $student->id)->where('book_id', $book->id)
            ->whereDate('created_at', today())->first();

        if (! $session) {
            ReadingSession::create(['student_id' => $student->id, 'book_id' => $book->id, 'pages_read' => $page, 'ip_address' => $request->ip()]);
            $book->increment('reading_count');
        } else {
            $session->update(['pages_read' => max($session->pages_read, $page), 'last_activity_at' => now()]);
        }

        $url = URL::temporarySignedRoute(
            'api.reader.page',
            now()->addMinutes(15),
            ['book' => $book->id, 'page' => $page, 'student' => $student->id]
        );

        return $this->success(['url' => $url]);
    }

    /** Reached only via the signed URL minted by pageUrl() above — see routes/api.php. */
    public function servePage(Request $request, Book $book, int $page, BookReaderService $reader): HttpResponse
    {
        abort_unless((int) $request->query('student') === auth('sanctum')->id(), 403);

        $student = auth('sanctum')->user();
        $isSvg = $reader->isPlaceholder($book);
        $bytes = $reader->renderPage($book, $page, $student);

        return response($bytes, 200, [
            'Content-Type' => $isSvg ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'private, max-age=900',
        ]);
    }
}
