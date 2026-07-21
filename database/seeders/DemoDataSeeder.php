<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookShelf;
use App\Models\ExamSession;
use App\Models\LeaderboardEntry;
use App\Models\Student;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $books = Book::all();

        if ($books->isEmpty() || $students->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // Bookshelf: 1-3 random books
            foreach ($books->random(min(3, $books->count())) as $book) {
                BookShelf::updateOrCreate(
                    ['student_id' => $student->id, 'book_id' => $book->id],
                    ['source' => $book->is_free ? 'free' : 'purchased', 'added_at' => now()->subDays(rand(1, 30))]
                );
            }

            // One wallet recharge transaction
            $before = 0;
            $amount = rand(100, 500);
            WalletTransaction::create([
                'student_id' => $student->id,
                'type' => 'credit',
                'category' => 'wallet_recharge',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $before + $amount,
                'reference' => 'DEMO-'.strtoupper(Str::random(8)),
                'notes' => 'Demo wallet recharge',
            ]);

            // Two completed exam sessions -> leaderboard entries
            for ($i = 0; $i < 2; $i++) {
                $score = rand(6, 10);
                $total = 10;
                $completedAt = now()->subDays(rand(0, 20));

                $exam = ExamSession::create([
                    'student_id' => $student->id,
                    'source_type' => 'topic',
                    'source_text' => 'পদার্থবিজ্ঞান',
                    'config' => ['type' => 'mcq', 'count' => 10, 'duration' => 30, 'mode' => 'exam'],
                    'questions' => $this->fakeMcqQuestions(10),
                    'score' => $score,
                    'total' => $total,
                    'percentage' => ($score / $total) * 100,
                    'mode' => 'exam',
                    'status' => 'completed',
                    'started_at' => $completedAt->clone()->subMinutes(25),
                    'completed_at' => $completedAt,
                ]);

                LeaderboardEntry::create([
                    'student_id' => $student->id,
                    'exam_session_id' => $exam->id,
                    'subject' => 'physics',
                    'student_type' => $student->type,
                    'score' => $score,
                    'total' => $total,
                    'percentage' => ($score / $total) * 100,
                    'questions_count' => $total,
                    'week_number' => (int) $completedAt->format('W'),
                    'month_number' => (int) $completedAt->format('n'),
                    'year' => (int) $completedAt->format('Y'),
                ]);
            }
        }
    }

    private function fakeMcqQuestions(int $count): array
    {
        $questions = [];
        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'id' => $i,
                'question' => "ডেমো প্রশ্ন {$i}: নিউটনের কোন সূত্রে বল এবং ত্বরণের সম্পর্ক আলোচনা করা হয়?",
                'type' => 'mcq',
                'options' => ['প্রথম সূত্র', 'দ্বিতীয় সূত্র', 'তৃতীয় সূত্র', 'কোনোটি নয়'],
                'correct_answer' => 'দ্বিতীয় সূত্র',
                'explanation' => 'নিউটনের দ্বিতীয় সূত্র: F = ma',
            ];
        }

        return $questions;
    }
}
