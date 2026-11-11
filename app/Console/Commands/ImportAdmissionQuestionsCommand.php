<?php

namespace App\Console\Commands;

use App\Services\Questions\AdmissionQuestionImportService;
use App\Services\Questions\QuestionJsonImportService;
use Illuminate\Console\Command;

class ImportAdmissionQuestionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'questions:import-admission
                            {file? : Specific file name in qsn-json directory or absolute path}
                            {--dry-run : Parse and validate without writing to database}
                            {--limit= : Maximum number of exams to process}
                            {--status=published : Default status (published or draft)}
                            {--cross-link : Cross-link repeating questions across exams}';

    /**
     * The console command description.
     */
    protected $description = 'Import university admission questions from crawled JSON into QuestionBank';

    public function handle(
        AdmissionQuestionImportService $importer,
        QuestionJsonImportService $generalImporter
    ): int {
        $fileArg = $this->argument('file') ?? 'chorcha_crawled_454_exams_master.json';
        $defaultDir = base_path('qsn-json');

        $filePath = file_exists($fileArg) ? $fileArg : $defaultDir . DIRECTORY_SEPARATOR . $fileArg;
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $dryRun = (bool)$this->option('dry-run');
        $limit = $this->option('limit') ? (int)$this->option('limit') : null;
        $status = $this->option('status') ?: 'published';

        $fileSizeMb = round(filesize($filePath) / 1024 / 1024, 2);
        $this->info("==================================================================");
        $this->info("  SIKHUN - ADMISSION QUESTION BANK IMPORTER");
        $this->info("==================================================================");
        $this->line("Target File: <comment>" . basename($filePath) . "</comment> ({$fileSizeMb} MB)");
        $this->line("Mode:        " . ($dryRun ? "<fg=yellow;options=bold>DRY RUN (No DB Writes)</>" : "<fg=green;options=bold>LIVE IMPORT</>"));
        $this->line("Status:      <info>{$status}</info>");
        if ($limit) {
            $this->line("Exam Limit:  <comment>{$limit}</comment>");
        }
        $this->newLine();

        $progressBar = null;

        try {
            $options = [
                'dry_run' => $dryRun,
                'limit' => $limit,
                'status' => $status,
            ];

            $result = $importer->importFile(
                $filePath,
                $options,
                function ($current, $total, $examName, $inserted) use (&$progressBar) {
                    if (!$progressBar) {
                        $progressBar = $this->output->createProgressBar($total);
                        $progressBar->setFormat(" %current%/%max% [%bar%] %percent:3s%% -- %message%");
                        $progressBar->start();
                    }
                    $progressBar->setMessage(mb_substr($examName, 0, 45));
                    $progressBar->advance();
                }
            );

            if ($progressBar) {
                $progressBar->finish();
                $this->newLine(2);
            }

            // Results Summary
            $this->info("==================================================================");
            $this->info("                     IMPORT RESULTS SUMMARY                      ");
            $this->info("==================================================================");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Exams Processed', number_format($result['total_exams'])],
                    ['Total Questions in File', number_format($result['total_questions_in_file'])],
                    ['Questions Inserted', number_format($result['total_inserted'])],
                    ['Questions Skipped (Duplicates/Empty)', number_format($result['total_skipped'])],
                ]
            );

            // University breakdown table
            if (!empty($result['university_stats'])) {
                $this->newLine();
                $this->info("--- University Breakdown ---");
                $uniRows = [];
                foreach ($result['university_stats'] as $uni => $count) {
                    $uniRows[] = [$uni, number_format($count)];
                }
                $this->table(['University / Institution', 'Questions'], $uniRows);
            }

            // Subject breakdown table
            if (!empty($result['subject_stats'])) {
                $this->newLine();
                $this->info("--- Canonical Subject Breakdown ---");
                $subjRows = [];
                foreach ($result['subject_stats'] as $slug => $count) {
                    $subjRows[] = [$slug, number_format($count)];
                }
                $this->table(['Subject', 'Questions'], $subjRows);
            }

            // Year distribution table
            if (!empty($result['year_stats'])) {
                $this->newLine();
                $this->info("--- Exam Year Breakdown ---");
                $yearRows = [];
                foreach ($result['year_stats'] as $yr => $count) {
                    $yearRows[] = [(string)$yr, number_format($count)];
                }
                $this->table(['Year', 'Questions'], $yearRows);
            }

            // Cross-linking repeated questions if requested
            if (!$dryRun && $this->option('cross-link')) {
                $this->newLine();
                $this->info("Cross-linking repeated questions across multiple exams...");
                $crossCount = $generalImporter->syncCrossExamReferences();
                $this->info("✔ Cross-linked {$crossCount} question occurrences with full exam history (appeared_in).");
            }

            $this->newLine();
            if ($dryRun) {
                $this->warn("Dry run completed successfully. No records were written to the database.");
            } else {
                $this->info("✔ Admission question bank import completed successfully!");
            }

            return 0;
        } catch (\Throwable $e) {
            $this->error("Error during import: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
