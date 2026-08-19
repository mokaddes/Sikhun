<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    protected $fillable = ['student_id', 'book_id', 'pages_read', 'duration_seconds', 'ip_address', 'last_activity_at'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'last_activity_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function book() { return $this->belongsTo(Book::class); }
}
