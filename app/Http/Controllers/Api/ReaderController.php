<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\ReadingSession;
use App\Services\BookAccessService;
use App\Services\BookReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\URL;

class ReaderController extends BaseApiController
{
    public function pageUrl(Request $request, Book $book, int $page, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($access->hasAccess($student, $book), 403);

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
