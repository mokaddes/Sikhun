<?php

namespace App\Services\Ai;

use App\Services\Ai\AiProviderFactory;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Minimal streaming chat grounded in one or more extracted PDF pages —
 * used by the in-reader "chat about this page" widget. Unlike AiChat
 * sessions, there is no persistent history: each request is a single
 * user turn answered with the surrounding page text as context.
 */
class PageChatService
{
    /**
     * @param  array<int, array{page_number: int, content: string}>  $pages
     */
    public function stream(string $documentTitle, array $pages, string $message, ?string $chapterTitle = null): StreamedResponse
    {
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

        $payload = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $message],
        ];

        return response()->stream(function () use ($payload) {
            try {
                $provider = AiProviderFactory::default('book_chat');
                foreach ($provider->stream($payload) as $chunk) {
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

    private function error(string $message): StreamedResponse
    {
        return response()->stream(function () use ($message) {
            echo 'data: '.json_encode(['error' => $message])."\n\n";
            ob_flush();
            flush();
        }, 200, ['Content-Type' => 'text/event-stream', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }
}
