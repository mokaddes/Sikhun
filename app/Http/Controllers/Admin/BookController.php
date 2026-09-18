<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Jobs\ProcessBookPdf;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publication;
use App\Services\ChunkedUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function __construct(private ChunkedUploadService $uploads) {}

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
        $data = $request->safe()->except(['cover_image', 'pdf_file', 'temp_pdf_path']);
        $data['is_free'] = $request->boolean('is_free');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_premium_gift'] = $request->boolean('is_premium_gift');
        $data['chapter_purchase_enabled'] = $request->boolean('chapter_purchase_enabled');

        if ($request->hasFile('cover_image')) {
            // Covers are safe to serve publicly — only the PDF itself is sensitive.
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book = Book::create($data);

        // PDFs are attached in a separate step (admin.books.pdf-upload) so a
        // large upload never blocks the metadata form. Processing runs in a
        // background job and can take a few minutes for big files.
        return redirect()->route('admin.books.pdf-upload', $book)
            ->with('success', 'Book created. Upload the PDF now — processing may take a few minutes.');
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
        $data = $request->safe()->except(['cover_image', 'pdf_file', 'temp_pdf_path']);
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

        $book->update($data);

        // Changing the PDF is intentionally NOT part of this form — it lives
        // on the dedicated admin.books.pdf-upload page and is processed in a
        // background job so a large upload can't hold up metadata edits.

        return redirect()->route('admin.books.index')->with('success', 'Book updated.');
    }

    /**
     * Dedicated page for attaching/replacing a book's PDF. Uses the chunked
     * uploader (5 MB slices + a background merge) so multi-hundred-MB files
     * work even when a single HTTP request would time out; processing then
     * runs in the ProcessBookPdf queue job.
     */
    public function pdfUpload(Book $book): Response
    {
        return Inertia::render('Admin/Books/PdfUpload', [
            'book' => $book,
            'current' => $book->pdf_path ? [
                'filename' => basename($book->pdf_path),
                'size' => Storage::disk('private')->size($book->pdf_path),
            ] : null,
        ]);
    }

    /**
     * Attach a new PDF to the book and queue PDF processing.
     *
     * Accepts either a direct `pdf_file` (small files) or a `temp_pdf_path`
     * produced by the chunked upload + merge endpoints. The old PDF is only
     * removed once the replacement is safely on the private disk.
     */
    public function storePdf(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'pdf_file' => ['nullable', 'mimes:pdf', 'max:512000'], // 500 MB direct (chunked for larger)
            'temp_pdf_path' => ['nullable', 'string', 'max:255', 'regex:/^books\/temp\/[a-zA-Z0-9\-]{8,64}\/[^\/]+$/'],
        ]);

        $pdfPath = null;

        if ($request->hasFile('pdf_file')) {
            // NEVER store on the 'public' disk. This path is only ever resolved
            // server-side through the signed, watermarked reader endpoint.
            $pdfPath = $request->file('pdf_file')->store('books/pdfs', 'private');
        } elseif (filled($validated['temp_pdf_path'])) {
            // promote() re-validates the client-supplied path (must be a direct
            // child of a books/temp upload dir) and sweeps the staging folder.
            $pdfPath = $this->uploads->promote(
                BookUploadController::PREFIX,
                $validated['temp_pdf_path'],
                'books/pdfs',
                BookUploadController::ALLOWED_EXTENSIONS,
            );
        }

        if (! $pdfPath) {
            return back()->with('error', 'No valid PDF was provided.');
        }

        if ($book->pdf_path && $book->pdf_path !== $pdfPath) {
            Storage::disk('private')->delete($book->pdf_path);
        }

        $book->forceFill([
            'pdf_path' => $pdfPath,
            'pdf_content_hash' => null,
            'processing_status' => 'pending',
            'processing_error' => null,
            'processing_started_at' => null,
            'processing_completed_at' => null,
        ])->save();

        ProcessBookPdf::dispatch($book->id);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'PDF uploaded. Processing started — this may take a few minutes.');
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
