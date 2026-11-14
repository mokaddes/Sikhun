<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_session_id',
        'question_bank_id',
        'question_identifier',
        'question_text',
        'question_type',
        'options',
        'correct_option',
        'correct_answer',
        'student_answer',
        'is_correct',
        'resolved',
        'resolved_at',
        'explanation',
        'subject_id',
        'subject_name',
        'topic_name',
        'origin_tag',
        'institution',
        'exam_name',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'exam_session_id' => 'integer',
            'question_bank_id' => 'integer',
            'subject_id' => 'integer',
            'options' => 'array',
            'is_correct' => 'boolean',
            'resolved' => 'boolean',
            'resolved_at' => 'datetime',
            'year' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function scopeMistakes(Builder $query): Builder
    {
        return $query->where('is_correct', false);
    }
}
