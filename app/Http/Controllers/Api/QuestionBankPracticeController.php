<?php

namespace App\Http\Controllers\Api;

use App\Models\ExamSession;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Services\AccessGrantService;
use App\Services\Questions\QuestionAiStructuringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionBankPracticeController extends BaseApiController
{
    private function resolveAllowedSector(?Request $request = null): string
    {
        $validSectors = ['admission', 'job', 'hsc', 'ssc'];
        $requested = $request?->input('sector');
        if ($requested && in_array($requested, $validSectors, true)) {
            return $requested;
        }

        $student = auth('sanctum')->user();
        $type = $student?->type ?? '';

        if (in_array($type, $validSectors, true) && QuestionBank::published()->where('segment', $type)->exists()) {
            return $type;
        }

        foreach (['admission', 'hsc', 'job', 'ssc'] as $candidate) {
            if (QuestionBank::published()->where('segment', $candidate)->exists()) {
                return $candidate;
            }
        }

        return 'admission';
    }

    public function index(Request $request): JsonResponse
    {
        $student = auth('sanctum')->user();
        $sector = $this->resolveAllowedSector($request);

        $query = QuestionBank::query()
            ->with(['subject', 'topic'])
            ->published()
            ->where('segment', $sector);

        if ($request->filled('origin_tag')) {
            $query->where('origin_tag', $request->input('origin_tag'));
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->input('subject_id'));
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(fn ($q) => $q->where('question_text', 'like', "%{$search}%")
                ->orWhere('origin_tag', 'like', "%{$search}%")
                ->orWhere('exam_name', 'like', "%{$search}%"));
        }

        $questions = $query->latest()->paginate(20);
        $isPaid = (bool) ($student?->activeSubscription || ($student && app(AccessGrantService::class)->hasActiveAccess($student)));

        return $this->success([
            'questions' => $questions,
            'sector' => $sector,
            'is_paid' => $isPaid,
            'subjects' => QuestionSubject::where('is_active', true)
                ->whereHas('questions', fn ($q) => $q->published()->where('segment', $sector))
                ->orderBy('name_en')
                ->get(['id', 'name_en', 'name_bn']),
        ]);
    }

    public function askAi(Request $request, QuestionBank $question, QuestionAiStructuringService $aiService): JsonResponse
    {
        $student = auth('sanctum')->user();

        $isPaid = (bool) ($student?->activeSubscription || ($student && app(AccessGrantService::class)->hasActiveAccess($student)));

        if (! $isPaid) {
            return $this->error('AI Explanation is a Pro feature. Please upgrade your plan.', [], 403);
        }

        $explanation = $question->meta['ai_explanation'] ?? null;
        if (! $explanation) {
            $explanation = $aiService->generateDetailedExplanation($question);
            if ($explanation) {
                $meta = $question->meta ?? [];
                $meta['ai_explanation'] = $explanation;
                $question->update(['meta' => $meta]);
            }
        }

        return $this->success(['explanation' => $explanation]);
    }

    public function startExam(Request $request): JsonResponse
    {
        $student = auth('sanctum')->user();
        $sector = $this->resolveAllowedSector($request);

        $validated = $request->validate([
            'sector' => ['nullable', 'string', 'in:admission,job,hsc,ssc'],
            'subject_id' => ['nullable', 'integer', 'exists:question_subjects,id'],
            'origin_tag' => ['nullable', 'string', 'max:255'],
            'question_count' => ['nullable', 'integer', 'min:5', 'max:100'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:180'],
            'negative_marking' => ['nullable', 'boolean'],
        ]);

        $count = (int) ($validated['question_count'] ?? 20);
        $durationMinutes = (int) ($validated['duration_minutes'] ?? 20);
        $negativeMarking = $request->has('negative_marking') ? $request->boolean('negative_marking') : true;

        $query = QuestionBank::query()->with('subject')->published()->where('segment', $sector);
        if (! empty($validated['subject_id'])) {
            $query->where('subject_id', (int) $validated['subject_id']);
        }
        if (! empty($validated['origin_tag'])) {
            $query->where('origin_tag', $validated['origin_tag']);
        }

        $questions = $query->inRandomOrder()->limit($count)->get();
        if ($questions->isEmpty()) {
            return $this->error('No questions matched the selected filters.', [], 422);
        }

        $formatted = $questions->values()->map(function ($q, $index) {
            return [
                'id' => $index + 1,
                'question_bank_id' => $q->id,
                'question' => $q->question_text,
                'question_text' => $q->question_text,
                'type' => $q->question_type ?? 'mcq',
                'options_data' => $q->options ?? [],
                'correct_option' => $q->correct_option,
                'marks' => (float) ($q->marks ?? 1.0),
                'negative_marks' => (float) ($q->negative_marks ?? 0.25),
                'explanation' => $q->meta['ai_explanation'] ?? $q->explanation,
            ];
        })->all();

        $session = ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'exam',
            'title' => 'কাস্টম মক টেস্ট - প্রশ্নব্যাংক',
            'source_type' => 'topic',
            'config' => [
                'type' => 'mcq',
                'count' => count($formatted),
                'duration' => $durationMinutes,
                'negative_marking' => $negativeMarking,
            ],
            'questions' => $formatted,
            'total' => count($formatted),
            'duration_seconds' => $durationMinutes * 60,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return $this->success(['exam_id' => $session->id, 'exam' => $session], 'Mock exam started', 201);
    }
}
