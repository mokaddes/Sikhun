<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardSet extends Model
{
    protected $fillable = ['student_id', 'title', 'source_label', 'is_public', 'share_token'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'is_public' => 'boolean'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function flashcards() { return $this->hasMany(Flashcard::class); }
}
