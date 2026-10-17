<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MyBook;
use App\Services\Ai\PageChatService;
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

class MyBookReaderController extends Controller
{
    // Pre-sign up to 250 pages so the opening window paints immediately.
    private const PRE_SIGNED_WINDOW = 250;

    /**
     * Stable signing expiry: URLs for a page/student stay identical all day,
     * so both browser caches and the Imagick cache are reused.
     */
    private function signingExpiry(): Carbon
    {
        return now()->startOfDay()->addDay();
    }

    private function signedPageUrl(int $bookId, int $page, int $studentId): string
    {
        return URL::temporarySignedRoute('my-books.reader.page', $this->signingExpiry(), [
            'myBook' => $bookId,
            'page' => $page,
            'student' => $studentId,
        ]);
    }

    private function authorize(MyBook $myBook): void
    {
        abort_unless($myBook->student_id === auth('web')->id(), 403);
    }

    public function show(MyBook $myBook, BookReaderService $reader): Response
    {
        $this->authorize($myBook);

        if ((int) $myBook->total_pages <= 0 && $myBook->file_path && \Illuminate\Support\Facades\Storage::disk('private')->exists($myBook->file_path)) {
            $detected = $reader->detectPageCount(\Illuminate\Support\Facades\Storage::disk('private')->path($myBook->file_path));
            if ($detected !== null && $detected > 0) {
                $myBook->update(['total_pages' => $detected]);
            }
        }

        $limit = min(self::PRE_SIGNED_WINDOW, (int) $myBook->total_pages);
        $pageUrls = [];
        if ($limit > 0) {
            foreach (range(1, $limit) as $p) {
                $pageUrls[$p] = $this->signedPageUrl($myBook->id, $p, auth('web')->id());
            }
        }

        return Inertia::render('Student/MyBooks/Reader', [
            'myBook' => $myBook->only(['id', 'title', 'total_pages', 'processing_status', 'processing_error']),
            'pageUrls' => $pageUrls,
            'initialPage' => max(1, min((int) ($myBook->last_page ?: 1), (int) ($myBook->total_pages ?: 1))),
        ]);
    }

    public function pageUrl(Request $request, MyBook $myBook, int $page): JsonResponse
    {
        $this->authorize($myBook);

        return response()->json(['url' => $this->signedPageUrl($myBook->id, $page, auth('web')->id())]);
    }

    /**
     * Remember the student's last page in their own upload so the reader
     * resumes there next time.
     */
    public function saveProgress(Request $request, MyBook $myBook): JsonResponse
    {
        $this->authorize($myBook);

        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $page = max(1, min((int) $validated['page'], (int) ($myBook->total_pages ?: 1)));

        $myBook->update(['last_page' => $page]);

        return response()->json(['saved' => true]);
    }

    public function servePage(Request $request, MyBook $myBook, int $page, BookReaderService $reader): HttpResponse
    {
        $this->authorize($myBook);

        abort_unless((int) $request->query('student') === auth('web')->id(), 403);

        $bytes = $reader->renderPage($myBook, $page, auth('web')->user());
        $isSvg = str_starts_with(ltrim($bytes), '<svg');

        return response($bytes, 200, [
            'Content-Type' => $isSvg ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'private, max-age=86400, immutable',
        ]);
    }

    public function chat(Request $request, MyBook $myBook, PageChatService $chat, PageTextService $pageText): StreamedResponse
    {
        $this->authorize($myBook);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $target = (int) $validated['page'];

        $contextPages = [];
        foreach ([$target - 1, $target, $target + 1] as $p) {
            if ($p < 1 || $p > (int) $myBook->total_pages) {
                continue;
            }

            $text = $pageText->forPage($myBook, $p);
            if ($text !== null && trim($text) !== '') {
                $contextPages[] = ['page_number' => $p, 'content' => mb_substr($text, 0, 8000)];
            }
        }

        return $chat->stream($myBook->title, $contextPages, $validated['message']);
    }
}
