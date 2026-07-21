<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseSectionRequest;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;

class CourseSectionController extends Controller
{
    public function store(CourseSectionRequest $request, Course $course): RedirectResponse
    {
        $course->sections()->create([
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? $course->sections()->count() + 1,
        ]);

        return back()->with('success', 'Section added.');
    }

    public function update(CourseSectionRequest $request, Course $course, CourseSection $section): RedirectResponse
    {
        $section->update($request->validated());

        return back()->with('success', 'Section updated.');
    }

    public function destroy(Course $course, CourseSection $section): RedirectResponse
    {
        $section->delete(); // lessons cascade via FK

        return back()->with('success', 'Section deleted.');
    }
}
