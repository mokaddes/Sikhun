<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QuestionTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name_bn',
        'name_en',
        'slug',
        'segment',
        'order',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuestionTopic $topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->name_en ?: $topic->name_bn);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'subject_id' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(QuestionSubject::class, 'subject_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'topic_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
