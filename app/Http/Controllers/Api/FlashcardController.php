<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\GenerateFlashcardsRequest;
use App\Jobs\GenerateFlashcards;
use App\Models\Book;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Services\AccessGrantService;
use App\Services\BookAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlashcardController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->flashcardSets()->withCount('flashcards')->latest()->get());
    }

    public function store(GenerateFlashcardsRequest $request, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted.');

        $sourceText = $request->source_text;
        $sourceLabel = null;

        if ($request->source_book_id) {
            $book = Book::findOrFail($request->source_book_id);
            abort_unless($access->hasAccess($student, $book), 403);
            $sourceText = $book->chunks()->orderBy('chunk_index')->limit(6)->pluck('content')->implode("\n");
            $sourceLabel = $book->title;
        }

        $set = $student->flashcardSets()->create(['title' => $request->title, 'source_label' => $sourceLabel]);
        GenerateFlashcards::dispatch($set->id, $sourceText, (int) $request->count);

        if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return $this->success($set, 'Flashcard generation started', 201);
    }

    public function show(FlashcardSet $set): JsonResponse
    {
        $this->authorizeSet($set);

        return $this->success($set->load('flashcards'));
    }

    public function review(Request $request, FlashcardSet $set, Flashcard $flashcard): JsonResponse
    {
        $this->authorizeSet($set);
        $request->validate(['result' => 'required|in:known,review_again']);

        $flashcard->increment('review_count');
        $flashcard->update([
            'last_result' => $request->result,
            'next_review_at' => $request->result === 'known' ? now()->addDays(2 ** min($flashcard->review_count, 6)) : now()->addHour(),
        ]);

        return $this->success($flashcard);
    }

    private function authorizeSet(FlashcardSet $set): void
    {
        abort_unless($set->student_id === auth('sanctum')->id(), 403);
    }
}
