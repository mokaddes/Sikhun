<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionIngestionBatch;
use App\Models\QuestionSource;
use App\Services\Questions\PdfQuestionExtractorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionImportController extends Controller
{
    public function __construct(
        protected PdfQuestionExtractorService $pdfExtractorService
    ) {}

    /**
     * Show PDF upload & OCR question extraction page.
     */
    public function showPdfUpload(): Response
    {
        $batches = QuestionIngestionBatch::with('source')
            ->latest('id')
            ->take(15)
            ->get();

        $sources = QuestionSource::active()->get(['id', 'name', 'institution']);

        return Inertia::render('Admin/Questions/Import/Pdf', [
            'batches' => $batches,
            'sources' => $sources,
        ]);
    }

    /**
     * Process uploaded PDF.
     */
    public function processPdf(Request $request): RedirectResponse
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:25600'], // max 25MB
            'title' => ['nullable', 'string', 'max:255'],
            'segment' => ['required', 'string', 'in:admission,job'],
            'origin_tag' => ['required', 'string', 'max:50'],
            'institution' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2035'],
            'source_id' => ['nullable', 'exists:question_sources,id'],
        ]);

        $result = $this->pdfExtractorService->processPdf(
            $request->file('pdf_file'),
            [
                'title' => $request->input('title'),
                'segment' => $request->input('segment'),
                'origin_tag' => strtoupper(trim($request->input('origin_tag'))),
                'institution' => $request->input('institution'),
                'year' => (int)($request->input('year') ?? date('Y')),
                'source_id' => $request->input('source_id'),
            ]
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
