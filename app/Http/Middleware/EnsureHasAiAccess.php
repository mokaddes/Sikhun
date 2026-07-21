<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates every AI feature behind "active subscription OR unused trial
 * minutes" (REQ-CHAT-01 and equivalent for exam/flashcard/essay/schedule).
 * Trial minutes are a simple counter, not literal wall-clock minutes —
 * each AI action (send a chat message, generate an exam, etc.) consumes 1.
 *
 * The actual check lives on Student::hasActiveAiAccess() — this middleware
 * (and AiChatController::stream, which can't use middleware cleanly since
 * it needs to reply with an SSE-formatted error, not a redirect) both just
 * call that one method rather than duplicating the logic.
 */
class EnsureHasAiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $student = $request->user('web');

        if ($student->hasActiveAiAccess()) {
            return $next($request);
        }

        return back()->with('error', 'Your free AI trial is used up. Subscribe to a plan to continue.');
    }
}
