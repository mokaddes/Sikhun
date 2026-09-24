<?php

namespace App\Http\Controllers\Student;

use App\Contracts\AiProviderContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateChatSessionRequest;
use App\Models\AiSession;
use App\Models\Book;
use App\Models\MyBook;
use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\BookChunkRetrievalService;
use App\Services\Ai\Providers\AbstractOpenAiCompatibleProvider;
use App\Services\AccessGrantService;
use App\Services\BookAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiChatController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/AiChat/Index', [
            'sessions' => $student->aiSessions()->latest()->get(['id', 'title', 'source_type', 'created_at']),
            'books' => $student->books()->get(['books.id', 'books.title']),
            'myBooks' => $student->myBooks()->latest()->get(['id', 'title']),
        ]);
    }

    public function create(CreateChatSessionRequest $request, BookAccessService $access): RedirectResponse
    {
        $student = auth('web')->user();

        if ($request->source_my_book_id ?? $request->my_book_id) {
            $myBook = MyBook::find($request->my_book_id ?? $request->source_my_book_id);
            abort_unless($myBook && $myBook->student_id === $student->id, 403, 'You can only chat about your own books.');

            $session = $student->aiSessions()->create([
                'source_type' => 'upload',
                'source_my_book_id' => $myBook->id,
                'title' => $request->title ?: $myBook->title,
                'messages' => [],
            ]);

            return redirect()->route('ai-chat.show', $session);
        }

        $book = $request->source_book_id ? Book::find($request->source_book_id) : null;

        if ($book) {
            abort_unless($access->hasAccess($student, $book), 403, 'You need access to this book to chat about it.');
        }

        $session = $student->aiSessions()->create([
            'source_type' => $book ? 'book' : 'none',
            'source_book_id' => $book?->id,
            'title' => $request->title ?: ($book ? "Chat: {$book->title}" : 'New chat'),
            'messages' => [],
        ]);

        return redirect()->route('ai-chat.show', $session);
    }

    public function show(AiSession $session): Response
    {
        $this->authorizeSession($session);
        $student = auth('web')->user();

        return Inertia::render('Student/AiChat/Show', [
            'session' => $session->load(['book', 'myBook']),
            'sessions' => $student->aiSessions()->latest()->get(['id', 'title', 'source_type', 'created_at']),
            'books' => $student->books()->get(['books.id', 'books.title', 'books.cover_image']),
            'myBooks' => $student->myBooks()->latest()->get(['id', 'title']),
        ]);
    }

    public function destroy(AiSession $session): RedirectResponse
    {
        $this->authorizeSession($session);
        $session->delete();

        return redirect()->route('ai-chat.index')->with('success', 'Chat deleted.');
    }

    public function rename(Request $request, AiSession $session): RedirectResponse
    {
        $this->authorizeSession($session);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $session->update(['title' => trim($validated['title'])]);

        return back();
    }

    public function attachBook(Request $request, AiSession $session, BookAccessService $access): RedirectResponse
    {
        $this->authorizeSession($session);

        $validated = $request->validate([
            'book_id' => ['nullable', 'integer', 'exists:books,id'],
            'my_book_id' => ['nullable', 'integer', 'exists:my_books,id'],
        ]);

        abort_unless($validated['book_id'] || $validated['my_book_id'], 422, 'Select a book to attach.');

        $student = auth('web')->user();

        if ($validated['my_book_id']) {
            $myBook = MyBook::find($validated['my_book_id']);
            abort_unless($myBook && $myBook->student_id === $student->id, 403, 'You can only attach your own books.');

            $session->update([
                'source_type' => 'upload',
                'source_book_id' => null,
                'source_my_book_id' => $myBook->id,
            ]);

            return back();
        }

        $book = Book::find($validated['book_id']);
        abort_unless($access->hasAccess($student, $book), 403, 'You need access to this book to attach it.');

        $session->update([
            'source_type' => 'book',
            'source_book_id' => $book->id,
            'source_my_book_id' => null,
        ]);

        return back();
    }

    /**
     * Streaming endpoint. POSTs the user's message (and optionally an image)
     * as multipart form data and streams the assistant's reply back as SSE —
     * one round trip both records the user turn AND streams the reply.
     */
    public function stream(Request $request, AiSession $session, BookChunkRetrievalService $retrieval): StreamedResponse
    {
        $this->authorizeSession($session);
        $student = auth('web')->user();

        $userMessage = trim((string) $request->input('message', ''));
        $image = $request->file('image');

        abort_if($userMessage === '' && ! $image, 400, 'Message cannot be empty.');

        $imagePath = null;
        if ($image) {
            $request->validate(['image' => ['nullable', 'mimes:png,jpg,jpeg,gif,webp', 'max:10240']]);
            $imagePath = $image->store('ai-chat-images', 'public');
        }

        if (! $student->hasActiveAiAccess()) {
            return response()->stream(function () {
                echo 'data: '.json_encode(['error' => 'Your free AI trial is used up. Subscribe to a plan to continue.'])."\n\n";
                ob_flush();
                flush();
            }, 200, ['Content-Type' => 'text/event-stream', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
        }

        return response()->stream(function () use ($session, $student, $userMessage, $imagePath, $retrieval) {
            $messages = $session->messages ?? [];

            $userMsg = ['role' => 'user', 'content' => $userMessage];
            if ($imagePath) {
                $userMsg['content'] = [
                    ['type' => 'text', 'text' => $userMessage],
                    ['type' => 'image', 'path' => $imagePath],
                ];
            }
            $messages[] = $userMsg;

            $systemPrompt = 'You are a helpful, encouraging study assistant for a Bangladeshi student. Answer clearly and concisely. Respond in the same language the student writes in (Bengali or English).';

            if ($session->source_type === 'book' && $session->book && $userMessage !== '') {
                $book = $session->book;

                $systemPrompt .= "\n\nThe student has attached the book \"{$book->title}\" (up to {$book->total_pages} pages). "
                    .'Ground your answer in that book where relevant and cite its chapter and page (e.g. "Chapter 3, Page 42").';

                // Access-aware structured context (chunks restricted to
                // readable chapters) with chapter/page citation info.
                $contextBlocks = $retrieval->buildContext($book, $userMessage, $student);

                if ($contextBlocks) {
                    $systemPrompt .= "\n\nExcerpts from \"{$book->title}\":\n\n".$this->renderContext($contextBlocks);
                } elseif (! $book->chunks()->exists()) {
                    // Book attached but its content was never indexed (still
                    // processing, or a failed run). Say so honestly instead of
                    // pretending the attachment doesn't exist.
                    $systemPrompt .= "\n\nIMPORTANT: The attached book's content has not been indexed yet "
                        .'(there are no searchable chunks for it at this moment). If the student asks about the '
                        ."book's content, politely explain that the book is still being prepared and ask them to "
                        .'try again in a few minutes. Do not claim you have no information about the attachment.';
                } else {
                    $systemPrompt .= "\n\nNo excerpt from the attached book matched the student's question. "
                        .'Answer from general knowledge, but do not invent book-specific content and note that '
                        .'you could not find it in the attached book.';
                }

                $outline = $book->chapters()->orderBy('sort_order')->pluck('title');
                if ($outline->isNotEmpty()) {
                    $systemPrompt .= "\n\nBook chapter outline: ".$outline->implode(' | ');
                }
            } elseif ($session->source_type === 'upload' && $session->myBook && $userMessage !== '') {
                $myBook = $session->myBook;

                $systemPrompt .= "\n\nThe student has attached their own uploaded document \"{$myBook->title}\". "
                    .'Ground your answer in that document where relevant and cite the page (e.g. "Page 12").';

                if ($myBook->processing_status !== 'completed') {
                    $systemPrompt .= "\n\nIMPORTANT: The uploaded document's content has not been prepared yet. "
                        ."If the student asks about \"{$myBook->title}\", politely explain it is still being prepared "
                        .'and ask them to try again in a few minutes. Do not claim the document is attached but empty.';
                } else {
                    $context = $this->myBookContext($myBook, $userMessage);

                    if ($context) {
                        $systemPrompt .= "\n\nExcerpts from \"{$myBook->title}\":\n\n".$context;
                    } else {
                        $systemPrompt .= "\n\nNo part of the uploaded document matched the student's question. "
                            .'Answer from general knowledge, but do not invent document-specific content and note '
                            .'that you could not find it in the attached document.';
                    }
                }
            }

            $payload = array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                array_slice($messages, -10) // keep the payload bounded
            );

            $fullReply = '';

            try {
                $provider = AiProviderFactory::default('book_chat');
                $payload = $this->normalizePayloadForProvider($payload, $provider);

                foreach ($provider->stream($payload) as $chunk) {
                    $fullReply .= $chunk;
                    echo 'data: '.json_encode(['content' => $chunk, 'done' => false])."\n\n";
                    ob_flush();
                    flush();
                }
            } catch (\Throwable $e) {
                echo 'data: '.json_encode(['error' => 'AI service unavailable: '.$e->getMessage()])."\n\n";
                ob_flush();
                flush();

                return;
            }

            $messages[] = ['role' => 'assistant', 'content' => $fullReply];
            $session->update([
                'messages' => $messages,
                'tokens_used' => $session->tokens_used + (int) (strlen($userMessage.$fullReply) / 4),
            ]);

            // Free campaign/coupon access is unlimited — never consume paid
            // quota while a grant is active.
            if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
                $student->increment('ai_trial_minutes_used');
                if ($sub = $student->activeSubscription) {
                    $sub->decrement('ai_chat_minutes_remaining');
                }
            }

            echo 'data: '.json_encode(['content' => '', 'done' => true])."\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Convert stored message content into whatever the active provider
     * understands. OpenAI-compatible providers get the native multimodal
     * content array (image_url parts); everything else just receives the
     * plain text so array-shaped content never breaks their serializers.
     */
    private function normalizePayloadForProvider(array $payload, AiProviderContract $provider): array
    {
        if ($provider instanceof AbstractOpenAiCompatibleProvider) {
            return array_map(function (array $message) {
                if (is_string($message['content'])) {
                    return $message;
                }

                $parts = [];
                foreach ($message['content'] as $part) {
                    if (($part['type'] ?? null) === 'text') {
                        $parts[] = ['type' => 'text', 'text' => (string) ($part['text'] ?? '')];
                    } elseif (($part['type'] ?? null) === 'image' && ! empty($part['path'])) {
                        $parts[] = ['type' => 'image_url', 'image_url' => ['url' => $this->imageDataUrl($part['path'])]];
                    }
                }

                $message['content'] = $parts;

                return $message;
            }, $payload);
        }

        return array_map(function (array $message) {
            if (is_string($message['content'])) {
                return $message;
            }

            $message['content'] = collect($message['content'])
                ->where('type', 'text')
                ->pluck('text')
                ->implode("\n");

            return $message;
        }, $payload);
    }

    private function imageDataUrl(string $path): string
    {
        $mime = Storage::disk('public')->mimeType($path);
        $data = Storage::disk('public')->get($path);

        return 'data:'.$mime.';base64,'.base64_encode((string) $data);
    }

    private function authorizeSession(AiSession $session): void
    {
        abort_unless($session->student_id === Auth::guard('web')->id(), 403);
    }

    /**
     * Lightweight retrieval for a student's own uploaded document: score
     * pages by how many distinctive question terms appear in them, then
     * return the best pages as bounded excerpt blocks. No embeddings, no
     * chapters — fine for personal notes.
     */
    private function myBookContext(MyBook $myBook, string $question): string
    {
        $terms = collect(preg_split('/\s+/u', mb_strtolower($question) ?: '') ?? [])
            ->filter(fn (string $w) => mb_strlen($w) > 2)
            ->values();

        $pages = $myBook->pages()->get(['page_number', 'content']);

        if ($terms->isEmpty()) {
            $pages = $pages->take(5);
        } else {
            $pages = $pages->map(function ($page) use ($terms) {
                $lower = mb_strtolower((string) $page->content);
                $score = $terms->sum(fn (string $w) => mb_substr_count($lower, $w));

                return ['page' => $page, 'score' => $score];
            })->filter(fn ($row) => $row['score'] > 0)
                ->sortByDesc('score')
                ->take(5)
                ->map(fn ($row) => $row['page']);
        }

        $blocks = [];
        $budget = 9000;

        foreach ($pages as $page) {
            $content = mb_substr((string) $page->content, 0, 6000);
            if (trim($content) === '') {
                continue;
            }

            $block = "[Source: {$myBook->title} — Page {$page->page_number}]\n".$content;
            if ($budget <= 0) {
                break;
            }

            $blocks[] = mb_substr($block, 0, $budget);
            $budget -= mb_strlen(end($blocks));
        }

        return implode("\n---\n", $blocks);
    }

    /**
     * Render structured RAG context blocks into a compact text prompt the
     * LLM can cite from — book/chapter/section/page header plus content,
     * with related tables/formulas inline.
     */
    private function renderContext(array $contextBlocks): string
    {
        return collect($contextBlocks)
            ->map(function (array $block) {
                $source = $block['book'];
                $source .= $block['chapter'] ? " — {$block['chapter']}" : '';
                $source .= $block['section'] ? " — {$block['section']}" : '';
                $source .= $block['page'] ? " (Page {$block['page']})" : '';

                $parts = ["[Source: {$source}]"];
                $parts[] = $block['content'];

                foreach ($block['tables'] as $table) {
                    $parts[] = '[Related table'.($table['title'] ? ": {$table['title']}" : '')."]\n"
                        .($table['markdown'] ?: json_encode($table['structured'] ?? []));
                }

                foreach ($block['formulas'] as $formula) {
                    $parts[] = '[Formula] '.($formula['latex'] ?: $formula['content'] ?? '');
                }

                foreach ($block['images'] as $image) {
                    $parts[] = '[Image description] '.($image['description'] ?: $image['alt_text'] ?? '');
                }

                return implode("\n", $parts);
            })
            ->implode("\n---\n");
    }
}
