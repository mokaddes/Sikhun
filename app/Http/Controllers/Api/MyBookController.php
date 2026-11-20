<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ProcessMyBookPdf;
use App\Models\AiSession;
use App\Models\BookChapter;
use App\Models\MyBook;
use App\Models\Student;
use App\Services\Ai\PageChatService;
use App\Services\BookReaderService;
use App\Services\Pdf\PageTextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyBookController extends BaseApiController
{
    private const PRE_SIGNED_WINDOW = 1000;

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
        abort_unless($myBook->student_id === auth('sanctum')->id(), 403);
    }

    public function index(): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();

        return $this->success([
            'my_books' => $student->myBooks()->latest()->get(),
            'upload_quota' => [
                'current_count' => $student->myBooks()->count(),
                'max_slots' => $student->maxUploadBookSlots(),
                'reuploads_remaining' => $student->getMonthlyReuploadsRemaining(),
            ],
        ]);
    }

    public function store(Request $request, BookReaderService $reader): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
            'rag_enabled' => ['nullable', 'boolean'],
        ]);

        /** @var Student $student */
        $student = auth('sanctum')->user();

        $maxSlots = $student->maxUploadBookSlots();
        $currentBooksCount = $student->myBooks()->count();
        if ($currentBooksCount >= $maxSlots) {
            return $this->error("You have reached your book upload limit ({$maxSlots} book slots). Remove an existing book to upload a new one, or upgrade your plan.", [], 422);
        }

        $reuploadsRemaining = $student->getMonthlyReuploadsRemaining();
        if ($reuploadsRemaining <= 0) {
            return $this->error('You have reached your monthly upload / re-upload limit. Your limit will reset on your next billing cycle.', [], 422);
        }

        $path = $request->file('pdf')->store('my-books', 'private');
        @chmod(Storage::disk('private')->path($path), 0666);

        $detectedPages = $reader->detectPageCount(Storage::disk('private')->path($path)) ?? 0;

        $myBook = $student->myBooks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'total_pages' => $detectedPages,
            'rag_enabled' => $request->boolean('rag_enabled', true),
            'processing_status' => 'pending',
        ]);

        $student->recordBookUpload();
        $reader->clearBookCache($myBook);
        ProcessMyBookPdf::dispatch($myBook->id);

        return $this->success($myBook, "Your book \"{$myBook->title}\" was uploaded and is being prepared.", 201);
    }

    public function destroy(MyBook $myBook, BookReaderService $reader): JsonResponse
    {
        $this->authorizeOwner($myBook);

        $reader->clearBookCache($myBook);

        if ($myBook->file_path && Storage::disk('private')->exists($myBook->file_path)) {
            Storage::disk('private')->delete($myBook->file_path);
        }

        $myBook->pages()->delete();
        AiSession::where('source_my_book_id', $myBook->id)->delete();
        $myBook->delete();

        return $this->success(null, 'Book and all related data removed from your bookshelf.');
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

        return $this->success(['url' => $this->signedPageUrl($myBook->id, $page, (int) auth('sanctum')->id())]);
    }

    public function saveProgress(Request $request, MyBook $myBook): JsonResponse
    {
        $this->authorizeOwner($myBook);

        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $page = max(1, min((int) $validated['page'], (int) ($myBook->total_pages ?: 1)));
        $myBook->update(['last_page' => $page]);

        return $this->success(['saved' => true, 'last_page' => $page], 'Progress saved');
    }

    public function servePage(Request $request, MyBook $myBook, int $page, BookReaderService $reader): HttpResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorizeOwner($myBook);
        abort_unless((int) $request->query('student') === (int) auth('sanctum')->id(), 403);

        /** @var Student $student */
        $student = auth('sanctum')->user();
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
        /** @var Student $student */
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
            ]);
        }

        return $chat->stream($myBook, $student, $validated['message'], $target, $contextPages, $session);
    }
}
