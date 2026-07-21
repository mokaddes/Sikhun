<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookChunk extends Model
{
    protected $fillable = ['book_id', 'chunk_index', 'page_number', 'content'];

    public function book() { return $this->belongsTo(Book::class); }
}
