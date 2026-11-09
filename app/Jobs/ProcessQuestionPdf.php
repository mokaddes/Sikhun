<?php

namespace App\Jobs;

use App\Models\QuestionBank;
use App\Models\QuestionIngestionBatch;
use App\Models\QuestionSource;
use App\Services\Pdf\PdfParserManager;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessQuestionPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 2;

    public function __construct(
        public int $batchId,
        public bool $force = false
    ) {
        $this->onQueue('default');
    }

    public function handle(
        PdfParserManager $parsers,
        QuestionAiStructuringService $aiStructuringService
    ): void {
        $batch = QuestionIngestionBatch::find($this->batchId);

        if (!$batch) {
            Log::warning("ProcessQuestionPdf: Ingestion batch {$this->batchId} not found.");
            return;
        }

        if ($batch->status === 'completed' && !$this->force) {
            Log::info("ProcessQuestionPdf: Batch {$this->batchId} already completed.");
            return;
        }

        $batch->update([
            'status' => 'processing',
            'error_log' => null,
        ]);

        try {
            $parser = $parsers->resolve();
            Log::info("ProcessQuestionPdf: Parsing batch {$batch->id} via " . get_class($parser));

            $parsedDoc = $parser->parse($batch);

            // Assemble text page-by-page
            $pageTexts = [];
            foreach ($parsedDoc->pages as $page) {
                $content = trim($page['content'] ?? '');
                if (!empty($content)) {
                    $pageTexts[] = "=== PAGE {$page['page_number']} ===\n" . $content;
                }
            }

            $extractedText = implode("\n\n", $pageTexts);

            // Fallback to elements if page content was empty
            if (empty(trim($extractedText))) {
                $extractedText = collect($parsedDoc->elements)
                    ->pluck('content')
                    ->filter()
                    ->implode("\n");
            }

            if (empty(trim($extractedText))) {
                $batch->update([
                    'status' => 'failed',
                    'error_log' => 'PDF থেকে কোনো টেক্সট বা OCR রিড করা সম্ভব হয়নি। ফাইলটি স্ক্যান করা হলেও রেজোলিউশন কম বা অস্পষ্ট হতে পারে।',
                ]);
                return;
            }

            Log::info("ProcessQuestionPdf: Extracted " . mb_strlen($extractedText) . " characters from batch {$batch->id}. OCR used: " . ($parsedDoc->ocrUsed ? 'yes' : 'no'));

            // Split into chunks of approx 5,000 characters
            $chunks = $this->splitTextIntoChunks($extractedText, 5000);
            $allStructuredQuestions = [];

            $segment = $batch->meta['segment'] ?? 'admission';
            $originTag = $batch->meta['origin_tag'] ?? 'PDF-24';
            $institution = $batch->meta['institution'] ?? null;
            $year = (int)($batch->meta['year'] ?? date('Y'));

            foreach ($chunks as $chunk) {
                $hints = [
                    'segment' => $segment,
                    'origin_tag' => $originTag,
                    'institution' => $institution,
                    'year' => $year,
                    'ingestion_type' => 'pdf_ocr',
                ];

                $parsed = $aiStructuringService->structureQuestionsFromText($chunk, $hints);
                foreach ($parsed as $item) {
                    $item['source_id'] = $batch->source_id;
                    if (empty($item['student_types'])) {
                        $item['student_types'] = $segment === 'job' ? ['job'] : ['admission'];
                    }
                    if (empty($item['plan_ids'])) {
                        $item['plan_ids'] = ['all'];
                    }
                    $allStructuredQuestions[] = $item;
                }
            }

            // Save structured questions to QuestionBank
            $savedCount = 0;
            foreach ($allStructuredQuestions as $qData) {
                QuestionBank::create($qData);
                $savedCount++;
            }

            if ($batch->source_id && $savedCount > 0) {
                QuestionSource::where('id', $batch->source_id)->increment('questions_imported_count', $savedCount);
            }

            $ocrNote = $parsedDoc->ocrUsed
                ? "OCR ব্যবহূত হয়েছে ({$parsedDoc->ocrTool}, {$parsedDoc->ocrLang}, {$parsedDoc->ocrPages} পৃষ্ঠা)"
                : "ডিজিটাল টেক্সট এক্সট্রাক্ট ব্যবহূত হয়েছে";

            $batch->update([
                'status' => 'completed',
                'total_raw_items' => count($allStructuredQuestions),
                'parsed_count' => count($allStructuredQuestions),
                'success_count' => $savedCount,
                'error_log' => $ocrNote,
            ]);

            Log::info("ProcessQuestionPdf: Batch {$batch->id} completed successfully with {$savedCount} questions.");
        } catch (\Throwable $e) {
            Log::error("ProcessQuestionPdf failed for batch {$batch->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $batch->update([
                'status' => 'failed',
                'error_log' => mb_substr($e->getMessage(), 0, 500),
            ]);
        }
    }

    private function splitTextIntoChunks(string $text, int $maxChunkSize = 5000): array
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
