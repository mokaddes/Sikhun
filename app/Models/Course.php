<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'cover_image', 'demo_video_url', 'mentor_id',
        'category_id', 'level', 'price', 'is_active',
        'delivery_type', 'external_link', 'link_note',
        'fb_shared_count', 'fb_last_shared_at', 'fb_post_id',
    ];

    protected $appends = ['cover_image_url', 'has_demo_video', 'demo_preview_url'];

    /**
     * The delivery link is the product for link-based courses, so it is never
     * serialized by default — controllers opt it back in only for students who
     * already have access. See CourseController::show().
     * demo_video_url is also hidden so students never see the raw Google Drive link.
     */
    protected $hidden = ['external_link', 'link_note', 'demo_video_url'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'fb_shared_count' => 'integer',
            'fb_last_shared_at' => 'datetime',
        ];
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    public function getHasDemoVideoAttribute(): bool
    {
        return filled($this->demo_video_url);
    }

    public function getDemoPreviewUrlAttribute(): ?string
    {
        return filled($this->demo_video_url) ? route('courses.demo.stream', $this->slug) : null;
    }

    /** True when the course is delivered as one external link rather than lessons. */
    public function isLinkDelivery(): bool
    {
        return $this->delivery_type !== 'video';
    }

    public function hasDeliveryLink(): bool
    {
        return $this->isLinkDelivery() && filled($this->external_link);
    }

    /**
     * Shapes the loaded sections/lessons for a student payload: attaches the
     * gated stream/download URLs and strips the raw private-disk paths, which
     * must never reach the browser.
     */
    public function decorateLessonsForStudent(): static
    {
        $this->sections->each(function (CourseSection $section) {
            $section->lessons->each(fn (CourseLesson $lesson) => $lesson->forStudent($this));
        });

        return $this;
    }

    public function mentor() { return $this->belongsTo(Mentor::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function sections() { return $this->hasMany(CourseSection::class)->orderBy('sort_order'); }
    public function enrollments() { return $this->hasMany(CourseEnrollment::class); }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
