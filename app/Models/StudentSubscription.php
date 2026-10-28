<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubscription extends Model
{
    protected $fillable = [
        'student_id', 'plan_id', 'status', 'ai_chat_minutes_remaining',
        'ai_vision_remaining', 'monthly_reuploads_remaining',
        'ai_exam_count_remaining', 'started_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'plan_id' => 'integer',
            'ai_chat_minutes_remaining' => 'integer',
            'ai_vision_remaining' => 'integer',
            'monthly_reuploads_remaining' => 'integer',
            'ai_exam_count_remaining' => 'integer',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
}
