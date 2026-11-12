<?php

namespace App\Services;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExamMistakeService
{
    /**
     * Record answer entries in exam_answers for a completed exam session.
     */
    public function recordExamAnswers(ExamSession $exam, array $answers): void
    {
        $questions = $exam->questions ?? [];
        if (empty($questions) || !is_array($questions)) {
            return;
        }

        // Delete any existing entries for this exam session to prevent duplicates
        ExamAnswer::where('exam_session_id', $exam->id)->delete();

        $rows = [];
        $now = now();

        foreach ($questions as $q) {
            $qId = $q['id'] ?? null;
            $given = $answers[$qId] ?? $answers[(string) $qId] ?? null;

            $correctAnswer = (string) ($q['correct_answer'] ?? '');
            $correctOption = (string) ($q['correct_option'] ?? '');
            $givenStr = trim((string) ($given ?? ''));

            $isCorrect = false;
            if ($givenStr !== '') {
                if ($correctAnswer !== '' && mb_strtolower($givenStr) === mb_strtolower(trim($correctAnswer))) {
                    $isCorrect = true;
                } elseif ($correctOption !== '' && mb_strtolower($givenStr) === mb_strtolower(trim($correctOption))) {
                    $isCorrect = true;
                }
            }

            $questionBankId = $q['question_bank_id'] ?? null;
            $questionText = $q['question'] ?? $q['question_text'] ?? '';
            $originTag = $q['origin_tag'] ?? null;
            $institution = $q['institution'] ?? null;
            $examName = $q['exam_name'] ?? null;
            $subjectId = $q['subject_id'] ?? null;
            $subjectName = $q['subject_name'] ?? null;
            $topicName = $q['topic_name'] ?? null;
            $year = $q['year'] ?? null;

            // If question originated from QuestionBank, try to enrich metadata if missing
            if ($questionBankId && (!$institution || !$subjectId || !$subjectName)) {
                $qb = QuestionBank::with('subject')->find($questionBankId);
                if ($qb) {
                    $originTag = $originTag ?: $qb->origin_tag;
                    $institution = $institution ?: $qb->institution;
                    $examName = $examName ?: $qb->exam_name;
                    $subjectId = $subjectId ?: $qb->subject_id;
                    $subjectName = $subjectName ?: ($qb->subject?->name_bn ?? $qb->subject?->name_en);
                    $topicName = $topicName ?: $qb->topic_name;
                    $year = $year ?: $qb->year;
                }
            }

            if ($subjectId && !$subjectName) {
                $subj = QuestionSubject::find($subjectId);
                $subjectName = $subj?->name_bn ?? $subj?->name_en;
            }

            // Detect university/institution from origin_tag if still empty
            if (!$institution && $originTag) {
                $institution = $this->guessInstitutionFromTag($originTag);
            }

            $rows[] = [
                'student_id' => $exam->student_id,
                'exam_session_id' => $exam->id,
                'question_bank_id' => $questionBankId,
                'question_identifier' => (string) ($questionBankId ?: md5($questionText)),
                'question_text' => $questionText,
                'question_type' => $q['type'] ?? 'mcq',
                'options' => json_encode($q['options_data'] ?? $q['options'] ?? []),
                'correct_option' => $correctOption ?: null,
                'correct_answer' => $correctAnswer ?: null,
                'student_answer' => $givenStr !== '' ? $givenStr : null,
                'is_correct' => $isCorrect,
                'explanation' => $q['explanation'] ?? null,
                'subject_id' => $subjectId,
                'subject_name' => $subjectName,
                'topic_name' => $topicName,
                'origin_tag' => $originTag,
                'institution' => $institution,
                'exam_name' => $examName,
                'year' => $year,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            ExamAnswer::insert($rows);
        }
    }

    /**
     * Backfill exam_answers for any completed exams that lack records.
     */
    public function backfillForStudent(Student $student): void
    {
        $existingSessionIds = ExamAnswer::where('student_id', $student->id)
            ->distinct()
            ->pluck('exam_session_id')
            ->all();

        $completedSessions = $student->examSessions()
            ->where('status', 'completed')
            ->whereNotIn('id', $existingSessionIds)
            ->get();

        foreach ($completedSessions as $session) {
            $this->recordExamAnswers($session, $session->answers ?? []);
        }
    }

    /**
     * Guess institution name from origin tags like DU-A-2023, RU-C-2022, BUET-2021, GST-2022.
     */
    public function guessInstitutionFromTag(string $tag): ?string
    {
        $upper = strtoupper(trim($tag));
        if (str_starts_with($upper, 'DU') || str_contains($upper, 'DHAKA')) {
            return 'ঢাকা বিশ্ববিদ্যালয় (DU)';
        }
        if (str_starts_with($upper, 'RU') || str_contains($upper, 'RAJSHAHI')) {
            return 'রাজশাহী বিশ্ববিদ্যালয় (RU)';
        }
        if (str_starts_with($upper, 'GST') || str_contains($upper, 'GUCCHO')) {
            return 'গুচ্ছ (GST)';
        }
        if (str_starts_with($upper, 'BUET')) {
            return 'বুয়েট (BUET)';
        }
        if (str_starts_with($upper, 'MAT') || str_contains($upper, 'MED') || str_contains($upper, 'MEDICAL')) {
            return 'মেডিকেল (Medical)';
        }
        if (str_starts_with($upper, 'CU') || str_contains($upper, 'CHITTAGONG')) {
            return 'চট্টগ্রাম বিশ্ববিদ্যালয় (CU)';
        }
        if (str_starts_with($upper, 'JU') || str_contains($upper, 'JAHANGIRNAGAR')) {
            return 'জাহাঙ্গীরনগর বিশ্ববিদ্যালয় (JU)';
        }
        if (str_starts_with($upper, 'BCS')) {
            return 'বিসিএস (BCS)';
        }

        return null;
    }

    /**
     * Get mistakes list and filter options for student.
     */
    public function getMistakesData(Student $student, array $filters = []): array
    {
        // Automatically backfill any missing sessions
        $this->backfillForStudent($student);

        $query = ExamAnswer::query()
            ->where('student_id', $student->id);

        $status = $filters['status'] ?? 'unresolved';

        if ($status === 'unresolved') {
            // Find questions where the most recent answer was incorrect
            $query->where('is_correct', false);
        } elseif ($status === 'mastered') {
            // Questions where student had previously failed but later succeeded
            $query->where('is_correct', true);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', (int) $filters['subject_id']);
        } elseif (!empty($filters['subject_name'])) {
            $query->where('subject_name', $filters['subject_name']);
        }

        if (!empty($filters['institution'])) {
            $inst = trim($filters['institution']);
            $query->where(function ($q) use ($inst) {
                $q->where('institution', 'like', "%{$inst}%")
                  ->orWhere('origin_tag', 'like', "%{$inst}%");
            });
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('origin_tag', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('exam_name', 'like', "%{$search}%");
            });
        }

        $mistakes = $query->latest('id')->paginate(15)->withQueryString();

        // Subjects with mistake counts
        $subjects = ExamAnswer::where('student_id', $student->id)
            ->where('is_correct', false)
            ->whereNotNull('subject_name')
            ->selectRaw('subject_id, subject_name, count(*) as mistakes_count')
            ->groupBy('subject_id', 'subject_name')
            ->orderByDesc('mistakes_count')
            ->get();

        // Institutions with mistake counts
        $institutions = ExamAnswer::where('student_id', $student->id)
            ->where('is_correct', false)
            ->whereNotNull('institution')
            ->selectRaw('institution, count(*) as mistakes_count')
            ->groupBy('institution')
            ->orderByDesc('mistakes_count')
            ->get();

        // Overall stats
        $totalMistakes = ExamAnswer::where('student_id', $student->id)->where('is_correct', false)->count();
        $totalAnswers = ExamAnswer::where('student_id', $student->id)->count();
        $masteredCount = ExamAnswer::where('student_id', $student->id)->where('is_correct', true)->count();

        return [
            'mistakes' => $mistakes,
            'subjects' => $subjects,
            'institutions' => $institutions,
            'stats' => [
                'total_mistakes' => $totalMistakes,
                'total_answers' => $totalAnswers,
                'mastered_count' => $masteredCount,
                'accuracy' => $totalAnswers > 0 ? round((($totalAnswers - $totalMistakes) / $totalAnswers) * 100, 1) : 0,
            ],
            'filters' => $filters,
        ];
    }

    /**
     * Create a revision ExamSession from student's mistakes.
     */
    public function createMistakePracticeExam(Student $student, array $filters = [], int $limit = 20): ?ExamSession
    {
        $this->backfillForStudent($student);

        $query = ExamAnswer::where('student_id', $student->id)
            ->where('is_correct', false);

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', (int) $filters['subject_id']);
        } elseif (!empty($filters['subject_name'])) {
            $query->where('subject_name', $filters['subject_name']);
        }

        if (!empty($filters['institution'])) {
            $inst = trim($filters['institution']);
            $query->where(function ($q) use ($inst) {
                $q->where('institution', 'like', "%{$inst}%")
                  ->orWhere('origin_tag', 'like', "%{$inst}%");
            });
        }

        $mistakes = $query->inRandomOrder()->limit($limit)->get();

        if ($mistakes->isEmpty()) {
            return null;
        }

        $formattedQuestions = $mistakes->values()->map(function ($m, $index) {
            $options = is_string($m->options) ? json_decode($m->options, true) : ($m->options ?? []);
            $optionTexts = [];
            $keys = [];
            $correctAnswerText = null;

            if (is_array($options)) {
                foreach ($options as $opt) {
                    if (is_array($opt)) {
                        $key = $opt['key'] ?? '';
                        $text = $opt['text'] ?? '';
                        $keys[] = $key;
                        $optionTexts[] = $text;
                        if (strtoupper(trim($key)) === strtoupper(trim($m->correct_option ?? ''))) {
                            $correctAnswerText = $text;
                        }
                    } else {
                        $optionTexts[] = (string) $opt;
                    }
                }
            }

            if (!$correctAnswerText) {
                $correctAnswerText = $m->correct_answer ?: $m->correct_option;
            }

            return [
                'id' => $index + 1,
                'question_bank_id' => $m->question_bank_id,
                'question' => $m->question_text,
                'question_text' => $m->question_text,
                'type' => $m->question_type ?: 'mcq',
                'keys' => !empty($keys) ? $keys : ['A', 'B', 'C', 'D'],
                'options' => !empty($optionTexts) ? $optionTexts : $options,
                'options_data' => $options,
                'correct_option' => $m->correct_option,
                'correct_answer' => $correctAnswerText,
                'marks' => 1.0,
                'negative_marks' => 0.25,
                'explanation' => $m->explanation,
                'subject_id' => $m->subject_id,
                'subject_name' => $m->subject_name,
                'origin_tag' => $m->origin_tag,
                'institution' => $m->institution,
                'exam_name' => $m->exam_name,
            ];
        })->all();

        $tagPart = $filters['institution'] ?? $filters['subject_name'] ?? 'সার্বিক';
        $title = "ভুল প্রশ্ন রিভিশন টেস্ট - {$tagPart}";

        $durationMinutes = max(5, count($formattedQuestions));

        return ExamSession::create([
            'student_id' => $student->id,
            'mode' => 'practice',
            'title' => $title,
            'source_type' => 'topic',
            'config' => [
                'title' => $title,
                'type' => 'mcq',
                'count' => count($formattedQuestions),
                'duration' => $durationMinutes,
                'duration_seconds' => $durationMinutes * 60,
                'mode' => 'practice',
                'is_mistake_review' => true,
                'subject_id' => $filters['subject_id'] ?? null,
                'institution' => $filters['institution'] ?? null,
            ],
            'questions' => $formattedQuestions,
            'total' => count($formattedQuestions),
            'duration_seconds' => $durationMinutes * 60,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }
}
