<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookChunk extends Model
{
    protected $fillable = [
        'book_id', 'chapter_id', 'page_id', 'chunk_index', 'page_number', 'content', 'metadata', 'embedding',
    ];

    protected function casts(): array
    {
        return [
            'chunk_index' => 'integer',
            'page_number' => 'integer',
            'metadata' => 'array',
            'embedding' => 'array',
        ];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
    public function page() { return $this->belongsTo(BookPage::class); }
}
