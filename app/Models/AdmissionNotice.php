<?php

namespace App\Models;

use Carbon\Carbon;
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
        'application_start_text',
        'application_deadline',
        'application_deadline_text',
        'exam_dates',
        'admit_card_date_text',
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
        'fb_shared_count',
        'fb_last_shared_at',
        'fb_post_id',
        'is_featured',
        'views_count',
        'source_name',
        'source_url',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected $appends = [
        'application_status',
        'application_status_label',
        'target_countdown_iso',
        'target_countdown_type',
    ];

    protected function casts(): array
    {
        return [
            'units' => 'array',
            'exam_dates' => 'array',
            'application_start' => 'datetime',
            'application_deadline' => 'datetime',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'fb_shared_count' => 'integer',
            'fb_last_shared_at' => 'datetime',
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

    public function getApplicationStatusAttribute(): string
    {
        $now = now();

        if ($this->application_start && $this->application_start->isFuture()) {
            return 'upcoming';
        }

        if ($this->application_deadline && $this->application_deadline->isPast()) {
            return 'closed';
        }

        return 'open';
    }

    public function getApplicationStatusLabelAttribute(): string
    {
        return match ($this->application_status) {
            'upcoming' => 'আবেদন শুরু হতে বাকি',
            'open' => 'আবেদন চলছে',
            'closed' => 'আবেদনের সময় শেষ',
        };
    }

    public function getTargetCountdownIsoAttribute(): ?string
    {
        if ($this->application_status === 'upcoming' && $this->application_start) {
            return $this->application_start->toIso8601String();
        }

        if ($this->application_status === 'open' && $this->application_deadline) {
            return $this->application_deadline->toIso8601String();
        }

        return null;
    }

    public function getTargetCountdownTypeAttribute(): string
    {
        return $this->application_status === 'upcoming' ? 'starting_in' : 'ending_in';
    }

    public function isApplicationOpen(): bool
    {
        return $this->application_status === 'open';
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
