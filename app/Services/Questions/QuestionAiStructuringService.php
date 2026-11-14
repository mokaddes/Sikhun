<?php

namespace App\Services\Questions;

use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuestionAiStructuringService
{
    /**
     * Generate new exam-standard questions via AI based on student type, subject, topic, and count.
     */
    public function generateQuestionsBatch(array $params): array
    {
        $count = (int)($params['count'] ?? 5);
        $count = max(1, min(25, $count));
        $studentType = $params['student_type'] ?? 'admission';
        $subjectSlug = $params['subject_slug'] ?? 'physics';
        $topicName = $params['topic_name'] ?? 'General';
        $originTag = $params['origin_tag'] ?? strtoupper($studentType) . '-25';
        $difficulty = $params['difficulty'] ?? 'medium';
        $institution = $params['institution'] ?? '';

        $subject = QuestionSubject::where('slug', $subjectSlug)->first() ?: QuestionSubject::first();
        $subjectName = $subject?->name_bn ?: 'সাধারণ';

        $systemPrompt = <<<PROMPT
You are Bangladesh's premier examination paper setter and curriculum specialist for {$studentType} students.
Generate exactly {$count} distinct, authentic, high-yield examination multiple choice questions (MCQ) for:
- Target Group: {$studentType}
- Subject: {$subjectName} ({$subjectSlug})
- Topic / Focus: {$topicName}
- Target Origin / Pattern: {$originTag}
- Difficulty: {$difficulty}

Rules:
1. Ensure the academic standard strictly aligns with actual Bangladeshi examinations for {$studentType} (e.g. Dhaka University/BUET/Medical for Admission, 46th/45th BCS or BB for Job, NCTB Board syllabus for SSC/HSC).
2. For mathematical/scientific questions, render formulas in KaTeX LaTeX notation ($...$ inline, $$...$$ display).
3. Provide 4 realistic options (A, B, C, D) with distinct plausible distractors.
4. Mark the single correct option key (e.g. "A").
5. Write a comprehensive, step-by-step conceptual explanation in Bengali explaining the solution and why other options are incorrect.

Return ONLY a valid JSON object without markdown fences, in this exact format:
{
  "questions": [
    {
      "question_text": "...",
      "question_type": "mcq",
      "options": [
        {"key": "A", "text": "..."},
        {"key": "B", "text": "..."},
        {"key": "C", "text": "..."},
        {"key": "D", "text": "..."}
      ],
      "correct_option": "A",
      "explanation": "...",
      "difficulty": "{$difficulty}"
    }
  ]
}
PROMPT;

        try {
            $provider = AiProviderFactory::default('question_gen', []);
            $response = $provider->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Generate {$count} questions for {$studentType} on {$subjectName} ({$topicName})."],
            ], [
                'temperature' => 0.4,
                'max_tokens' => 4500,
            ]);

            return $this->parseJsonResponse($response, [
                'segment' => in_array($studentType, ['job']) ? 'job' : 'admission',
                'origin_tag' => $originTag,
                'institution' => $institution ?: ($studentType === 'job' ? 'BPSC / Bank' : 'Public University'),
                'year' => (int)date('Y'),
                'ingestion_type' => 'ai_generated',
            ]);
        } catch (\Throwable $e) {
            Log::error('generateQuestionsBatch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Parses raw unstructured text (from scraper or PDF OCR) into structured question records.
     *
     * @param string $rawText
     * @param array $hints ['segment' => 'admission'|'job', 'origin_tag' => 'DU-24', 'institution' => '...', 'year' => 2024]
     * @return array<int, array>
     */
    public function structureQuestionsFromText(string $rawText, array $hints = []): array
    {
        if (trim($rawText) === '') {
            return [];
        }

        try {
            $provider = AiProviderFactory::default('question_structure', []);
            $subjectsList = QuestionSubject::active()->pluck('slug', 'name_bn')->toArray();
            $subjectsJson = json_encode($subjectsList, JSON_UNESCAPED_UNICODE);

            $hintSegment = $hints['segment'] ?? 'auto-detect';
            $hintOrigin = $hints['origin_tag'] ?? 'auto-detect';
            $hintInstitution = $hints['institution'] ?? '';
            $hintYear = $hints['year'] ?? '';

            $systemPrompt = <<<PROMPT
You are a premier Bangladeshi examination specialist and AI question architect.
Analyze the provided raw question text (which may be from Bangladesh University Admission tests or Job/BCS exams).
Extract all distinct questions and structure them into valid JSON with high academic precision.

Requirements:
1. Identify the primary segment: "admission" or "job".
2. Match to the best subject slug from available list: {$subjectsJson}.
3. Assign a subcategory/topic name in Bengali (e.g., "মহাকর্ষ ও অভিকর্ষ", "কারক ও বিভক্তি", "Subject-Verb Agreement").
4. Detect the exam origin tag: Format as DU-24, RU-23, BUET-23, BCS-46, BB-AD-24, COMB-BANK-23, DGME-24, etc.
   If hint is provided, use: segment="{$hintSegment}", origin_tag="{$hintOrigin}", institution="{$hintInstitution}".
5. Ensure question_text and options preserve all Bengali characters and render mathematical/scientific formulas in clean KaTeX LaTeX syntax (e.g., $E=mc^2$, $\lim_{x \\to 0} \\frac{\\sin x}{x}$).
6. Extract 4 options (A, B, C, D) or 5 if present. Provide clean key and text.
7. Determine the correct option ("A", "B", "C", "D", or "E").
8. Write a comprehensive, step-by-step conceptual explanation in Bengali explaining why the answer is correct, demonstrating calculations/grammar rules, and providing memory shortcuts.
9. Assess difficulty: "easy", "medium", or "hard".

Return ONLY a valid JSON object without markdown fences, in this exact format:
{
  "questions": [
    {
      "segment": "admission",
      "subject_slug": "physics",
      "topic_name": "মহাকর্ষ ও অভিকর্ষ",
      "institution": "University of Dhaka",
      "exam_name": "DU Ka Unit Admission Test",
      "origin_tag": "DU-24",
      "year": 2024,
      "session": "2023-2024",
      "question_text": "...",
      "question_type": "mcq",
      "options": [
        {"key": "A", "text": "..."},
        {"key": "B", "text": "..."}
      ],
      "correct_option": "A",
      "explanation": "...",
      "difficulty": "medium"
    }
  ]
}
PROMPT;

            $userContent = "Raw Content to Parse:\n\n" . mb_substr($rawText, 0, 7500);

            $response = $provider->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userContent],
            ], [
                'temperature' => 0.2,
                'max_tokens' => 4000,
            ]);

            return $this->parseJsonResponse($response, $hints);
        } catch (\Throwable $e) {
            Log::error('QuestionAiStructuringService error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generates a step-by-step conceptual explanation with formulas for a question.
     */
    public function generateAiExplanation(string $questionText, array $options, string $correctOption, ?string $subjectName = null): string
    {
        try {
            $provider = AiProviderFactory::default('question_analysis', []);

            $optionsStr = collect($options)->map(function ($opt) {
                $key = $opt['key'] ?? '';
                $text = $opt['text'] ?? '';
                return "{$key}. {$text}";
            })->implode("\n");

            $prompt = <<<PROMPT
You are an expert Bangladeshi teacher and academic exam specialist.
For the following question:
Question: {$questionText}
Subject: {$subjectName}
Options:
{$optionsStr}
Correct Option: {$correctOption}

Provide an in-depth, pedagogical, and conceptual explanation in Bengali.
- If mathematical or scientific, show full step-by-step formula derivations using LaTeX ($...$ and $$...$$).
- If grammatical, explain the underlying rule and why other options are incorrect.
- If general knowledge or literature, provide historical context and mnemonic tips.
Return only the explanation text without introductory chit-chat.
PROMPT;

            return $provider->chat([
                ['role' => 'user', 'content' => $prompt],
            ], ['temperature' => 0.3, 'max_tokens' => 1200]);
        } catch (\Throwable $e) {
            Log::error('generateAiExplanation error: ' . $e->getMessage());
            return 'সঠিক উত্তর ' . $correctOption . '। বিস্তারিত ব্যাখ্যা শীঘ্রই যুক্ত করা হবে।';
        }
    }

    /**
     * Helper to clean AI response string and parse JSON safely.
     */
    private function parseJsonResponse(string $rawResponse, array $fallbackHints = []): array
    {
        $cleaned = trim($rawResponse);
        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
        }

        $decoded = json_decode($cleaned, true);
        if (!is_array($decoded) || !isset($decoded['questions']) || !is_array($decoded['questions'])) {
            // Attempt to extract json object via regex
            if (preg_match('/\{[\s\S]*"questions"[\s\S]*\}/', $cleaned, $matches)) {
                $decoded = json_decode($matches[0], true);
            }
        }

        $items = $decoded['questions'] ?? [];
        $results = [];

        foreach ($items as $item) {
            if (empty($item['question_text']) || empty($item['options']) || empty($item['correct_option'])) {
                continue;
            }

            // Match subject
            $subjectSlug = $item['subject_slug'] ?? 'bangla';
            $subject = QuestionSubject::where('slug', $subjectSlug)->first()
                ?: QuestionSubject::first();

            $segment = in_array($item['segment'] ?? '', ['admission', 'job'])
                ? $item['segment']
                : ($fallbackHints['segment'] ?? ($subject?->segment === 'job' ? 'job' : 'admission'));

            $originTag = !empty($item['origin_tag'])
                ? strtoupper(trim($item['origin_tag']))
                : ($fallbackHints['origin_tag'] ?? 'GEN-24');

            $year = !empty($item['year'])
                ? (int)$item['year']
                : ($fallbackHints['year'] ?? (int)date('Y'));

            $results[] = [
                'segment' => $segment,
                'subject_id' => $subject?->id,
                'topic_name' => $item['topic_name'] ?? null,
                'institution' => $item['institution'] ?? ($fallbackHints['institution'] ?? null),
                'exam_name' => $item['exam_name'] ?? null,
                'origin_tag' => $originTag,
                'year' => $year,
                'session' => $item['session'] ?? null,
                'shift_or_set' => $item['shift_or_set'] ?? null,
                'question_text' => $item['question_text'],
                'question_type' => $item['question_type'] ?? 'mcq',
                'options' => $item['options'],
                'correct_option' => strtoupper(trim($item['correct_option'])),
                'explanation' => $item['explanation'] ?? null,
                'marks' => 1.00,
                'negative_marks' => $segment === 'job' ? 0.50 : 0.25,
                'difficulty' => in_array($item['difficulty'] ?? '', ['easy', 'medium', 'hard']) ? $item['difficulty'] : 'medium',
                'ingestion_type' => $fallbackHints['ingestion_type'] ?? 'ai_generated',
                'status' => 'published',
                'verification_status' => 'verified',
            ];
        }

        return $results;
    }
}
