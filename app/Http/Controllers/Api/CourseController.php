<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\LessonProgress;
use App\Services\CertificateService;
use App\Services\Payment\SslcommerzService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $courses = Course::active()->with(['mentor:id,name', 'category:id,name'])
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->latest()->paginate(20);

        return $this->success($courses);
    }

    public function show(Course $course): JsonResponse
    {
        abort_unless($course->is_active, 404);
        $student = auth('sanctum')->user();
        $enrollment = $student->courseEnrollments()->where('course_id', $course->id)->first();

        return $this->success([
            'course' => $course->load(['mentor', 'category', 'sections.lessons']),
            'enrollment' => $enrollment,
            'is_enrolled' => (bool) $enrollment,
        ]);
    }

    public function enroll(PurchaseRequest $request, Course $course, PurchaseService $purchases, SslcommerzService $sslcommerz): JsonResponse
    {
        $student = auth('sanctum')->user();

        if ($student->courseEnrollments()->where('course_id', $course->id)->exists()) {
            return $this->error('Already enrolled.', [], 422);
        }

        if ((float) $course->price === 0.0) {
            $enrollment = $student->courseEnrollments()->create(['course_id' => $course->id, 'progress_percentage' => 0]);

            return $this->success($enrollment, 'Enrolled', 201);
        }

        try {
            $result = $purchases->purchaseCourse($student, $course, $request->payment_method, $request->payment_method === 'sslcommerz' ? $sslcommerz : null);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success($result);
    }

    public function lesson(Course $course, CourseSection $section, CourseLesson $lesson): JsonResponse
    {
        $student = auth('sanctum')->user();
        $enrollment = $student->courseEnrollments()->where('course_id', $course->id)->first();
        abort_unless($enrollment || $lesson->is_free_preview, 403);

        $progress = $enrollment ? LessonProgress::where('student_id', $student->id)->where('course_lesson_id', $lesson->id)->first() : null;

        return $this->success(['lesson' => $lesson, 'is_completed' => (bool) $progress?->is_completed]);
    }

    public function completeLesson(Course $course, CourseSection $section, CourseLesson $lesson, CertificateService $certificates): JsonResponse
    {
        $student = auth('sanctum')->user();
        $enrollment = $student->courseEnrollments()->where('course_id', $course->id)->firstOrFail();

        LessonProgress::updateOrCreate(
            ['student_id' => $student->id, 'course_lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $lessonIds = CourseLesson::whereIn('course_section_id', $course->sections()->pluck('id'))->pluck('id');
        $total = $lessonIds->count();
        $completed = LessonProgress::where('student_id', $student->id)->whereIn('course_lesson_id', $lessonIds)->where('is_completed', true)->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        $enrollment->update(['progress_percentage' => $percentage]);

        if ($percentage >= 100 && ! $enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
            $enrollment->update(['certificate_path' => $certificates->generate($enrollment->fresh())]);
        }

        return $this->success($enrollment->fresh());
    }
}
