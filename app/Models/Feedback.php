<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'student_id', 'type', 'target_type', 'target_id', 'target_key', 'rating',
        'message', 'context', 'status', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'target_id' => 'integer',
            'rating' => 'integer',
            'context' => 'array',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
