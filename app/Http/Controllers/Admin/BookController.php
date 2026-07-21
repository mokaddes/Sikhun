<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Books/Index', [
            'books' => Book::with(['author', 'category'])->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Books/Form', [
            'book' => null,
            ...$this->formOptions(),
        ]);
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'pdf_file']);
        $data['is_free'] = $request->boolean('is_free');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_premium_gift'] = $request->boolean('is_premium_gift');

        if ($request->hasFile('cover_image')) {
            // Covers are safe to serve publicly — only the PDF itself is sensitive.
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            // NEVER store on the 'public' disk. This path is only ever resolved
            // server-side through the signed, watermarked reader endpoint (Phase 3).
            $data['pdf_path'] = $request->file('pdf_file')->store('books/pdfs', 'private');
        }

        $book = Book::create($data);

        if ($request->hasFile('pdf_file')) {
            \App\Jobs\ProcessBookChunking::dispatch($book->id);
        }

        return redirect()->route('admin.books.index')->with('success', 'Book created.');
    }

    public function edit(Book $book): Response
    {
        return Inertia::render('Admin/Books/Form', [
            'book' => $book,
            ...$this->formOptions(),
        ]);
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'pdf_file']);
        $data['is_free'] = $request->boolean('is_free');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_premium_gift'] = $request->boolean('is_premium_gift');

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_path) {
                Storage::disk('private')->delete($book->pdf_path);
            }
            $data['pdf_path'] = $request->file('pdf_file')->store('books/pdfs', 'private');
        }

        $book->update($data);

        if ($request->hasFile('pdf_file')) {
            \App\Jobs\ProcessBookChunking::dispatch($book->id);
        }

        return redirect()->route('admin.books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_path) {
            Storage::disk('private')->delete($book->pdf_path);
        }

        $book->delete();

        return back()->with('success', 'Book deleted.');
    }

    private function formOptions(): array
    {
        return [
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'publications' => Publication::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ];
    }
}
