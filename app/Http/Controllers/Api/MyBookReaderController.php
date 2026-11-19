<?php

namespace App\Http\Controllers\Api;

use App\Models\BookChapter;
use App\Models\MyBook;
use App\Services\Ai\PageChatService;
use App\Services\BookReaderService;
use App\Services\Pdf\PageTextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyBookReaderController extends BaseApiController
{
    private const PRE_SIGNED_WINDOW = 200;

    private function signingExpiry(): Carbon
    {
        return now()->startOfDay()->addDay();
    }

    private function signedPageUrl(int $bookId, int $page, int $studentId): string
    {
        return URL::temporarySignedRoute('api.my-books.reader.page', $this->signingExpiry(), [
            'myBook' => $bookId,
            'page' => $page,
            'student' => $studentId,
        ]);
    }

    private function authorizeOwner(MyBook $myBook): void
    {
        abort_unless((int) $myBook->student_id === (int) auth('sanctum')->id(), 403);
    }

    public function show(MyBook $myBook, BookReaderService $reader): JsonResponse
    {
        $this->authorizeOwner($myBook);

        if ((int) $myBook->total_pages <= 0 && $myBook->file_path && Storage::disk('private')->exists($myBook->file_path)) {
            $detected = $reader->detectPageCount(Storage::disk('private')->path($myBook->file_path));
            if ($detected !== null && $detected > 0) {
                $myBook->update(['total_pages' => $detected]);
            }
        }

        $limit = min(self::PRE_SIGNED_WINDOW, (int) $myBook->total_pages);
        $pageUrls = [];
        if ($limit > 0) {
            foreach (range(1, $limit) as $p) {
                $pageUrls[$p] = $this->signedPageUrl($myBook->id, $p, (int) auth('sanctum')->id());
            }
        }

        return $this->success([
            'my_book' => $myBook->only(['id', 'title', 'total_pages', 'processing_status', 'processing_error']),
            'page_urls' => $pageUrls,
            'initial_page' => max(1, min((int) ($myBook->last_page ?: 1), (int) ($myBook->total_pages ?: 1))),
        ]);
    }

    public function pageUrl(MyBook $myBook, int $page): JsonResponse
    {
        $this->authorizeOwner($myBook);

        return $this->success([
            'url' => $this->signedPageUrl($myBook->id, $page, (int) auth('sanctum')->id()),
        ]);
    }

    public function saveProgress(Request $request, MyBook $myBook): JsonResponse
    {
        $this->authorizeOwner($myBook);

        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $page = max(1, min((int) $validated['page'], (int) ($myBook->total_pages ?: 1)));
        $myBook->update(['last_page' => $page]);

        return $this->success(['saved' => true, 'last_page' => $page]);
    }

    public function servePage(Request $request, MyBook $myBook, int $page, BookReaderService $reader): Response
    {
        $student = auth('sanctum')->user() ?? \App\Models\Student::findOrFail((int) $request->query('student'));
        abort_unless((int) $myBook->student_id === (int) $student->id, 403);
        abort_unless((int) $request->query('student') === (int) $student->id, 403);

        $filePath = $reader->renderPageFile($myBook, $page, $student);
        if ($filePath && file_exists($filePath)) {
            return response()->file($filePath, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=604800, immutable',
            ]);
        }

        $bytes = $reader->renderPage($myBook, $page, $student);
        $isSvg = str_starts_with(ltrim($bytes), '<svg');

        return response($bytes, 200, [
            'Content-Type' => $isSvg ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    public function chat(Request $request, MyBook $myBook, PageChatService $chat, PageTextService $pageText): StreamedResponse
    {
        $this->authorizeOwner($myBook);
        $student = auth('sanctum')->user();

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'page' => ['required', 'integer', 'min:1'],
            'session_id' => ['nullable', 'integer'],
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

        $session = null;
        if (! empty($validated['session_id'])) {
            $session = $student->aiSessions()->find($validated['session_id']);
        }

        if (! $session) {
            $bnPage = BookChapter::toBengaliDigits($target);
            $session = $student->aiSessions()->create([
                'source_type' => 'upload',
                'source_my_book_id' => $myBook->id,
                'title' => "{$myBook->title} - পৃষ্ঠা {$bnPage}",
                'messages' => [],
                'tokens_used' => 0,
            ]);
        }

        return $chat->stream($myBook->title, $contextPages, $validated['message'], null, $session);
    }
}
