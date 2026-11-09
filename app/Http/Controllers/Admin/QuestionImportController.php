<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FinalizeChunkedUpload;
use App\Jobs\ProcessQuestionPdf;
use App\Models\QuestionIngestionBatch;
use App\Models\QuestionSource;
use App\Services\ChunkedUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class QuestionImportController extends Controller
{
    public const PREFIX = 'question-bank';
    public const ALLOWED_EXTENSIONS = ['pdf'];

    public function __construct(
        protected ChunkedUploadService $uploads
    ) {}

    /**
     * Show PDF upload & OCR question extraction page.
     */
    public function showPdfUpload(): Response
    {
        $batches = QuestionIngestionBatch::with('source')
            ->latest('id')
            ->take(30)
            ->get();

        $sources = QuestionSource::active()->get(['id', 'name', 'institution']);

        return Inertia::render('Admin/Questions/Import/Pdf', [
            'batches' => $batches,
            'sources' => $sources,
        ]);
    }

    /**
     * Accept a single chunk of an uploaded PDF.
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB chunk limit
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'chunk_index' => ['required', 'integer', 'min:0', 'max:2048'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:2048'],
            'filename' => ['required', 'string', 'max:255', 'regex:/\.pdf$/i'],
        ]);

        if ((int) $validated['chunk_index'] >= (int) $validated['total_chunks']) {
            return response()->json(['ok' => false, 'message' => 'Invalid chunk index.'], 422);
        }

        $stored = $this->uploads->storeChunk(
            self::PREFIX,
            $validated['upload_id'],
            (int) $validated['chunk_index'],
            $validated['file'],
            (int) $validated['total_chunks'],
            $validated['filename']
        );

        return response()->json(['ok' => $stored]);
    }

    /**
     * Asynchronously merge uploaded chunks.
     */
    public function mergeChunks(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
        ]);

        FinalizeChunkedUpload::dispatch(
            self::PREFIX,
            $validated['upload_id'],
            self::ALLOWED_EXTENSIONS,
            500 * 1024 * 1024 // 500 MB max
        );

        return response()->json(['ok' => true, 'status' => 'merging']);
    }

    /**
     * Check chunk merge status.
     */
    public function mergeStatus(string $uploadId): JsonResponse
    {
        $status = $this->uploads->mergeStatus(self::PREFIX, $uploadId);

        return response()->json($status);
    }

    /**
     * Process uploaded PDF.
     */
    public function processPdf(Request $request): RedirectResponse
    {
        $request->validate([
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:512000'],
            'temp_pdf_path' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'segment' => ['required', 'string', 'in:admission,job'],
            'origin_tag' => ['required', 'string', 'max:50'],
            'institution' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2035'],
            'source_id' => ['nullable', 'exists:question_sources,id'],
        ]);

        $pdfPath = null;
        $title = $request->input('title');

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $title = $title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $pdfPath = $file->store('question-bank/pdfs', 'private');
        } elseif (filled($request->input('temp_pdf_path'))) {
            $pdfPath = $this->uploads->promote(
                self::PREFIX,
                $request->input('temp_pdf_path'),
                'question-bank/pdfs',
                self::ALLOWED_EXTENSIONS
            );
        }

        if (!$pdfPath) {
            return redirect()->back()->with('error', 'কোনো বৈধ পিডিএফ ফাইল পাওয়া যায়নি বা আপলোড ব্যর্থ হয়েছে।');
        }

        $batch = QuestionIngestionBatch::create([
            'batch_type' => 'pdf_upload',
            'source_id' => $request->input('source_id'),
            'title' => $title ?: 'Uploaded Question Paper PDF',
            'file_path' => $pdfPath,
            'status' => 'pending',
            'meta' => [
                'segment' => $request->input('segment'),
                'origin_tag' => strtoupper(trim($request->input('origin_tag'))),
                'institution' => $request->input('institution'),
                'year' => (int)($request->input('year') ?? date('Y')),
            ],
        ]);

        ProcessQuestionPdf::dispatch($batch->id)->onQueue('default');

        return redirect()->back()->with('success', 'PDF আপলোড সফল হয়েছে। ব্যাকগ্রাউন্ডে OCR ও প্রশ্ন নিষ্কাশন প্রসেস শুরু হয়েছে...');
    }

    /**
     * Retry processing for a batch.
     */
    public function reprocess(QuestionIngestionBatch $batch): RedirectResponse
    {
        if (!$batch->file_path || !Storage::disk('private')->exists($batch->file_path)) {
            return redirect()->back()->with('error', 'পিডিএফ ফাইলটি সার্ভারে পাওয়া যায়নি। নতুন করে আপলোড করুন।');
        }

        $batch->update([
            'status' => 'pending',
            'error_log' => null,
        ]);

        ProcessQuestionPdf::dispatch($batch->id, force: true)->onQueue('default');

        return redirect()->back()->with('success', "ব্যাচ #{$batch->id} পুনরায় প্রসেসিং শুরু হয়েছে।");
    }

    /**
     * Delete an ingestion batch and its associated PDF.
     */
    public function destroyBatch(QuestionIngestionBatch $batch): RedirectResponse
    {
        if ($batch->file_path && Storage::disk('private')->exists($batch->file_path)) {
            Storage::disk('private')->delete($batch->file_path);
        }

        $batch->delete();

        return redirect()->back()->with('success', "ব্যাচ #{$batch->id} সফলভাবে মুছে ফেলা হয়েছে।");
    }
}
