<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubscription extends Model
{
    protected $fillable = [
        'student_id', 'plan_id', 'status', 'ai_chat_minutes_remaining',
        'ai_exam_count_remaining', 'started_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
}
