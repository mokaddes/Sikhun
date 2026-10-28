<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'ai_chat_minutes',
        'ai_vision_limit', 'upload_book_limit', 'monthly_reupload_limit',
        'ai_exam_count', 'gift_book_ids', 'trial_ai_minutes', 'features',
        'ai_provider_id', 'ai_model', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'ai_vision_limit' => 'integer',
            'upload_book_limit' => 'integer',
            'monthly_reupload_limit' => 'integer',
            'gift_book_ids' => 'array',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions() { return $this->hasMany(StudentSubscription::class); }
    public function aiProvider() { return $this->belongsTo(AiProvider::class, 'ai_provider_id'); }
}
