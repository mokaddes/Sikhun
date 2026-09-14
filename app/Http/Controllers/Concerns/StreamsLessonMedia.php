<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Student;
use App\Services\AccessGrantService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Access-checked delivery of lesson media for both the web and API controllers.
 *
 * Uploaded videos and PDFs live on the private disk, so these methods are the
 * only way to reach them — that is what stops a paid lesson from being shared
 * by passing a URL around.
 */
trait StreamsLessonMedia
{
    private const VIDEO_MIME_TYPES = [
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
        'mkv' => 'video/x-matroska',
    ];

    /**
     * Same rule the lesson page uses: enrolled, a free preview, or holding an
     * active access grant.
     */
    protected function ensureLessonAccess(?Student $student, Course $course, CourseLesson $lesson, AccessGrantService $grants): void
    {
        $enrolled = $student
            ? $student->courseEnrollments()->where('course_id', $course->id)->exists()
            : false;

        abort_unless(
            $enrolled || $lesson->is_free_preview || ($student && $grants->hasActiveAccess($student)),
            403,
            'Enroll in this course to view this lesson.'
        );
    }

    /** Nested route binding alone does not prove the lesson sits under this course. */
    protected function ensureLessonBelongsToCourse(Course $course, CourseSection $section, CourseLesson $lesson): void
    {
        abort_unless(
            $lesson->course_section_id === $section->id && $section->course_id === $course->id,
            404
        );
    }

    /**
     * Streams with HTTP Range support (Symfony's binary response handles the
     * 206 responses), so students can seek without downloading the whole file.
     */
    protected function streamLessonVideo(CourseLesson $lesson): BinaryFileResponse
    {
        $path = $this->privateFilePath($lesson->video_path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return response()->file($path, [
            'Content-Type' => self::VIDEO_MIME_TYPES[$extension] ?? 'application/octet-stream',
        ]);
    }

    protected function downloadLessonPdf(CourseLesson $lesson): BinaryFileResponse
    {
        $path = $this->privateFilePath($lesson->pdf_path);
        $filename = (Str::slug($lesson->title) ?: 'lesson').'.pdf';

        return response()->download($path, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function privateFilePath(?string $storedPath): string
    {
        abort_unless($storedPath && Storage::disk('private')->exists($storedPath), 404);

        return Storage::disk('private')->path($storedPath);
    }
}
