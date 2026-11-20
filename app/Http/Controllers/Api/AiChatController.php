<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\CreateChatSessionRequest;
use App\Models\AiSession;
use App\Models\Book;
use App\Models\MyBook;
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
        return $this->success(auth('sanctum')->user()->aiSessions()->latest()->get(['id', 'title', 'source_type', 'source_book_id', 'source_my_book_id', 'created_at']));
    }

    public function store(CreateChatSessionRequest $request, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        $book = $request->source_book_id ? Book::find($request->source_book_id) : null;
        $myBook = ($request->my_book_id ?? $request->source_my_book_id)
            ? MyBook::find($request->my_book_id ?? $request->source_my_book_id)
            : null;

        if ($book) {
            $canAccess = $access->hasAccess($student, $book)
                || ($student && $student->ownedChapters()->where('book_id', $book->id)->exists());
            abort_unless($canAccess, 403);
        }

        if ($myBook) {
            abort_unless($myBook->student_id === $student->id, 403);
        }

        $session = $student->aiSessions()->create([
            'source_type' => $myBook ? 'upload' : ($book ? 'book' : 'none'),
            'source_book_id' => $book?->id,
            'source_my_book_id' => $myBook?->id,
            'title' => $request->title ?: ($myBook ? "Chat: {$myBook->title}" : ($book ? "Chat: {$book->title}" : 'New chat')),
            'messages' => [],
        ]);

        return $this->success($session, 'Session created', 201);
    }

    public function show(AiSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        return $this->success($session);
    }

    public function rename(Request $request, AiSession $session): JsonResponse
    {
        $this->authorizeSession($session);
        $validated = $request->validate(['title' => ['required', 'string', 'max:255']]);
        $session->update(['title' => trim($validated['title'])]);

        return $this->success($session, 'Session renamed');
    }

    public function attachBook(Request $request, AiSession $session, BookAccessService $access): JsonResponse
    {
        $this->authorizeSession($session);
        $student = auth('sanctum')->user();

        $validated = $request->validate([
            'source_book_id' => ['nullable', 'integer', 'exists:books,id'],
            'source_my_book_id' => ['nullable', 'integer', 'exists:my_books,id'],
        ]);

        if (! empty($validated['source_my_book_id'])) {
            $myBook = MyBook::findOrFail($validated['source_my_book_id']);
            abort_unless($myBook->student_id === $student->id, 403);
            $session->update([
                'source_type' => 'upload',
                'source_book_id' => null,
                'source_my_book_id' => $myBook->id,
            ]);
        } elseif (! empty($validated['source_book_id'])) {
            $book = Book::findOrFail($validated['source_book_id']);
            $canAccess = $access->hasAccess($student, $book)
                || $student->ownedChapters()->where('book_id', $book->id)->exists();
            abort_unless($canAccess, 403);
            $session->update([
                'source_type' => 'book',
                'source_book_id' => $book->id,
                'source_my_book_id' => null,
            ]);
        } else {
            $session->update([
                'source_type' => 'none',
                'source_book_id' => null,
                'source_my_book_id' => null,
            ]);
        }

        return $this->success($session->fresh(), 'Book attachment updated');
    }

    public function destroy(AiSession $session): JsonResponse
    {
        $this->authorizeSession($session);
        $session->delete();

        return $this->success(null, 'Deleted');
    }

    /**
     * Direct synchronous AI question-answering for mobile and client apps.
     */
    public function ask(Request $request, BookChunkRetrievalService $retrieval): JsonResponse
    {
        $student = auth('sanctum')->user();
        $userMessage = trim((string) $request->input('message', ''));
        if ($userMessage === '') {
            return $this->error('Message cannot be empty', [], 422);
        }

        $bookId = $request->input('book_id');
        $book = $bookId ? Book::find($bookId) : null;
        $sessionId = $request->input('session_id');

        $session = null;
        if ($sessionId && $student) {
            $session = $student->aiSessions()->find($sessionId);
        }
        if (! $session && $student) {
            $session = $student->aiSessions()->create([
                'source_type' => $book ? 'book' : 'none',
                'source_book_id' => $book?->id,
                'title' => mb_substr($userMessage, 0, 40),
                'messages' => [],
            ]);
        }

        $messages = $session?->messages ?? [];
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $systemPrompt = 'You are Sikhun AI Tutor (শিখুন এআই টিউটর), a helpful, encouraging and accurate study assistant for Bangladeshi students. Always respond clearly in Bengali (বাংলা) with bullet points and easy-to-understand explanations unless the user asks in English.';
        if ($book) {
            $contextBlocks = $retrieval->buildContext($book, $userMessage, $student);
            if ($contextBlocks) {
                $systemPrompt .= "\n\nUse these excerpts from \"{$book->title}\" where relevant:\n\n".$this->renderContext($contextBlocks);
            }
        }

        $payload = array_merge([['role' => 'system', 'content' => $systemPrompt]], array_slice($messages, -10));
        $reply = '';

        try {
            $provider = AiProviderFactory::default('book_chat', ['student' => $student]);
            // Attempt streaming accumulation to handle any provider contract cleanly
            foreach ($provider->stream($payload) as $chunk) {
                $reply .= $chunk;
            }
            if (empty(trim($reply))) {
                $reply = $provider->complete($payload);
            }
        } catch (\Throwable $e) {
            \Log::error('AI Chat Error: '.$e->getMessage());
            $reply = 'দুঃখিত, এআই সার্ভার সাময়িকভাবে ব্যস্ত আছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।';
        }

        if (empty(trim($reply))) {
            $reply = 'দুঃখিত, এই মুহূর্তে কোনো উত্তর তৈরি করা সম্ভব হয়নি। অনুগ্রহ করে প্রশ্নটি আবার সংক্ষেপে লিখুন।';
        }

        if ($session) {
            $messages[] = ['role' => 'assistant', 'content' => $reply];
            $session->update(['messages' => $messages]);
            if ($student) {
                $student->recordSuccessfulAiChat();
            }
        }

        return $this->success([
            'reply' => $reply,
            'session_id' => $session?->id,
            'status' => 'success',
        ]);
    }

    public function stream(Request $request, AiSession $session, BookChunkRetrievalService $retrieval): StreamedResponse
    {
        $this->authorizeSession($session);
        $student = auth('sanctum')->user();
        $userMessage = trim((string) $request->input('message', ''));
        abort_if($userMessage === '', 400);
        abort_unless($student->hasActiveAiChatAccess(), 403, 'AI trial exhausted — subscribe to continue.');

        return response()->stream(function () use ($session, $student, $userMessage, $retrieval) {
            $messages = $session->messages ?? [];
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $systemPrompt = 'You are a helpful, encouraging study assistant for a Bangladeshi student. Respond in the same language the student writes in.';
            if ($session->source_type === 'book' && $session->book) {
                // Access-aware structured context: chunks are restricted to
                // chapters this student can read (see BookChunkRetrievalService),
                // and each source carries chapter/page info for citations.
                $contextBlocks = $retrieval->buildContext($session->book, $userMessage, $student);

                if ($contextBlocks) {
                    $systemPrompt .= "\n\nUse these excerpts from \"{$session->book->title}\" to ground your answer where relevant. "
                        ."When you use an excerpt, mention its chapter and page (e.g. \"Chapter 3, Page 42\"):\n\n"
                        .$this->renderContext($contextBlocks);
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
                if ($sub = $student->activeSubscription) {
                    $sub->decrement('ai_chat_minutes_remaining');
                } else {
                    $student->increment('ai_trial_minutes_used');
                }
            }

            if ($fullReply !== '') {
                $student->recordSuccessfulAiChat();
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
