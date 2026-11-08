<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QuestionSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_bn',
        'name_en',
        'slug',
        'segment',
        'icon',
        'order',
        'is_active',
        'meta',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuestionSubject $subject) {
            if (empty($subject->slug)) {
                $subject->slug = Str::slug($subject->name_en ?: $subject->name_bn);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function topics(): HasMany
    {
        return $this->hasMany(QuestionTopic::class, 'subject_id')->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'subject_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function scopeForSegment(Builder $query, string $segment): Builder
    {
        if ($segment === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($segment) {
            $q->where('segment', $segment)
              ->orWhere('segment', 'both');
        });
    }
}
