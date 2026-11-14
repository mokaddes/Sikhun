<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Jobs\DispatchPagePilotSocialPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\Mentor;
use App\Services\Social\PagePilotSocialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::with('mentor')->withCount('enrollments');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('mentor', fn ($mq) => $mq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $query->latest()->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Courses/Form', [
            'course' => null,
            ...$this->formOptions(),
        ]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        $course = Course::create($data);

        if ($course->is_active) {
            DispatchPagePilotSocialPost::dispatch('course', $course->id);
        }

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Course created — now add sections and lessons.');
    }

    /**
     * Doubles as the "manage sections & lessons" screen — Course has no
     * public admin `show`, so `edit` carries the full nested structure.
     */
    public function edit(Course $course): Response
    {
        // The delivery link is hidden by default on the model (students must
        // never see it for free) — the admin form needs it back.
        $course->makeVisible(['external_link', 'link_note']);

        return Inertia::render('Admin/Courses/Form', [
            'course' => $course->load('sections.lessons'),
            ...$this->formOptions(),
        ]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        $wasActive = $course->is_active;
        $course->update($data);

        if (! $wasActive && $course->is_active) {
            DispatchPagePilotSocialPost::dispatch('course', $course->id);
        }

        return back()->with('success', 'Course updated.');
    }

    public function shareToFacebook(Course $course, PagePilotSocialService $social): RedirectResponse
    {
        $result = $social->publishCourse($course);

        if ($result['success'] ?? false) {
            $course->increment('fb_shared_count');
            $course->update([
                'fb_last_shared_at' => now(),
                'fb_post_id' => $result['post_id'] ?? $result['id'] ?? $course->fb_post_id,
            ]);

            return back()->with('success', 'ফেসবুক পেজে সফলভাবে কোর্স পোস্ট এবং প্রথম কমেন্ট প্রকাশিত হয়েছে! (মোট শেয়ার: ' . $course->fb_shared_count . ' বার)');
        }

        return back()->with('error', 'ফেসবুক পোস্টে ব্যর্থ হয়েছে: ' . ($result['message'] ?? 'অজানা ত্রুটি'));
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }

    private function formOptions(): array
    {
        return [
            'mentors' => Mentor::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ];
    }
}
