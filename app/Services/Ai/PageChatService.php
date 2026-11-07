<?php

namespace App\Services\Ai;

use App\Models\AiSession;
use App\Services\AccessGrantService;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Minimal streaming chat grounded in one or more extracted PDF pages —
 * used by the in-reader "chat about this page" widget.
 * Saves user conversation into an attached AiSession so the student
 * can view and continue discussions directly from /ai/chat.
 */
class PageChatService
{
    /**
     * @param  array<int, array{page_number: int, content: string}>  $pages
     */
    public function stream(
        string $documentTitle,
        array $pages,
        string $message,
        ?string $chapterTitle = null,
        ?AiSession $session = null
    ): StreamedResponse {
        $student = Auth::guard('web')->user();

        if (! $student || ! $student->hasActiveAiAccess()) {
            return $this->error('Your free AI trial is used up. Subscribe to a plan to continue.');
        }

        $system = 'You are a helpful, encouraging study assistant for a Bangladeshi student. '
            .'Answer clearly and concisely. Respond in the same language the student writes in (Bengali or English). '
            ."The student is reading \"{$documentTitle}\"".($chapterTitle ? " in chapter/section \"{$chapterTitle}\"" : '')." and has shared the surrounding page text with you. "
            .'Ground your answer in that text where relevant, citing the page number (e.g. "Page 12").';

        if ($pages) {
            foreach ($pages as $page) {
                $system .= "\n\n[Page {$page['page_number']} of \"{$documentTitle}\"]\n".$page['content'];
            }
        } else {
            $system .= "\n\nNo page text is available for this part of the book yet. "
                .'Answer from general knowledge and politely note the page text is not available.';
        }

        $history = [];
        if ($session && ! empty($session->messages)) {
            // Keep recent turns for conversation continuity
            $history = array_slice($session->messages, -6);
        }

        $payload = array_merge(
            [['role' => 'system', 'content' => $system]],
            $history,
            [['role' => 'user', 'content' => $message]]
        );

        return response()->stream(function () use ($payload, $student, $message, $session) {
            // 1. Emit session meta event if a session is attached
            if ($session) {
                echo 'data: '.json_encode([
                    'session_id' => $session->id,
                    'session_title' => $session->title,
                ])."\n\n";
                ob_flush();
                flush();
            }

            $fullReply = '';

            try {
                $provider = AiProviderFactory::default('book_chat', ['student' => $student]);
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

            // 2. Persist turn into AiSession so it appears in /ai/chat
            if ($session) {
                $messages = $session->messages ?? [];
                $messages[] = ['role' => 'user', 'content' => $message];
                $messages[] = ['role' => 'assistant', 'content' => $fullReply];

                $tokensUsed = ($session->tokens_used ?? 0) + (int) (strlen($message . $fullReply) / 4);
                $session->update([
                    'messages' => $messages,
                    'tokens_used' => $tokensUsed,
                ]);

                // Track quota usage
                if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
                    $student->increment('ai_trial_minutes_used');
                    if ($sub = $student->activeSubscription) {
                        $sub->decrement('ai_chat_minutes_remaining');
                    }
                }
            }

            echo 'data: '.json_encode([
                'content' => '',
                'done' => true,
                'session_id' => $session?->id,
            ])."\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    private function error(string $message): StreamedResponse
    {
        return response()->stream(function () use ($message) {
            echo 'data: '.json_encode(['error' => $message])."\n\n";
            ob_flush();
            flush();
        }, 200, ['Content-Type' => 'text/event-stream', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }
}
