<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSession extends Model
{
    protected $fillable = [
        'student_id', 'source_type', 'source_book_id', 'title', 'messages', 'tokens_used',
    ];

    protected function casts(): array
    {
        return ['messages' => 'array'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function book() { return $this->belongsTo(Book::class, 'source_book_id'); }
}
