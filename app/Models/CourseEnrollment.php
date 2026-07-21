<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = ['student_id', 'course_id', 'progress_percentage', 'completed_at', 'certificate_path'];

    protected function casts(): array
    {
        return ['progress_percentage' => 'decimal:2', 'completed_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function course() { return $this->belongsTo(Course::class); }
}
