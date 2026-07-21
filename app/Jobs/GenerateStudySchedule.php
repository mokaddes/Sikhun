<?php

namespace App\Jobs;

use App\Models\StudySchedule;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateStudySchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'ai';

    public $tries = 3;

    public $timeout = 90;

    public function __construct(private int $scheduleId) {}

    public function handle(): void
    {
        $schedule = StudySchedule::find($this->scheduleId);

        if (! $schedule) {
            return;
        }

        try {
            $provider = AiProviderFactory::default('schedule_gen');
            $config = $schedule->config;
            $daysUntilExam = now()->diffInDays($schedule->exam_date);

            $response = $provider->chat([
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => json_encode([
                    'days_until_exam' => $daysUntilExam,
                    'subjects' => $config['subjects'],
                    'weak_subjects' => $config['weak_subjects'] ?? [],
                    'hours_per_day' => $config['hours_per_day'],
                    'style' => $config['style'],
                    'include_weekends' => $config['include_weekends'] ?? true,
                    'start_date' => now()->toDateString(),
                ])],
            ], ['temperature' => 0.4, 'max_tokens' => 3000]);

            $clean = preg_replace('/```json\s*|\s*```/', '', trim($response));
            $data = json_decode($clean, true);

            if (! is_array($data['days'] ?? null)) {
                throw new \RuntimeException('AI returned an invalid schedule format.');
            }

            $schedule->update(['schedule_data' => $data['days'], 'status' => 'active']);
        } catch (\Throwable $e) {
            $schedule->update(['status' => 'failed']);
        }
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
        You are an expert study planner for Bangladeshi HSC/SSC/university/job-prep students.
        Given exam info as JSON, produce a day-by-day study plan from today until the exam date.
        Give weak subjects proportionally more days. Return ONLY valid JSON, no markdown fences:
        {"days": [{"date": "YYYY-MM-DD", "subject": "...", "topic": "...", "hours": 2, "tip": "..."}]}
        Write subject/topic/tip in Bengali unless the subjects given are in English.
        PROMPT;
    }
}
