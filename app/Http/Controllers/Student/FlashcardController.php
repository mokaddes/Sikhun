<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\GenerateFlashcardsRequest;
use App\Models\FlashcardSet;
use App\Services\BookAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FlashcardController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Flashcards/Index', [
            'sets' => $student->flashcardSets()->withCount('flashcards')->latest()->get(),
        ]);
    }

    public function createForm(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Flashcards/Create', [
            'books' => $student->books()->get(['books.id', 'books.title']),
        ]);
    }

    public function create(GenerateFlashcardsRequest $request, BookAccessService $access): RedirectResponse
    {
        $student = auth('web')->user();
        $sourceText = $request->source_text;
        $sourceLabel = null;

        if ($request->source_book_id) {
            $book = \App\Models\Book::findOrFail($request->source_book_id);
            abort_unless($access->hasAccess($student, $book), 403);
            $sourceText = $book->chunks()->orderBy('chunk_index')->limit(6)->pluck('content')->implode("\n");
            $sourceLabel = $book->title;
        }

        $set = $student->flashcardSets()->create([
            'title' => $request->title,
            'source_label' => $sourceLabel,
        ]);

        \App\Jobs\GenerateFlashcards::dispatch($set->id, $sourceText, (int) $request->count);

        if (! app(\App\Services\AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return redirect()->route('flashcards.show', $set);
    }

    public function show(FlashcardSet $set): Response
    {
        $this->authorizeSet($set);

        return Inertia::render('Student/Flashcards/Show', [
            'set' => $set->load('flashcards'),
        ]);
    }

    public function review(Request $request, FlashcardSet $set, \App\Models\Flashcard $flashcard): RedirectResponse
    {
        $this->authorizeSet($set);
        $request->validate(['result' => 'required|in:known,review_again']);

        $flashcard->increment('review_count');
        $flashcard->update([
            'last_result' => $request->result,
            'next_review_at' => $request->result === 'known'
                ? now()->addDays(2 ** min($flashcard->review_count, 6))
                : now()->addHour(),
        ]);

        return back();
    }

    public function pdf(FlashcardSet $set)
    {
        $this->authorizeSet($set);
        $pdf = Pdf::loadView('pdf.flashcard-set', ['set' => $set->load('flashcards')]);

        return $pdf->download("flashcards-{$set->id}.pdf");
    }

    public function destroy(FlashcardSet $set): RedirectResponse
    {
        $this->authorizeSet($set);
        $set->delete();

        return redirect()->route('flashcards.index')->with('success', 'Flashcard set deleted.');
    }

    private function authorizeSet(FlashcardSet $set): void
    {
        abort_unless($set->student_id === Auth::guard('web')->id(), 403);
    }
}
