<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'target_segment', 'duration_months', 'badge_text', 'description', 'price_monthly', 'price_total', 'ai_chat_minutes',
        'ai_vision_limit', 'upload_book_limit', 'monthly_reupload_limit',
        'ai_exam_count', 'ai_explanation_count', 'gift_book_ids', 'trial_ai_minutes', 'features',
        'ai_provider_id', 'ai_model', 'is_active', 'is_free',
    ];

    protected function casts(): array
    {
        return [
            'duration_months' => 'integer',
            'price_monthly' => 'decimal:2',
            'price_total' => 'decimal:2',
            'ai_vision_limit' => 'integer',
            'upload_book_limit' => 'integer',
            'monthly_reupload_limit' => 'integer',
            'ai_explanation_count' => 'integer',
            'gift_book_ids' => 'array',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
        ];
    }

    public function getEffectiveTotalAttribute(): float
    {
        return (float) ($this->price_total ?: round((float) $this->price_monthly * ($this->duration_months ?: 1), 2));
    }

    public function subscriptions() { return $this->hasMany(StudentSubscription::class); }
    public function aiProvider() { return $this->belongsTo(AiProvider::class, 'ai_provider_id'); }
}
