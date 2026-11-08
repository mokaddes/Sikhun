<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'segment',
        'allowed_student_types',
        'allowed_plan_ids',
        'is_public_to_all',
        'subject_id',
        'topic_id',
        'topic_name',
        'institution',
        'exam_name',
        'origin_tag',
        'year',
        'session',
        'shift_or_set',
        'question_text',
        'question_type',
        'options',
        'correct_option',
        'explanation',
        'marks',
        'negative_marks',
        'difficulty',
        'source_id',
        'source_url',
        'ingestion_type',
        'status',
        'verification_status',
        'verified_by_admin_id',
        'verified_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'allowed_student_types' => 'array',
            'allowed_plan_ids' => 'array',
            'is_public_to_all' => 'boolean',
            'subject_id' => 'integer',
            'topic_id' => 'integer',
            'year' => 'integer',
            'options' => 'array',
            'marks' => 'decimal:2',
            'negative_marks' => 'decimal:2',
            'verified_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(QuestionSubject::class, 'subject_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(QuestionTopic::class, 'topic_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(QuestionSource::class, 'source_id');
    }

    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by_admin_id');
    }

    public function getCorrectOptionTextAttribute(): ?string
    {
        if (empty($this->options) || !is_array($this->options)) {
            return null;
        }

        $targetKey = strtoupper(trim($this->correct_option));
        foreach ($this->options as $opt) {
            if (isset($opt['key']) && strtoupper(trim($opt['key'])) === $targetKey) {
                return $opt['text'] ?? null;
            }
        }

        return null;
    }

    /**
     * Check if a student can view this question based on type and subscription plan.
     */
    public function isAccessibleBy(?Student $student): bool
    {
        if (!$student) {
            return false;
        }

        // Check student type (SSC, HSC, Admission, Job, University)
        if (!empty($this->allowed_student_types) && is_array($this->allowed_student_types)) {
            if (!in_array('all', $this->allowed_student_types, true)) {
                $studentType = strtolower($student->type ?? 'hsc');
                if (!in_array($studentType, array_map('strtolower', $this->allowed_student_types), true)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if question requires higher subscription plan for the student.
     */
    public function isPlanLockedFor(?Student $student): bool
    {
        if (!$student) {
            return true;
        }

        if (empty($this->allowed_plan_ids) || !is_array($this->allowed_plan_ids) || in_array('all', $this->allowed_plan_ids)) {
            return false;
        }

        // Student's active plan
        $activeSub = $student->activeSubscription;
        $studentPlanId = $activeSub ? (int)$activeSub->plan_id : 'free';

        // Check if student's plan is in allowed list
        return !in_array($studentPlanId, $this->allowed_plan_ids) && !in_array((string)$studentPlanId, $this->allowed_plan_ids);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeAdmission(Builder $query): Builder
    {
        return $query->where('segment', 'admission');
    }

    public function scopeJob(Builder $query): Builder
    {
        return $query->where('segment', 'job');
    }

    public function scopeByOrigin(Builder $query, string $originTag): Builder
    {
        return $query->where('origin_tag', $originTag);
    }
}
