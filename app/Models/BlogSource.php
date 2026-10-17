<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogSource extends Model
{
    protected $fillable = [
        'name',
        'url',
        'type',
        'link_selector',
        'title_selector',
        'content_selector',
        'default_category_id',
        'is_active',
        'max_per_run',
        'last_run_at',
        'last_status',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_per_run' => 'integer',
            'last_run_at' => 'datetime',
        ];
    }

    public function defaultCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'default_category_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BlogScrapeLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
