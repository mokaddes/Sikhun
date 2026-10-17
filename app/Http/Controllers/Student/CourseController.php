<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Concerns\StreamsLessonMedia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\CertificateService;
use App\Services\CourseDeliveryService;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseController extends Controller
{
    use StreamsLessonMedia;

    public function index(Request $request, \App\Services\SeoService $seo): Response
    {
        $sort = $request->input('sort', 'newest');

        $courses = Course::active()
            ->with(['mentor:id,name,photo,designation', 'category:id,name'])
            ->withCount(['enrollments', 'sections'])
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->free, fn ($q) => $q->where('price', 0))
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
            ))
            ->when($sort === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('enrollments_count'))
            ->when($sort === 'newest', fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Student/Courses/Index', [
            'seo' => $seo->forCourses(),
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
        $guestEnrolled = (bool) session("guest_enrolled_course_{$course->id}");
        $ownsCourse = (bool) $enrollment || $hasAccess || $guestEnrolled;

        $course->load(['mentor', 'category', 'sections.lessons'])
            ->loadCount('enrollments')
            ->decorateLessonsForStudent();

        if ($ownsCourse) {
            // The delivery link *is* the product for link-based courses — it is
            // hidden by default on the model and revealed only to owners.
            $course->makeVisible(['external_link', 'link_note']);
        }

        $relatedCourses = Course::active()
            ->where('id', '!=', $course->id)
            ->when($course->category_id, fn ($q) => $q->where('category_id', $course->category_id))
            ->with(['mentor:id,name,photo,designation', 'category:id,name'])
            ->withCount('enrollments')
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Student/Courses/Show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'isEnrolled' => $ownsCourse,
            'hasAccess' => $hasAccess,
            'walletBalance' => $student ? (float) $student->wallet_balance : 0.0,
            'relatedCourses' => $relatedCourses,
            'seo' => $seo->forCourse($course),
        ]);
    }

    public function enroll(PurchaseRequest $request, Course $course, PurchaseService $purchases, ZinipayService $zinipay, \App\Services\AccessGrantService $grants, CourseDeliveryService $delivery): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($course->is_active, 404);
        $student = auth('web')->user();

        if ($student->courseEnrollments()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        // Free campaign/coupon access → enroll for free so progress and
        // certificates work exactly like a paid enrollment.
        if ($grants->hasActiveAccess($student) || (float) $course->price === 0.0) {
            $student->courseEnrollments()->create(['course_id' => $course->id, 'progress_percentage' => 0]);
            $delivery->deliver($student, $course);

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

    /**
     * Guest Checkout for Courses: allows unauthenticated visitors to purchase
     * or enroll in a course directly by providing their name, email, and phone.
     * If the email is new, a student account is provisioned automatically and
     * logged in so the student has immediate access to lessons and emails.
     */
    public function guestEnroll(
        Request $request,
        Course $course,
        PurchaseService $purchases,
        ZinipayService $zinipay,
        CourseDeliveryService $delivery
    ): \Symfony\Component\HttpFoundation\Response {
        abort_unless($course->is_active, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'type' => ['nullable', 'in:ssc,hsc,university,job'],
        ]);

        $student = auth('web')->user();

        if (! $student) {
            $existing = \App\Models\Student::where('email', $validated['email'])->first();

            if ($existing) {
                if ($existing->status === 'inactive') {
                    return back()->with('error', 'This account is inactive. Please contact support.');
                }
                $student = $existing;
            } else {
                $defaultType = in_array($course->level, ['ssc', 'hsc', 'university', 'job'], true)
                    ? $course->level
                    : 'hsc';

                $student = \App\Models\Student::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Illuminate\Support\Str::random(12),
                    'type' => $validated['type'] ?? $defaultType,
                    'referral_code' => 'SIKHU-'.strtoupper(\Illuminate\Support\Str::random(5)),
                ]);

                \Illuminate\Support\Facades\Auth::guard('web')->login($student);
                $request->session()->regenerate();

                try {
                    \Illuminate\Support\Facades\Mail::to($student->email)->send(new \App\Mail\WelcomeMail($student));
                } catch (\Throwable) {
                    // Ignore mail transport errors during guest checkout
                }
            }
        }

        if ($student->courseEnrollments()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'This email is already enrolled in this course. Please sign in to access it.');
        }

        if ((float) $course->price === 0.0) {
            $student->courseEnrollments()->create(['course_id' => $course->id, 'progress_percentage' => 0]);
            try {
                $delivery->deliver($student, $course);
            } catch (\Throwable) {
                // Ignore mail transport errors
            }
            session(["guest_enrolled_course_{$course->id}" => true]);

            return redirect()->route('courses.show', $course)->with('success', 'Enrolled successfully!');
        }

        try {
            $result = $purchases->purchaseCourse(
                $student,
                $course,
                'zinipay',
                $zinipay,
                [
                    'guest_checkout' => true,
                    'guest_name' => $validated['name'],
                    'guest_email' => $validated['email'],
                    'guest_phone' => $validated['phone'] ?? null,
                ]
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['redirect_url']) {
            return Inertia::location($result['redirect_url']);
        }

        session(["guest_enrolled_course_{$course->id}" => true]);

        return redirect()->route('courses.show', $course)->with('success', 'Enrolled successfully!');
    }

    public function lesson(Course $course, \App\Models\CourseSection $section, \App\Models\CourseLesson $lesson, \App\Services\AccessGrantService $grants): Response
    {
        $student = auth('web')->user();
        $enrollment = $student ? $student->courseEnrollments()->where('course_id', $course->id)->first() : null;

        $this->ensureLessonBelongsToCourse($course, $section, $lesson);
        $this->ensureLessonAccess($student, $course, $lesson, $grants);

        $progress = $enrollment
            ? \App\Models\LessonProgress::where('student_id', $student->id)->where('course_lesson_id', $lesson->id)->first()
            : null;

        return Inertia::render('Student/Courses/Lesson', [
            'course' => $course->load('sections.lessons')->decorateLessonsForStudent(),
            'lesson' => $lesson->forStudent($course),
            'isCompleted' => (bool) $progress?->is_completed,
            'isEnrolled' => (bool) $enrollment,
        ]);
    }

    /** Streams an uploaded lesson video. Range requests make seeking work. */
    public function streamVideo(Course $course, \App\Models\CourseSection $section, \App\Models\CourseLesson $lesson, \App\Services\AccessGrantService $grants): BinaryFileResponse
    {
        $this->ensureLessonBelongsToCourse($course, $section, $lesson);
        $this->ensureLessonAccess(auth('web')->user(), $course, $lesson, $grants);

        return $this->streamLessonVideo($lesson);
    }

    /** Downloads an uploaded lesson PDF (a lesson may attach one instead of a video). */
    public function downloadAttachment(Course $course, \App\Models\CourseSection $section, \App\Models\CourseLesson $lesson, \App\Services\AccessGrantService $grants): BinaryFileResponse
    {
        $this->ensureLessonBelongsToCourse($course, $section, $lesson);
        $this->ensureLessonAccess(auth('web')->user(), $course, $lesson, $grants);

        return $this->downloadLessonPdf($lesson);
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
