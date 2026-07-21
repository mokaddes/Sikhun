<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookShelf extends Model
{
    protected $fillable = ['student_id', 'book_id', 'source', 'added_at'];

    protected function casts(): array
    {
        return ['added_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function book() { return $this->belongsTo(Book::class); }
}
