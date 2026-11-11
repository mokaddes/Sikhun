<?php

namespace App\Console\Commands;

use App\Services\Questions\QuestionJsonImportService;
use Illuminate\Console\Command;

class ImportQuestionsJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'questions:import-json 
                            {file? : Specific file name or full path in qsn-json directory}
                            {--segment= : Override segment (job, admission, hsc, ssc)}
                            {--status=active : Default status (active or draft)}
                            {--sync-repeats : Sync and cross-link duplicate questions across multiple exams}';

    /**
     * The console command description.
     */
    protected $description = 'Import questions from JSON files in qsn-json directory or uploaded path into QuestionBank';

    public function handle(QuestionJsonImportService $importer): int
    {
        $fileArg = $this->argument('file');
        $defaultDir = base_path('qsn-json');

        $filesToProcess = [];

        if ($fileArg) {
            $path = file_exists($fileArg) ? $fileArg : $defaultDir . DIRECTORY_SEPARATOR . $fileArg;
            if (!file_exists($path)) {
                $this->error("File not found: {$path}");
                return 1;
            }
            $filesToProcess[] = $path;
        } else {
            // Find all .json files in qsn-json
            if (!is_dir($defaultDir)) {
                $this->error("Directory not found: {$defaultDir}");
                return 1;
            }
            $all = glob($defaultDir . DIRECTORY_SEPARATOR . '*.json');
            if (empty($all)) {
                $this->warn("No .json files found in {$defaultDir}");
                return 0;
            }
            $filesToProcess = $all;
        }

        $options = [
            'segment' => $this->option('segment'),
            'status' => $this->option('status'),
        ];

        $this->info("Found " . count($filesToProcess) . " file(s) to process.");

        $overallExams = 0;
        $overallQuestions = 0;
        $overallInserted = 0;
        $overallSkipped = 0;

        foreach ($filesToProcess as $filePath) {
            $baseName = basename($filePath);
            $this->line("");
            $this->info("Processing: <comment>{$baseName}</comment> (" . round(filesize($filePath) / 1024 / 1024, 2) . " MB)");

            $progressBar = null;

            try {
                $result = $importer->importFile($filePath, $options, function ($current, $total, $examName, $inserted) use (&$progressBar) {
                    if (!$progressBar) {
                        $progressBar = $this->output->createProgressBar($total);
                        $progressBar->start();
                    }
                    $progressBar->advance();
                });

                if ($progressBar) {
                    $progressBar->finish();
                    $this->newLine();
                }

                $this->info("✔ Completed {$baseName}:");
                $this->line("   - Exams Processed: <info>{$result['total_exams']}</info>");
                $this->line("   - Questions in File: <info>{$result['total_questions_in_file']}</info>");
                $this->line("   - Inserted: <info>{$result['total_inserted']}</info>");
                $this->line("   - Skipped / Duplicate: <comment>{$result['total_skipped']}</comment>");

                $overallExams += $result['total_exams'];
                $overallQuestions += $result['total_questions_in_file'];
                $overallInserted += $result['total_inserted'];
                $overallSkipped += $result['total_skipped'];
            } catch (\Throwable $e) {
                $this->error("Error importing {$baseName}: " . $e->getMessage());
                $this->error($e->getTraceAsString());
            }
        }

        if ($this->option('sync-repeats')) {
            $this->info("Cross-linking repeated questions across exams...");
            $crossCount = $importer->syncCrossExamReferences();
            $this->info("✔ Cross-linked {$crossCount} question occurrences with full exam history (appeared_in).");
        }

        $this->line("");
        $this->info("==========================================");
        $this->info("GRAND TOTAL SUMMARY:");
        $this->info("  Total Exams:     {$overallExams}");
        $this->info("  Total Questions: {$overallQuestions}");
        $this->info("  Total Inserted:  {$overallInserted}");
        $this->info("  Total Skipped:   {$overallSkipped}");
        $this->info("==========================================");

        return 0;
    }
}
