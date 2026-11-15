<?php

namespace App\Console\Commands;

use App\Models\QuestionBank;
use App\Models\QuestionIngestionBatch;
use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ImportJobSolutionQuestions extends Command
{
    protected $signature = 'question-bank:import-job-solution 
                            {--file= : Absolute path to the PDF file}
                            {--batch-id= : Ingestion batch ID if already created}
                            {--force-ocr : Force re-run Poppler+Tesseract OCR even if cached}';

    protected $description = 'Ingest Job Solution PDF questions (e.g. Recent-job-solution-2026.pdf) into Job Question Bank via Poppler+Tesseract and AI structuring';

    public function handle(QuestionAiStructuringService $aiService): int
    {
        $this->info("=== Sikhun Job Question Bank Ingestion Pipeline ===");

        $defaultPath = storage_path('app/private/question-bank/pdfs/recent-job-solution-2026.pdf');
        $filePath = $this->option('file') ?: $defaultPath;

        if (! file_exists($filePath)) {
            $this->error("PDF file not found at: {$filePath}");
            return 1;
        }

        $fileSizeMb = round(filesize($filePath) / (1024 * 1024), 2);
        $this->line("Target PDF: {$filePath} ({$fileSizeMb} MB)");

        // 1. Create or load Ingestion Batch
        $batchId = $this->option('batch-id');
        if ($batchId) {
            $batch = QuestionIngestionBatch::find($batchId);
        } else {
            $batch = QuestionIngestionBatch::firstOrCreate([
                'file_path' => 'question-bank/pdfs/' . basename($filePath),
            ], [
                'batch_type' => 'pdf_upload',
                'title' => 'Recent Job Solution 2026',
                'status' => 'processing',
                'meta' => [
                    'segment' => 'job',
                    'origin_tag' => 'JOB-26',
                    'institution' => 'বিভিন্ন সরকারি দপ্তর ও ব্যাংক',
                    'year' => 2026,
                ],
            ]);
        }

        $batch->update([
            'status' => 'processing',
            'error_log' => 'OCR ও টেক্সট এক্সট্রাকশন চলমান...',
        ]);

        // 2. OCR via Node Worker (Poppler + Tesseract eng+ben)
        $workDir = storage_path('app/pdf-parsing/job-solution-2026');
        if (! is_dir($workDir)) {
            @mkdir($workDir, 0775, true);
        }

        $manifestFile = $workDir . '/manifest.json';
        $pageFiles = glob($workDir . '/page-*.json');

        if ($this->option('force-ocr') || empty($pageFiles) || ! file_exists($manifestFile)) {
            $this->info("Running multi-core Poppler + Tesseract OCR on {$filePath}...");
            $workerPath = base_path('pdf-worker/parse.js');

            $cmd = [
                'node',
                $workerPath,
                '--input', $filePath,
                '--output', $workDir,
                '--ocr-dpi', '150',
                '--tesseract', 'tesseract',
                '--pdftoppm', 'pdftoppm',
                '--ocr-lang', 'eng+ben',
            ];

            $process = Process::timeout(7200)->run($cmd);

            if (! $process->successful()) {
                $err = "OCR Worker failed: " . Str::limit($process->errorOutput(), 500);
                $this->error($err);
                $batch->update(['status' => 'failed', 'error_log' => $err]);
                return 1;
            }

            $pageFiles = glob($workDir . '/page-*.json');
            $this->info("OCR completed! Generated " . count($pageFiles) . " page files.");
        } else {
            $this->info("Using cached OCR page files (" . count($pageFiles) . " pages found in {$workDir}).");
        }

        sort($pageFiles, SORT_NATURAL);

        // Preload Subject Slugs for fast subject assignment
        $subjectsBySlug = QuestionSubject::all()->keyBy('slug');
        $subjectIdMap = [
            'bangla' => $subjectsBySlug['bangla']?->id ?? 1,
            'english' => $subjectsBySlug['english']?->id ?? 2,
            'mathematics' => $subjectsBySlug['mathematics']?->id ?? ($subjectsBySlug['math-reasoning']?->id ?? 7),
            'bangladesh-affairs' => $subjectsBySlug['bangladesh-affairs']?->id ?? 8,
            'international-affairs' => $subjectsBySlug['international-affairs']?->id ?? 9,
            'computer-ict' => $subjectsBySlug['computer-ict']?->id ?? 10,
            'general-science' => $subjectsBySlug['general-science']?->id ?? 11,
            'ethics-governance' => $subjectsBySlug['ethics-governance']?->id ?? 12,
            'general-knowledge' => $subjectsBySlug['general-knowledge']?->id ?? 14,
        ];

        // 3. Assemble and parse pages
        $this->info("Extracting exam question blocks and structuring questions...");

        $totalExtracted = 0;
        $totalSaved = 0;
        $currentExam = 'সাম্প্রতিক জব সলিউশন পরীক্ষা';
        $currentOrigin = 'JOB-26';
        $currentInstitution = 'সরকারি চাকরি ও ব্যাংক নিয়োগ';

        $unstructuredChunks = [];
        $currentChunkText = '';

        $bar = $this->output->createProgressBar(count($pageFiles));
        $bar->start();

        foreach ($pageFiles as $pageFile) {
            $data = json_decode(file_get_contents($pageFile), true);
            $content = trim($data['text'] ?? $data['content'] ?? '');
            if (empty($content)) {
                $bar->advance();
                continue;
            }

            // Check if page header mentions a specific exam
            if (preg_match('/(?:পরীক্ষার নাম|পদের নাম|মন্ত্রণালয়|অধিদপ্তর|ব্যাংক|বিসিএস|নিয়োগ পরীক্ষা|কমিশন|বোর্ড|প্রাথমিক)[:\-–\s]+([^\n]+)/u', $content, $examMatch)) {
                $foundExam = trim($examMatch[1]);
                if (mb_strlen($foundExam) > 4 && mb_strlen($foundExam) < 100) {
                    $currentExam = $foundExam;
                    if (preg_match('/([০-৯0-9]+)\s*তম\s*বিসিএস/u', $currentExam, $bcsM)) {
                        $currentOrigin = 'BCS-' . $bcsM[1];
                    } elseif (preg_match('/বাংলাদেশ\s*ব্যাংক/u', $currentExam)) {
                        $currentOrigin = 'BB-26';
                    } elseif (preg_match('/সমন্বিত\s*([০-৯0-9]+)\s*ব্যাংক/u', $currentExam, $bkM)) {
                        $currentOrigin = 'COMB-BANK-26';
                    } else {
                        $currentOrigin = 'JOB-26';
                    }
                }
            }

            // Try fast regex extraction of standardized MCQ blocks:
            // e.g. "১. প্রশ্ন? (ক) অপশন ১ (খ) অপশন ২ (গ) অপশন ৩ (ঘ) অপশন ৪ উত্তর: ক ব্যাখ্যা: ..."
            $parsedQuestions = $this->extractQuestionsFromPageText($content, [
                'exam_name' => $currentExam,
                'origin_tag' => $currentOrigin,
                'institution' => $currentInstitution,
                'subjectIdMap' => $subjectIdMap,
            ]);

            if (! empty($parsedQuestions)) {
                foreach ($parsedQuestions as $q) {
                    QuestionBank::create($q);
                    $totalSaved++;
                }
                $totalExtracted += count($parsedQuestions);
            } else {
                // Accumulate into text chunk for AI parsing
                $currentChunkText .= "\n=== EXAM: {$currentExam} (ORIGIN: {$currentOrigin}) ===\n" . $content;
                if (mb_strlen($currentChunkText) > 6000) {
                    $unstructuredChunks[] = [
                        'text' => $currentChunkText,
                        'hints' => [
                            'segment' => 'job',
                            'origin_tag' => $currentOrigin,
                            'institution' => $currentInstitution,
                            'year' => 2026,
                        ],
                    ];
                    $currentChunkText = '';
                }
            }

            $bar->advance();
        }

        if (! empty(trim($currentChunkText))) {
            $unstructuredChunks[] = [
                'text' => $currentChunkText,
                'hints' => [
                    'segment' => 'job',
                    'origin_tag' => $currentOrigin,
                    'institution' => $currentInstitution,
                    'year' => 2026,
                ],
            ];
        }

        $bar->finish();
        $this->newLine();

        $this->info("Regex pass saved {$totalSaved} questions directly!");

        // 4. Process unstructured chunks with AI structuring service
        if (! empty($unstructuredChunks)) {
            $this->info("Processing " . count($unstructuredChunks) . " remaining chunks with AI Question Structuring Service...");

            $aiBar = $this->output->createProgressBar(count($unstructuredChunks));
            $aiBar->start();

            foreach ($unstructuredChunks as $chunkItem) {
                try {
                    $aiQuestions = $aiService->structureQuestionsFromText($chunkItem['text'], $chunkItem['hints']);
                    foreach ($aiQuestions as $qData) {
                        $qData['segment'] = 'job';
                        $qData['allowed_student_types'] = ['job'];
                        $qData['allowed_plan_ids'] = ['all'];
                        $qData['is_public_to_all'] = false;
                        $qData['status'] = 'published';
                        $qData['verification_status'] = 'verified';
                        $qData['ingestion_type'] = 'pdf_ocr';

                        QuestionBank::create($qData);
                        $totalSaved++;
                        $totalExtracted++;
                    }
                } catch (\Throwable $e) {
                    Log::warning("AI Question Structuring batch error: " . $e->getMessage());
                }
                $aiBar->advance();
            }

            $aiBar->finish();
            $this->newLine();
        }

        // 5. Finalize Batch Record
        $batch->update([
            'status' => 'completed',
            'total_raw_items' => $totalExtracted,
            'parsed_count' => $totalExtracted,
            'success_count' => $totalSaved,
            'error_log' => "Poppler + Tesseract OCR সম্পন্ন (" . count($pageFiles) . " পৃষ্ঠা থেকে {$totalSaved}টি প্রশ্ন সংরক্ষিত)",
        ]);

        $this->info("SUCCESS! Total {$totalSaved} questions imported into Job Question Bank.");
        return 0;
    }

    /**
     * Parse MCQ blocks with Bengali/English numbering, options, and explanations.
     */
    private function extractQuestionsFromPageText(string $text, array $context): array
    {
        $questions = [];

        // Match patterns like:
        // ১. / ১। প্রশ্ন... (ক)/(a) opt1 (খ)/(b) opt2 (গ)/(c) opt3 (ঘ)/(d) opt4 [উত্তর: ক/A] [ব্যাখ্যা: ...]
        $pattern = '/(?:^|\n)([০-৯0-9]+)\s*[\.\-\)\|।\)]\s*([^\n\(\)]+)\s*(?:\n|\s+)(?:\([কaA]\)|[কaA][\.\)])\s*([^\n\(\)]+)\s*(?:\([খbB]\)|[খbB][\.\)])\s*([^\n\(\)]+)\s*(?:\([গcC]\)|[গcC][\.\)])\s*([^\n\(\)]+)\s*(?:\([ঘdD]\)|[ঘdD][\.\)])\s*([^\n\(\)]+)(?:[\s\S]*?(?:উত্তর|Ans|Answer|উঃ)\s*[:\-–\s]*([কখগঘABCDabcd]))?(?:[\s\S]*?(?:ব্যাখ্যা|Explanation)\s*[:\-–\s]*([^\n]+(?:\n(?![০-৯0-9]+\s*[\.\-\)\|।\)]).*)*))?/u';

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $qNum = trim($m[1]);
                $qText = trim($m[2]);
                $optA = trim($m[3]);
                $optB = trim($m[4]);
                $optC = trim($m[5]);
                $optD = trim($m[6]);
                $ansRaw = isset($m[7]) ? trim($m[7]) : 'A';
                $explanation = isset($m[8]) ? trim($m[8]) : '';

                // Normalize answer key to A, B, C, D
                $ansKey = match ($ansRaw) {
                    'ক', 'a', 'A' => 'A',
                    'খ', 'b', 'B' => 'B',
                    'গ', 'c', 'C' => 'C',
                    'ঘ', 'd', 'D' => 'D',
                    default => 'A',
                };

                // Detect subject from text content
                $subjectId = $this->detectSubjectId($qText, $context['subjectIdMap']);

                $questions[] = [
                    'segment' => 'job',
                    'allowed_student_types' => ['job'],
                    'allowed_plan_ids' => ['all'],
                    'is_public_to_all' => false,
                    'subject_id' => $subjectId,
                    'topic_name' => 'বিসিএস ও সরকারি নিয়োগ প্রশ্নসমাধান',
                    'institution' => $context['institution'],
                    'exam_name' => $context['exam_name'],
                    'origin_tag' => $context['origin_tag'],
                    'year' => 2026,
                    'question_text' => $qText,
                    'question_type' => 'mcq',
                    'options' => [
                        ['key' => 'A', 'text' => $optA],
                        ['key' => 'B', 'text' => $optB],
                        ['key' => 'C', 'text' => $optC],
                        ['key' => 'D', 'text' => $optD],
                    ],
                    'correct_option' => $ansKey,
                    'explanation' => $explanation ?: "সঠিক উত্তর ({$ansKey})।",
                    'marks' => 1.00,
                    'negative_marks' => 0.25,
                    'difficulty' => 'medium',
                    'ingestion_type' => 'pdf_ocr',
                    'status' => 'published',
                    'verification_status' => 'verified',
                ];
            }
        }

        return $questions;
    }

    private function detectSubjectId(string $text, array $idMap): int
    {
        if (preg_match('/(?:বাংলা|চর্যাপদ|উপন্যাস|সন্ধি|সমাস|কারক|ব্যাকরণ|বাগধারা|বিপরীত শব্দ|সাহিত্য|রবীন্দ্রনাথ|নজরুল|বঙ্কিম|মাইকেল)/u', $text)) {
            return $idMap['bangla'];
        }
        if (preg_match('/(?:English|Grammar|Preposition|Synonym|Antonym|Tense|Idiom|Voice|Clause|Sentence|Noun|Verb|Adjective)/i', $text)) {
            return $idMap['english'];
        }
        if (preg_match('/(?:গণিত|সুদ|শতকরা|অনুপাত|ত্রিভুজ|বৃত্ত|বীজগণিত|মান নির্ণয়|উৎপাদক|কোণ|ভগ্নাংশ|বর্গ|সমীকরণ)/u', $text)) {
            return $idMap['mathematics'];
        }
        if (preg_match('/(?:বাংলাদেশ|মুক্তিযুদ্ধ|বঙ্গবন্ধু|সংবিধান|মুদ্রা|জেলা|নদী|পদ্মা সেতু|উপজাতি|জাতীয় সংসদ|ঐতিহাসিক)/u', $text)) {
            return $idMap['bangladesh-affairs'];
        }
        if (preg_match('/(?:আন্তর্জাতিক|জাতিসংঘ|ইউরোপ|বিশ্ব|রাজধানী|নোবেল|ন্যাটো|সার্ক|পররাষ্ট্র|যুদ্ধ|চুক্তি)/u', $text)) {
            return $idMap['international-affairs'];
        }
        if (preg_match('/(?:কম্পিউটার|মেমোরি|ইন্টারনেট|সফটওয়্যার|বাইট|CPU|RAM|ROM|নেটওয়ার্ক|বাইনারি|ওয়েব)/iu', $text)) {
            return $idMap['computer-ict'];
        }
        if (preg_match('/(?:বিজ্ঞান|ভিটামিন|কোষ|রক্ত|গ্রহ|সূর্য|শব্দ|আলো|পরমাণু|বায়ুমণ্ডল|রোগ|হরমোন)/u', $text)) {
            return $idMap['general-science'];
        }
        if (preg_match('/(?:নৈতিকতা|মূল্যবোধ|সুশাসন)/u', $text)) {
            return $idMap['ethics-governance'];
        }

        return $idMap['general-knowledge'];
    }
}
