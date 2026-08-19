<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $fillable = ['student_id', 'course_lesson_id', 'is_completed', 'completed_at'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'is_completed' => 'boolean', 'completed_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function lesson() { return $this->belongsTo(CourseLesson::class, 'course_lesson_id'); }
}
