<?php

namespace App\Jobs;

use App\Models\EssaySubmission;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GradeEssay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 90;

    public function __construct(private int $submissionId)
    {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $submission = EssaySubmission::find($this->submissionId);

        if (! $submission) {
            return;
        }

        try {
            $provider = AiProviderFactory::default('essay_grade');

            $response = $provider->chat([
                ['role' => 'system', 'content' => $this->systemPrompt($submission->grading_type)],
                ['role' => 'user', 'content' => $submission->essay_text],
            ], ['temperature' => 0.2, 'max_tokens' => 3000]);

            $clean = preg_replace('/```json\s*|\s*```/', '', trim($response));
            $result = json_decode($clean, true);

            if (! is_array($result)) {
                throw new \RuntimeException('AI returned an invalid grading format.');
            }

            $submission->update(['result' => $result, 'status' => 'completed']);
        } catch (\Throwable $e) {
            $submission->update(['status' => 'failed']);
        }
    }

    private function systemPrompt(string $type): string
    {
        $rubricLabel = match ($type) {
            'hsc_bangla' => 'HSC-level Bengali essay writing',
            'hsc_english' => 'HSC-level English essay writing',
            'custom_rubric' => 'general academic writing',
            default => 'general writing',
        };

        return <<<PROMPT
        You are an expert {$rubricLabel} evaluator for Bangladeshi students. Grade the essay the user
        submits. Return ONLY valid JSON, no markdown fences, in this exact structure:
        {
          "total_score": 85, "max_score": 100,
          "breakdown": {
            "content": {"score": 30, "max": 35, "feedback": "..."},
            "language": {"score": 25, "max": 30, "feedback": "..."},
            "structure": {"score": 20, "max": 25, "feedback": "..."},
            "originality": {"score": 10, "max": 10, "feedback": "..."}
          },
          "overall_feedback": "...", "strengths": ["...", "..."], "improvements": ["...", "..."]
        }
        Write all feedback in the same language as the essay.
        PROMPT;
    }
}
