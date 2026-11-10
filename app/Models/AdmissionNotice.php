<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'institution_name',
        'university_type',
        'academic_year',
        'units',
        'application_start',
        'application_deadline',
        'exam_dates',
        'minimum_gpa_ssc',
        'minimum_gpa_hsc',
        'total_gpa_required',
        'application_fee',
        'apply_url',
        'official_website_url',
        'notice_image_url',
        'featured_image_url',
        'description',
        'eligibility_criteria',
        'marks_distribution',
        'status',
        'is_featured',
        'views_count',
        'source_name',
        'source_url',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'units' => 'array',
            'exam_dates' => 'array',
            'application_start' => 'date',
            'application_deadline' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function isApplicationOpen(): bool
    {
        if (! $this->application_deadline) {
            return true;
        }

        return ! $this->application_deadline->isPast();
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (! $this->application_deadline) {
            return null;
        }

        $now = now()->startOfDay();
        $deadline = $this->application_deadline->copy()->startOfDay();

        return $deadline->isPast() ? 0 : (int) $now->diffInDays($deadline, false);
    }
}
