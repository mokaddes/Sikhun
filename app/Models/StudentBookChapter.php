<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Chapter-level ownership: a student who bought (or was gifted) an
 * individual chapter of a chapter-purchasable book. Uniqueness of
 * (student_id, book_id, chapter_id) is enforced at the DB level.
 */
class StudentBookChapter extends Model
{
    protected $fillable = ['student_id', 'book_id', 'chapter_id', 'source', 'price', 'purchased_at'];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'book_id' => 'integer',
            'chapter_id' => 'integer',
            'price' => 'decimal:2',
            'purchased_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function book() { return $this->belongsTo(Book::class); }
    public function chapter() { return $this->belongsTo(BookChapter::class); }
}
