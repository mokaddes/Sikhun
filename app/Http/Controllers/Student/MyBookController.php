<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMyBookPdf;
use App\Models\MyBook;
use App\Services\BookReaderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyBookController extends Controller
{
    public function store(Request $request, BookReaderService $reader): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $student = auth('web')->user();

        $path = $request->file('pdf')->store('my-books', 'private');
        @chmod(Storage::disk('private')->path($path), 0666);

        $detectedPages = $reader->detectPageCount(Storage::disk('private')->path($path)) ?? 0;

        $myBook = $student->myBooks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'total_pages' => $detectedPages,
            'processing_status' => 'pending',
        ]);

        $reader->clearBookCache($myBook);

        ProcessMyBookPdf::dispatch($myBook->id);

        return back()->with('success', "Your book “{$myBook->title}” was uploaded and is being prepared.");
    }

    public function destroy(MyBook $myBook, BookReaderService $reader): RedirectResponse
    {
        abort_unless($myBook->student_id === auth('web')->id(), 403);

        $reader->clearBookCache($myBook);
        if ($myBook->file_path) {
            Storage::disk('private')->delete($myBook->file_path);
        }
        $myBook->delete();

        return back()->with('success', 'Book removed from your bookshelf.');
    }
}