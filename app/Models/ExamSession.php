<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'student_id', 'title', 'source_type', 'source_book_id', 'source_chapter', 'source_page',
        'source_text', 'config', 'questions', 'answers', 'score', 'total', 'percentage',
        'mode', 'status', 'started_at', 'completed_at', 'time_taken_seconds',
    ];

    protected $appends = ['display_title'];

    public function getDisplayTitleAttribute(): string
    {
        if (!empty($this->title)) {
            return $this->title;
        }

        if ($this->source_type === 'book' && $this->relationLoaded('book') && $this->book) {
            return $this->book->title;
        }

        if (!empty($this->source_text)) {
            return \Illuminate\Support\Str::limit($this->source_text, 40);
        }

        $type = strtoupper($this->config['type'] ?? 'MCQ');
        return "{$type} Exam #{$this->id}";
    }

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
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
