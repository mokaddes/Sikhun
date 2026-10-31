<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\GenerateFlashcardsRequest;
use App\Models\Book;
use App\Models\FlashcardSet;
use App\Services\Ai\FlashcardSourceService;
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

    public function createForm(BookAccessService $access): Response
    {
        $student = auth('web')->user();
        $books = Book::query()
            ->where(function ($query) use ($student) {
                $query->where('is_free', true)
                    ->orWhereIn('id', $student->books()->select('books.id'))
                    ->orWhereIn('id', $student->ownedChapters()->select('book_id'));
            })
            ->get(['id', 'title', 'processing_status'])
            ->map(function (Book $book) use ($student, $access) {
                $accessibleIds = $access->accessibleChapterIds($student, $book) ?? [];
                $book->setAttribute('chapters', $book->chapters()
                    ->whereIn('id', $accessibleIds)
                    ->get(['id', 'title', 'chapter_number']));

                return $book;
            });

        return Inertia::render('Student/Flashcards/Create', ['books' => $books]);
    }

    public function create(GenerateFlashcardsRequest $request, FlashcardSourceService $sources): RedirectResponse
    {
        $student = auth('web')->user();
        $sourceText = (string) $request->source_text;
        $sourceLabel = null;
        $sourceReferences = [];

        if ($request->source_book_id) {
            $source = $sources->fromBook(
                Book::findOrFail($request->source_book_id),
                $student,
                $request->input('chapter_ids', []),
            );
            $sourceText = $source['content'];
            $sourceLabel = $source['source_label'];
            $sourceReferences = $source['references'];
        }

        $set = $student->flashcardSets()->create([
            'title' => $request->title,
            'source_label' => $sourceLabel,
        ]);

        \App\Jobs\GenerateFlashcards::dispatch($set->id, $sourceText, (int) $request->count, $sourceReferences);

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
