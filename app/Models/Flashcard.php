<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    protected $fillable = ['flashcard_set_id', 'front', 'back', 'review_count', 'last_result', 'next_review_at'];

    protected function casts(): array
    {
        return ['next_review_at' => 'datetime'];
    }

    public function set() { return $this->belongsTo(FlashcardSet::class, 'flashcard_set_id'); }
}
