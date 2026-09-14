<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPage extends Model
{
    protected $fillable = [
        'book_id', 'page_number', 'chapter_id', 'content', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
    public function elements() { return $this->hasMany(BookElement::class); }
}
