<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySchedule extends Model
{
    protected $fillable = ['student_id', 'exam_date', 'config', 'schedule_data', 'status'];

    protected function casts(): array
    {
        return ['exam_date' => 'date', 'config' => 'array', 'schedule_data' => 'array'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
