<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMyBookPdf;
use App\Models\MyBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MyBookController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $student = auth('web')->user();

        $path = $request->file('pdf')->store('my-books', 'private');

        $myBook = $student->myBooks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'total_pages' => 0,
            'processing_status' => 'pending',
        ]);

        ProcessMyBookPdf::dispatch($myBook->id);

        return back()->with('success', "Your book “{$myBook->title}” was uploaded and is being prepared.");
    }

    public function destroy(MyBook $myBook): RedirectResponse
    {
        abort_unless($myBook->student_id === auth('web')->id(), 403);

        \Illuminate\Support\Facades\Storage::disk('private')->delete($myBook->file_path);
        $myBook->delete();

        return back()->with('success', 'Book removed from your bookshelf.');
    }
}