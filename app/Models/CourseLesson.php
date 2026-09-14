<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_section_id', 'title', 'type', 'video_url', 'video_path', 'text_content',
        'pdf_path', 'is_free_preview', 'sort_order', 'duration_minutes',
    ];

    protected function casts(): array
    {
        return ['is_free_preview' => 'boolean'];
    }

    public function section() { return $this->belongsTo(CourseSection::class, 'course_section_id'); }
    public function progress() { return $this->hasMany(LessonProgress::class); }

    /**
     * Playable source for a video lesson — the gated stream route when the
     * admin uploaded a file, otherwise whatever external URL was pasted in.
     */
    public function streamUrl(Course|string $course): ?string
    {
        $slug = $course instanceof Course ? $course->slug : $course;

        if ($this->video_path) {
            return route('courses.lesson.video', [
                'course' => $slug,
                'section' => $this->course_section_id,
                'lesson' => $this->id,
            ]);
        }

        return filled($this->video_url) ? $this->video_url : null;
    }

    public function downloadUrl(Course|string $course): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        $slug = $course instanceof Course ? $course->slug : $course;

        return route('courses.lesson.download', [
            'course' => $slug,
            'section' => $this->course_section_id,
            'lesson' => $this->id,
        ]);
    }

    /**
     * Shape this lesson for a student payload: expose the gated media URLs and
     * strip the raw private-disk paths, which must never reach the browser.
     */
    public function forStudent(Course|string $course): static
    {
        $this->setAttribute('stream_url', $this->streamUrl($course));
        $this->setAttribute('download_url', $this->downloadUrl($course));
        $this->makeHidden(['video_path', 'pdf_path']);

        return $this;
    }
}
