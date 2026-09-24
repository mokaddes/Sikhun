<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MyBook;
use App\Services\Ai\PageChatService;
use App\Services\BookReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyBookReaderController extends Controller
{
    private function authorize(MyBook $myBook): void
    {
        abort_unless($myBook->student_id === auth('web')->id(), 403);
    }

    public function show(MyBook $myBook): Response
    {
        $this->authorize($myBook);

        return Inertia::render('Student/MyBooks/Reader', [
            'myBook' => $myBook->only(['id', 'title', 'total_pages', 'processing_status', 'processing_error']),
        ]);
    }

    public function pageUrl(Request $request, MyBook $myBook, int $page): JsonResponse
    {
        $this->authorize($myBook);

        $url = URL::temporarySignedRoute(
            'my-books.reader.page',
            now()->addMinutes(15),
            ['myBook' => $myBook->id, 'page' => $page, 'student' => auth('web')->id()]
        );

        return response()->json(['url' => $url]);
    }

    public function servePage(Request $request, MyBook $myBook, int $page, BookReaderService $reader): HttpResponse
    {
        $this->authorize($myBook);

        abort_unless((int) $request->query('student') === auth('web')->id(), 403);

        $isSvgPlaceholder = $reader->isPlaceholder($myBook);
        $bytes = $reader->renderPage($myBook, $page, auth('web')->user());

        return response($bytes, 200, [
            'Content-Type' => $isSvgPlaceholder ? 'image/svg+xml' : 'image/jpeg',
            'Cache-Control' => 'private, max-age=900',
        ]);
    }

    public function chat(Request $request, MyBook $myBook, PageChatService $chat): StreamedResponse
    {
        $this->authorize($myBook);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $contextPages = $myBook->pages()
            ->whereBetween('page_number', [$validated['page'] - 1, $validated['page'] + 1])
            ->orderBy('page_number')
            ->get()
            ->map(fn ($p) => [
                'page_number' => $p->page_number,
                'content' => mb_substr((string) $p->content, 0, 8000),
            ])
            ->filter(fn ($p) => trim($p['content']) !== '')
            ->values()
            ->all();

        return $chat->stream($myBook->title, $contextPages, $validated['message']);
    }
}