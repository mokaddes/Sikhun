<?php

namespace App\Jobs;

use App\Models\ExamSession;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateExamQuestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    public function __construct(private int $examSessionId)
    {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $session = ExamSession::find($this->examSessionId);

        if (! $session) {
            return;
        }

        try {
            $provider = AiProviderFactory::default('exam_gen');
            $config = $session->config;

            $sourceText = $session->source_text
                ?: ($session->book ? $session->book->chunks()->orderBy('chunk_index')->limit(6)->pluck('content')->implode("\n") : '');

            $response = $provider->chat([
                ['role' => 'system', 'content' => $this->systemPrompt($config['type'], $config['count'])],
                ['role' => 'user', 'content' => "Generate questions based on this content:\n\n".mb_substr($sourceText, 0, 6000)],
            ], ['temperature' => 0.3, 'max_tokens' => 4000]);

            $questions = $this->parseQuestions($response);

            $session->update([
                'questions' => $questions,
                'total' => count($questions),
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $session->update(['status' => 'failed']);
        }
    }

    private function systemPrompt(string $type, int $count): string
    {
        return <<<PROMPT
        You are an expert Bangladeshi exam-question writer. Generate exactly {$count} "{$type}" questions
        based on the content the user provides. Questions and answers should be in Bengali unless the
        source content is in English. Return ONLY valid JSON, no markdown fences, no extra text, in this
        exact structure:
        {"questions": [{"id": 1, "question": "...", "type": "{$type}", "options": ["A","B","C","D"], "correct_answer": "...", "explanation": "..."}]}
        For non-MCQ types, omit "options" and set "correct_answer" to the expected answer text.
        PROMPT;
    }

    private function parseQuestions(string $response): array
    {
        $clean = preg_replace('/```json\s*|\s*```/', '', trim($response));
        $data = json_decode($clean, true);

        if (! is_array($data['questions'] ?? null)) {
            throw new \RuntimeException('AI returned an invalid question format.');
        }

        return $data['questions'];
    }
}
