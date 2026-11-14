<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircular extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'organization',
        'category',
        'job_type',
        'vacancy',
        'salary',
        'location',
        'education_requirement',
        'experience_requirement',
        'deadline',
        'published_date',
        'description',
        'application_instructions',
        'apply_url',
        'source_name',
        'source_url',
        'image_url',
        'is_featured',
        'views_count',
        'fb_shared_count',
        'fb_last_shared_at',
        'fb_post_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'published_date' => 'date',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'fb_shared_count' => 'integer',
            'fb_last_shared_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }
}
