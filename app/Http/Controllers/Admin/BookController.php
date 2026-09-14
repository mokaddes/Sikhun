<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Jobs\ProcessBookPdf;
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
        $data['chapter_purchase_enabled'] = $request->boolean('chapter_purchase_enabled');

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
            ProcessBookPdf::dispatch($book->id);
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
        $data['chapter_purchase_enabled'] = $request->boolean('chapter_purchase_enabled');

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
            ProcessBookPdf::dispatch($book->id);
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
        Storage::disk('private')->deleteDirectory("books/{$book->id}/images");

        $book->delete();

        return back()->with('success', 'Book deleted.');
    }

    /**
     * Admin book detail view: PDF processing status, parsed structure
     * counts, chapter tree, and RAG state.
     */
    public function show(Book $book): Response
    {
        $book->load(['author'])->loadCount(['pages', 'chapters', 'elements', 'tables', 'images', 'formulas', 'chunks']);

        $chunksWithEmbeddings = $book->chunks()->whereNotNull('embedding')->count();

        return Inertia::render('Admin/Books/Show', [
            'book' => $book,
            'chapters' => $book->chapters()
                ->orderBy('sort_order')
                ->get(['id', 'parent_id', 'chapter_number', 'title', 'level', 'start_page', 'end_page', 'price']),
            'stats' => [
                'pages' => $book->pages_count,
                'chapters' => $book->chapters_count,
                'elements' => $book->elements_count,
                'tables' => $book->tables_count,
                'images' => $book->images_count,
                'formulas' => $book->formulas_count,
                'chunks' => $book->chunks_count,
                'chunks_with_embeddings' => $chunksWithEmbeddings,
            ],
        ]);
    }

    /** Retry a failed (or force a fresh) PDF processing run. */
    public function retryProcessing(Book $book): RedirectResponse
    {
        abort_unless($book->pdf_path, 422, 'This book has no PDF to process.');

        ProcessBookPdf::dispatch($book->id, force: true);

        return back()->with('success', 'PDF processing queued.');
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
