<?php

namespace App\Services\Questions;

use App\Models\QuestionBank;
use App\Models\QuestionIngestionBatch;
use App\Models\QuestionSource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class PdfQuestionExtractorService
{
    public function __construct(
        protected QuestionAiStructuringService $aiStructuringService
    ) {}

    /**
     * Process an uploaded PDF question paper.
     */
    public function processPdf(
        UploadedFile|string $file,
        array $metadata = []
    ): array {
        $sourceId = $metadata['source_id'] ?? null;
        $title = $metadata['title'] ?? 'Uploaded Question Paper PDF';
        $segment = $metadata['segment'] ?? 'admission';
        $originTag = $metadata['origin_tag'] ?? 'PDF-24';
        $institution = $metadata['institution'] ?? null;
        $year = (int)($metadata['year'] ?? date('Y'));

        // Save file to storage
        $storedPath = null;
        if ($file instanceof UploadedFile) {
            $title = $title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $storedPath = $file->store('question-bank/pdfs', 'public');
            $fullPath = storage_path('app/public/' . $storedPath);
        } else {
            $fullPath = $file;
            $storedPath = str_replace(storage_path('app/public/'), '', $file);
        }

        // Create ingestion batch record
        $batch = QuestionIngestionBatch::create([
            'batch_type' => 'pdf_upload',
            'source_id' => $sourceId,
            'title' => $title,
            'file_path' => $storedPath,
            'status' => 'processing',
            'meta' => [
                'segment' => $segment,
                'origin_tag' => $originTag,
                'institution' => $institution,
                'year' => $year,
            ],
        ]);

        try {
            $rawText = $this->extractTextFromPdf($fullPath);

            if (empty(trim($rawText))) {
                $batch->update([
                    'status' => 'failed',
                    'error_log' => 'No readable text could be extracted from this PDF. It may be a scanned document requiring high-resolution OCR.',
                ]);

                return [
                    'success' => false,
                    'batch_id' => $batch->id,
                    'message' => 'PDF-এ কোনো রিডাবল টেক্সট পাওয়া যায়নি। এটি স্ক্যান করা ফাইল হতে পারে।',
                    'questions_count' => 0,
                    'questions' => [],
                ];
            }

            // Split into chunks of approx 6,000 characters
            $chunks = $this->splitTextIntoChunks($rawText, 6000);
            $allStructuredQuestions = [];

            foreach ($chunks as $chunk) {
                $hints = [
                    'segment' => $segment,
                    'origin_tag' => $originTag,
                    'institution' => $institution,
                    'year' => $year,
                    'ingestion_type' => 'pdf_ocr',
                ];

                $parsed = $this->aiStructuringService->structureQuestionsFromText($chunk, $hints);
                foreach ($parsed as $item) {
                    $item['source_id'] = $sourceId;
                    $allStructuredQuestions[] = $item;
                }
            }

            // Save structured questions to QuestionBank
            $savedCount = 0;
            foreach ($allStructuredQuestions as $qData) {
                QuestionBank::create($qData);
                $savedCount++;
            }

            if ($sourceId) {
                QuestionSource::where('id', $sourceId)->increment('questions_imported_count', $savedCount);
            }

            $batch->update([
                'status' => 'completed',
                'total_raw_items' => count($allStructuredQuestions),
                'parsed_count' => count($allStructuredQuestions),
                'success_count' => $savedCount,
            ]);

            return [
                'success' => true,
                'batch_id' => $batch->id,
                'message' => "সফলভাবে {$savedCount}টি প্রশ্ন নিষ্কাশন এবং প্রশ্ন ব্যাংকে সংরক্ষণ করা হয়েছে!",
                'questions_count' => $savedCount,
                'questions' => $allStructuredQuestions,
            ];
        } catch (\Throwable $e) {
            Log::error('PDF Question extraction failed: ' . $e->getMessage());

            $batch->update([
                'status' => 'failed',
                'error_log' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'batch_id' => $batch->id,
                'message' => 'প্রসেসিংয়ে ত্রুটি ঘটেছে: ' . $e->getMessage(),
                'questions_count' => 0,
                'questions' => [],
            ];
        }
    }

    /**
     * Extract text from PDF file.
     */
    private function extractTextFromPdf(string $filePath): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Throwable $e) {
            Log::warning('PdfParser failed, trying pdftotext CLI: ' . $e->getMessage());

            // Fallback to pdftotext if available on server
            if (function_exists('exec')) {
                $output = [];
                @exec("pdftotext \"{$filePath}\" -", $output);
                if (!empty($output)) {
                    return implode("\n", $output);
                }
            }

            return '';
        }
    }

    /**
     * Split text cleanly into chunks respecting paragraphs.
     */
    private function splitTextIntoChunks(string $text, int $maxChunkSize = 6000): array
    {
        $paragraphs = preg_split('/\n{2,}/u', $text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $para) {
            if (mb_strlen($current) + mb_strlen($para) > $maxChunkSize) {
                if (!empty(trim($current))) {
                    $chunks[] = trim($current);
                }
                $current = $para;
            } else {
                $current .= "\n\n" . $para;
            }
        }

        if (!empty(trim($current))) {
            $chunks[] = trim($current);
        }

        return !empty($chunks) ? $chunks : [$text];
    }
}
