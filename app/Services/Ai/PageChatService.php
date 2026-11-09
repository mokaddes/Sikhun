<?php

namespace App\Services\Ai;

use App\Models\AiSession;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\Ai\Contracts\AiProviderFactory;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        if (! $student || ! $student->hasActiveAiChatAccess()) {
            return $this->error('Your free AI trial is used up. Subscribe to a plan to continue.');
        }

        $excerpt = $this->buildContext($pages);

        $system = "You are an AI study assistant for the book '{$documentTitle}'";
        if ($chapterTitle) {
            $system .= " (Chapter: {$chapterTitle})";
        }
        $system .= ".\n\n"
            ."You have access to the following page excerpts:\n"
            ."---\n{$excerpt}\n---\n"
            .'Answer the student clearly, accurately, and concisely. '
            .'Always answer in Bengali unless the student specifically asks in English. '
            .'If the excerpt is empty or unrelated, politely answer from general knowledge '
            .'and remind them to make sure the page has extracted text.';

        if (blank($excerpt)) {
            $system = "You are an AI study assistant for the book '{$documentTitle}'. "
                .'The text for the requested pages could not be extracted from the PDF. '
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
                report($e);
                echo 'data: '.json_encode(['error' => 'AI generation failed: '.$e->getMessage()])."\n\n";
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
            }

            // Track quota usage
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

            echo 'data: '.json_encode([
                'content' => '',
                'done' => true,
                'session_id' => $session?->id,
            ])."\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * @param  array<int, array{page_number: int, content: string}>  $pages
     */
    protected function buildContext(array $pages): string
    {
        $buffer = [];
        $totalLength = 0;
        $maxChars = 6000;

        foreach ($pages as $p) {
            $num = $p['page_number'] ?? '?';
            $text = trim($p['content'] ?? '');
            if ($text === '') {
                continue;
            }

            $entry = "[Page {$num}]\n{$text}";
            $len = mb_strlen($entry);

            if ($totalLength + $len > $maxChars) {
                $remaining = $maxChars - $totalLength;
                if ($remaining > 200) {
                    $buffer[] = mb_substr($entry, 0, $remaining).'... [truncated]';
                }
                break;
            }

            $buffer[] = $entry;
            $totalLength += $len;
        }

        return implode("\n\n", $buffer);
    }

    protected function error(string $message): StreamedResponse
    {
        return response()->stream(function () use ($message) {
            echo 'data: '.json_encode(['error' => $message])."\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}