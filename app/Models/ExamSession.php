<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'student_id', 'source_type', 'source_book_id', 'source_chapter', 'source_page',
        'source_text', 'config', 'questions', 'answers', 'score', 'total', 'percentage',
        'mode', 'status', 'started_at', 'completed_at', 'time_taken_seconds',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'questions' => 'array',
            'answers' => 'array',
            'percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function book() { return $this->belongsTo(Book::class, 'source_book_id'); }
    public function leaderboardEntry() { return $this->hasOne(LeaderboardEntry::class); }
}
