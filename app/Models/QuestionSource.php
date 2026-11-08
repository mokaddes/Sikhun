<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QuestionSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'source_type',
        'institution',
        'content_category',
        'crawl_frequency',
        'robots_txt_status',
        'robots_txt_checked_at',
        'tos_review',
        'tos_notes',
        'permission_status',
        'permission_document_url',
        'license_info',
        'commercial_redistribution',
        'attribution_requirements',
        'license_expiry',
        'rate_limit_rpm',
        'crawl_delay_ms',
        'last_crawl_at',
        'last_crawl_status',
        'questions_imported_count',
        'is_active',
        'notes',
        'meta',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuestionSource $source) {
            if (empty($source->slug)) {
                $source->slug = Str::slug($source->name);
                // Handle duplicate slug collision
                $count = static::where('slug', 'LIKE', "{$source->slug}%")->count();
                if ($count > 0) {
                    $source->slug .= '-' . ($count + 1);
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'commercial_redistribution' => 'boolean',
            'is_active' => 'boolean',
            'robots_txt_checked_at' => 'datetime',
            'last_crawl_at' => 'datetime',
            'license_expiry' => 'date',
            'rate_limit_rpm' => 'integer',
            'crawl_delay_ms' => 'integer',
            'questions_imported_count' => 'integer',
            'meta' => 'array',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'source_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(QuestionIngestionBatch::class, 'source_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAuthorized(Builder $query): Builder
    {
        return $query->whereIn('permission_status', ['authorized', 'public_domain', 'fair_use_educational']);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        if ($category === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($category) {
            $q->where('content_category', $category)
              ->orWhere('content_category', 'both');
        });
    }
}
