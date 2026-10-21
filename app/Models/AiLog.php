<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    protected $fillable = [
        'student_id',
        'admin_id',
        'user_type',
        'user_name',
        'user_email',
        'provider_id',
        'provider_name',
        'provider_type',
        'model_name',
        'use_case',
        'prompt',
        'response',
        'status',
        'error_message',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'latency_ms',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'total_tokens' => 'integer',
            'latency_ms' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }
}
