<?php

namespace App\Console\Commands;

use App\Models\ExamAnswer;
use App\Models\QuestionBank;
use Illuminate\Console\Command;

class CleanCorruptedQuestionsCommand extends Command
{
    protected $signature = 'questions:clean-corrupted {--dry-run : Only show count without deleting}';
    protected $description = 'Find and safely delete corrupted, placeholder, or invalid scraped questions from question_banks.';

    public function handle(): int
    {
        $this->info('Scanning question_banks for corrupted records...');

        $query = QuestionBank::query()->where(function ($q) {
            // Placeholder options scraped as "বিকল্প ক", "বিকল্প খ", etc.
            $q->where(function ($sub) {
                $sub->where('options', 'like', '%"text":"বিকল্প ক"%')
                    ->where('options', 'like', '%"text":"বিকল্প খ"%');
            })
            // Or raw fallback Bengali placeholder option strings
            ->orWhere(function ($sub) {
                $sub->where('options', 'like', '%বিকল্প ক%')
                    ->where('options', 'like', '%বিকল্প খ%');
            })
            // Or question text is an index tag rather than real question
            ->orWhere(function ($sub) {
                $sub->where('question_text', 'like', '[প্রশ্ন%')
                    ->where('question_text', 'like', '%বহুনির্বাচনী প্রশ্নাবলী%');
            })
            // Or empty question text
            ->orWhereNull('question_text')
            ->orWhere('question_text', '=', '');
        });

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->info('No corrupted questions found in question_banks table! Database is clean.');
            return self::SUCCESS;
        }

        $this->warn("Found {$totalCount} corrupted/placeholder questions.");

        if ($this->option('dry-run')) {
            $sample = $query->limit(5)->get(['id', 'origin_tag', 'question_text']);
            foreach ($sample as $item) {
                $this->line("ID {$item->id} | {$item->origin_tag} | " . mb_substr($item->question_text, 0, 60));
            }
            $this->info('Dry-run completed. No records deleted.');
            return self::SUCCESS;
        }

        // Delete associated answers to maintain referential integrity
        $ids = $query->pluck('id');
        ExamAnswer::whereIn('question_bank_id', $ids)->delete();

        // Delete from question_banks
        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} corrupted questions from question_banks.");
        return self::SUCCESS;
    }
}
