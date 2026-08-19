<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    protected $fillable = [
        'student_id', 'exam_session_id', 'subject', 'book_id', 'student_type',
        'score', 'total', 'percentage', 'questions_count', 'week_number', 'month_number', 'year',
    ];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'percentage' => 'decimal:2'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function examSession() { return $this->belongsTo(ExamSession::class); }
    public function book() { return $this->belongsTo(Book::class); }
}
