<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookElement extends Model
{
    public const TYPES = ['text', 'heading', 'table', 'image', 'formula', 'list', 'caption', 'quote', 'other'];

    protected $fillable = [
        'book_id', 'chapter_id', 'page_id', 'type', 'content', 'metadata', 'bbox', 'page_number', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'bbox' => 'array',
            'page_number' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
    public function page() { return $this->belongsTo(BookPage::class); }
}
