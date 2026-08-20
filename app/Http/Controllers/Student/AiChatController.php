<?php

namespace App\Http\Controllers\Student;

use App\Contracts\AiProviderContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateChatSessionRequest;
use App\Models\AiSession;
use App\Models\Book;
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
        ]);
    }

    public function create(CreateChatSessionRequest $request, BookAccessService $access): RedirectResponse
    {
        $student = auth('web')->user();
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
            'session' => $session->load('book'),
            'sessions' => $student->aiSessions()->latest()->get(['id', 'title', 'source_type', 'created_at']),
            'books' => $student->books()->get(['books.id', 'books.title', 'books.cover_image']),
        ]);
    }

    public function destroy(AiSession $session): RedirectResponse
    {
        $this->authorizeSession($session);
        $session->delete();

        return redirect()->route('ai-chat.index')->with('success', 'Chat deleted.');
    }

    public function attachBook(Request $request, AiSession $session, BookAccessService $access): RedirectResponse
    {
        $this->authorizeSession($session);

        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $book = Book::find($validated['book_id']);

        abort_unless($access->hasAccess(auth('web')->user(), $book), 403, 'You need access to this book to attach it.');

        $session->update([
            'source_type' => 'book',
            'source_book_id' => $book->id,
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
                $context = implode("\n---\n", $retrieval->relevantChunks($session->book, $userMessage));
                if ($context) {
                    $systemPrompt .= "\n\nUse the following excerpts from the book \"{$session->book->title}\" to ground your answer where relevant:\n\n{$context}";
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
}
