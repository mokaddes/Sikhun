<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'ai_chat_minutes',
        'ai_exam_count', 'gift_book_ids', 'trial_ai_minutes', 'features', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'gift_book_ids' => 'array',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions() { return $this->hasMany(StudentSubscription::class); }
}
