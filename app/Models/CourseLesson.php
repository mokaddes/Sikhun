<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_section_id', 'title', 'type', 'video_url', 'text_content',
        'pdf_path', 'is_free_preview', 'sort_order', 'duration_minutes',
    ];

    protected function casts(): array
    {
        return ['is_free_preview' => 'boolean'];
    }

    public function section() { return $this->belongsTo(CourseSection::class, 'course_section_id'); }
    public function progress() { return $this->hasMany(LessonProgress::class); }
}
