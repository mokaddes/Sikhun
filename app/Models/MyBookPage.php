<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyBookPage extends Model
{
    protected $fillable = ['my_book_id', 'page_number', 'content'];

    protected function casts(): array
    {
        return ['page_number' => 'integer'];
    }

    public function myBook() { return $this->belongsTo(MyBook::class); }
}