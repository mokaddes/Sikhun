<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssaySubmission extends Model
{
    protected $fillable = ['student_id', 'grading_type', 'essay_text', 'result', 'status'];

    protected function casts(): array
    {
        return ['result' => 'array'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
