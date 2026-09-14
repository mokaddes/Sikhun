<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookImage extends Model
{
    protected $fillable = [
        'book_id', 'chapter_id', 'page_id', 'element_id', 'path', 'alt_text', 'description', 'ocr_text', 'bbox', 'metadata',
    ];

    protected function casts(): array
    {
        return ['bbox' => 'array', 'metadata' => 'array'];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
    public function page() { return $this->belongsTo(BookPage::class); }
    public function element() { return $this->belongsTo(BookElement::class); }
}
