<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\CertificateService;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->input('sort', 'newest');

        $courses = Course::active()
            ->with(['mentor:id,name,photo', 'category:id,name'])
            ->withCount('enrollments')
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->free, fn ($q) => $q->where('price', 0))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($sort === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('enrollments_count'))
            ->when($sort === 'newest', fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Student/Courses/Index', [
            'courses' => $courses,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('level', 'category_id', 'free', 'search', 'sort'),
        ]);
    }

    public function show(Course $course, \App\Services\SeoService $seo, \App\Services\AccessGrantService $grants): Response
    {
        abort_unless($course->is_active, 404);
        $student = auth('web')->user();

        $enrollment = $student ? $student->courseEnrollments()->where('course_id', $course->id)->first() : null;
        $hasAccess = $student && $grants->hasActiveAccess($student);

        return Inertia::render('Student/Courses/Show', [
            'course' => $course->load(['mentor', 'category', 'sections.lessons']),
            'enrollment' => $enrollment,
            'isEnrolled' => (bool) $enrollment,
            'hasAccess' => $hasAccess,
            'seo' => $seo->forCourse($course),
        ]);
    }

    public function enroll(PurchaseRequest $request, Course $course, PurchaseService $purchases, ZinipayService $zinipay, \App\Services\AccessGrantService $grants): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();

        if ($student->courseEnrollments()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        // Free campaign/coupon access → enroll for free so progress and
        // certificates work exactly like a paid enrollment.
        if ($grants->hasActiveAccess($student) || (float) $course->price === 0.0) {
            $student->courseEnrollments()->create(['course_id' => $course->id, 'progress_percentage' => 0]);

            return redirect()->route('courses.show', $course)->with('success', 'Enrolled!');
        }

        try {
            $result = $purchases->purchaseCourse(
                $student, $course, $request->payment_method,
                $request->payment_method === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['redirect_url']) {
            // Inertia XHR visits cannot follow cross-origin redirects (CORS) —
            // location() answers with 409 + X-Inertia-Location so the client
            // performs a full-page browser redirect to the gateway.
            return Inertia::location($result['redirect_url']);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Enrolled!');
    }

    public function lesson(Course $course, \App\Models\CourseSection $section, \App\Models\CourseLesson $lesson, \App\Services\AccessGrantService $grants): Response
    {
        $student = auth('web')->user();
        $enrollment = $student ? $student->courseEnrollments()->where('course_id', $course->id)->first() : null;

        abort_unless(
            $enrollment || $lesson->is_free_preview || ($student && $grants->hasActiveAccess($student)),
            403,
            'Enroll in this course to view this lesson.'
        );

        $progress = $enrollment
            ? \App\Models\LessonProgress::where('student_id', $student->id)->where('course_lesson_id', $lesson->id)->first()
            : null;

        return Inertia::render('Student/Courses/Lesson', [
            'course' => $course->load('sections.lessons'),
            'lesson' => $lesson,
            'isCompleted' => (bool) $progress?->is_completed,
            'isEnrolled' => (bool) $enrollment,
        ]);
    }

    public function completeLesson(Course $course, \App\Models\CourseSection $section, \App\Models\CourseLesson $lesson, CertificateService $certificates): RedirectResponse
    {
        $student = auth('web')->user();
        $enrollment = $student->courseEnrollments()->where('course_id', $course->id)->firstOrFail();

        \App\Models\LessonProgress::updateOrCreate(
            ['student_id' => $student->id, 'course_lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $lessonIds = \App\Models\CourseLesson::whereIn('course_section_id', $course->sections()->pluck('id'))->pluck('id');
        $total = $lessonIds->count();
        $completed = \App\Models\LessonProgress::where('student_id', $student->id)->whereIn('course_lesson_id', $lessonIds)->where('is_completed', true)->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        $enrollment->update(['progress_percentage' => $percentage]);

        if ($percentage >= 100 && ! $enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
            $enrollment->update(['certificate_path' => $certificates->generate($enrollment->fresh())]);
        }

        return back()->with('success', 'Lesson marked complete.');
    }
}
