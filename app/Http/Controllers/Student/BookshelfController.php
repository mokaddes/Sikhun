<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReadingSession;
use Inertia\Inertia;
use Inertia\Response;

class BookshelfController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        $shelves = $student->bookShelf()
            ->with('book:id,title,slug,cover_image,level,subject,total_pages')
            ->latest('added_at')
            ->get();

        $bookIds = $shelves->pluck('book_id')->all();
        $lastPages = ReadingSession::where('student_id', $student->id)
            ->whereIn('book_id', $bookIds)
            ->whereNotNull('last_page')
            ->orderBy('id', 'desc')
            ->get(['book_id', 'last_page'])
            ->unique('book_id')
            ->pluck('last_page', 'book_id');

        $shelves->each(function ($shelf) use ($lastPages) {
            $shelf->last_read_page = $lastPages[$shelf->book_id] ?? 1;
        });

        return Inertia::render('Student/Bookshelf/Index', [
            'shelves' => $shelves,
            'myBooks' => $student->myBooks()->latest()->get(),
        ]);
    }
}
