<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\CreateChatSessionRequest;
use App\Models\AiSession;
use App\Models\Book;
use App\Services\AccessGrantService;
use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\BookChunkRetrievalService;
use App\Services\BookAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiChatController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->aiSessions()->latest()->get(['id', 'title', 'source_type', 'created_at']));
    }

    public function store(CreateChatSessionRequest $request, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        $book = $request->source_book_id ? Book::find($request->source_book_id) : null;

        if ($book) {
            abort_unless($access->hasAccess($student, $book), 403);
        }

        $session = $student->aiSessions()->create([
            'source_type' => $book ? 'book' : 'none',
            'source_book_id' => $book?->id,
            'title' => $request->title ?: ($book ? "Chat: {$book->title}" : 'New chat'),
            'messages' => [],
        ]);

        return $this->success($session, 'Session created', 201);
    }

    public function show(AiSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        return $this->success($session);
    }

    public function destroy(AiSession $session): JsonResponse
    {
        $this->authorizeSession($session);
        $session->delete();

        return $this->success(null, 'Deleted');
    }

    public function stream(Request $request, AiSession $session, BookChunkRetrievalService $retrieval): StreamedResponse
    {
        $this->authorizeSession($session);
        $student = auth('sanctum')->user();
        $userMessage = trim((string) $request->input('message', ''));
        abort_if($userMessage === '', 400);
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted — subscribe to continue.');

        return response()->stream(function () use ($session, $student, $userMessage, $retrieval) {
            $messages = $session->messages ?? [];
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $systemPrompt = 'You are a helpful, encouraging study assistant for a Bangladeshi student. Respond in the same language the student writes in.';
            if ($session->source_type === 'book' && $session->book) {
                $context = implode("\n---\n", $retrieval->relevantChunks($session->book, $userMessage));
                if ($context) {
                    $systemPrompt .= "\n\nUse these excerpts from \"{$session->book->title}\":\n\n{$context}";
                }
            }

            $payload = array_merge([['role' => 'system', 'content' => $systemPrompt]], array_slice($messages, -10));
            $fullReply = '';

            try {
                foreach (AiProviderFactory::default('book_chat')->stream($payload) as $chunk) {
                    $fullReply .= $chunk;
                    echo 'data: '.json_encode(['content' => $chunk, 'done' => false])."\n\n";
                    ob_flush();
                    flush();
                }
            } catch (\Throwable $e) {
                echo 'data: '.json_encode(['error' => $e->getMessage()])."\n\n";
                ob_flush();
                flush();

                return;
            }

            $messages[] = ['role' => 'assistant', 'content' => $fullReply];
            $session->update(['messages' => $messages]);

            if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
                $student->increment('ai_trial_minutes_used');
            }

            echo 'data: '.json_encode(['content' => '', 'done' => true])."\n\n";
            ob_flush();
            flush();
        }, 200, ['Content-Type' => 'text/event-stream', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }

    private function authorizeSession(AiSession $session): void
    {
        abort_unless($session->student_id === auth('sanctum')->id(), 403);
    }
}
