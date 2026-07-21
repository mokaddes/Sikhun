<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseLessonRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;

class CourseLessonController extends Controller
{
    public function store(CourseLessonRequest $request, Course $course, CourseSection $section): RedirectResponse
    {
        $data = $request->validated();
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $data['sort_order'] = $data['sort_order'] ?? $section->lessons()->count() + 1;

        $section->lessons()->create($data);

        return back()->with('success', 'Lesson added.');
    }

    public function update(CourseLessonRequest $request, Course $course, CourseSection $section, CourseLesson $lesson): RedirectResponse
    {
        $data = $request->validated();
        $data['is_free_preview'] = $request->boolean('is_free_preview');

        $lesson->update($data);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroy(Course $course, CourseSection $section, CourseLesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }
}
