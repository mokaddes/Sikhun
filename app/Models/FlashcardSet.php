<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardSet extends Model
{
    protected $fillable = [
        'student_id', 'title', 'source_label', 'is_public', 'share_token',
        'generation_data', 'generation_status', 'generation_attempts',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'is_public' => 'boolean',
            'generation_data' => 'array',
            'generation_attempts' => 'integer',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function flashcards() { return $this->hasMany(Flashcard::class); }
}
