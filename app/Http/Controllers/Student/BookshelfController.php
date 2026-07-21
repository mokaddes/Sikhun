<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BookshelfController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Bookshelf/Index', [
            'shelves' => $student->bookShelf()
                ->with('book:id,title,slug,cover_image,level,subject,total_pages')
                ->latest('added_at')
                ->get(),
        ]);
    }
}
