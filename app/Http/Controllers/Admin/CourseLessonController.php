<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseLessonRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ChunkedUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseLessonController extends Controller
{
    /** Where promoted lesson videos live on the private disk. */
    private const VIDEO_DIR = 'courses/videos';

    /** Where promoted lesson PDFs live on the private disk. */
    private const PDF_DIR = 'courses/pdfs';

    public function __construct(private ChunkedUploadService $uploads) {}

    public function store(CourseLessonRequest $request, Course $course, CourseSection $section): RedirectResponse
    {
        $data = $request->validated();
        unset($data['video_temp_path'], $data['remove_video'], $data['pdf_file']);

        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $data['sort_order'] = $data['sort_order'] ?? $section->lessons()->count() + 1;

        $lesson = $section->lessons()->create($data);

        $this->applyUploads($request, $lesson);

        return back()->with('success', 'Lesson added.');
    }

    public function update(CourseLessonRequest $request, Course $course, CourseSection $section, CourseLesson $lesson): RedirectResponse
    {
        $data = $request->validated();
        unset($data['video_temp_path'], $data['remove_video'], $data['pdf_file']);

        $data['is_free_preview'] = $request->boolean('is_free_preview');

        $lesson->update($data);

        $this->applyUploads($request, $lesson);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroy(Course $course, CourseSection $section, CourseLesson $lesson): RedirectResponse
    {
        $this->deleteFile($lesson->video_path);
        $this->deleteFile($lesson->pdf_path);

        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    /**
     * Promote any uploaded media attached to this request onto the lesson.
     *
     * The video arrives as a temp path produced by the chunked uploader
     * (promote() re-validates it), while a PDF is small enough to post inline.
     */
    private function applyUploads(CourseLessonRequest $request, CourseLesson $lesson): void
    {
        $changed = false;

        if ($request->boolean('remove_video') && $lesson->video_path) {
            $this->deleteFile($lesson->video_path);
            $lesson->video_path = null;
            $changed = true;
        }

        if ($request->filled('video_temp_path') && $lesson->type === 'video') {
            $stored = $this->uploads->promote(
                CourseVideoUploadController::PREFIX,
                (string) $request->input('video_temp_path'),
                self::VIDEO_DIR,
                CourseVideoUploadController::ALLOWED_EXTENSIONS,
            );

            if ($stored) {
                $this->deleteFile($lesson->video_path); // replacing an existing video
                $lesson->video_path = $stored;
                $changed = true;
            }
        }

        if ($request->hasFile('pdf_file') && $lesson->type === 'pdf') {
            /** @var UploadedFile $file */
            $file = $request->file('pdf_file');
            $stored = $file->store(self::PDF_DIR, 'private');

            if ($stored) {
                $this->deleteFile($lesson->pdf_path);
                $lesson->pdf_path = $stored;
                $changed = true;
            }
        }

        if ($changed) {
            $lesson->save();
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('private')->delete($path);
        }
    }
}
