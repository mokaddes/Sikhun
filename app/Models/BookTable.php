<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTable extends Model
{
    protected $fillable = [
        'book_id', 'chapter_id', 'page_id', 'element_id', 'title', 'content', 'markdown', 'html', 'metadata',
    ];

    protected function casts(): array
    {
        return ['content' => 'array', 'metadata' => 'array'];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
    public function page() { return $this->belongsTo(BookPage::class); }
    public function element() { return $this->belongsTo(BookElement::class); }
}
