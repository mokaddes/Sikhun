<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MentorRequest;
use App\Models\Mentor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MentorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Mentors/Index', [
            'mentors' => Mentor::withCount('courses')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Mentors/Form', ['mentor' => null]);
    }

    public function store(MentorRequest $request): RedirectResponse
    {
        Mentor::create($this->prepared($request));

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor created.');
    }

    public function edit(Mentor $mentor): Response
    {
        return Inertia::render('Admin/Mentors/Form', [
            'mentor' => array_merge($mentor->toArray(), [
                'expertise' => implode(', ', $mentor->expertise ?? []),
            ]),
        ]);
    }

    public function update(MentorRequest $request, Mentor $mentor): RedirectResponse
    {
        $mentor->update($this->prepared($request));

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor updated.');
    }

    public function destroy(Mentor $mentor): RedirectResponse
    {
        $mentor->delete();

        return back()->with('success', 'Mentor deleted.');
    }

    private function prepared(MentorRequest $request): array
    {
        $data = $request->validated();
        $data['expertise'] = $data['expertise']
            ? array_map('trim', explode(',', $data['expertise']))
            : [];

        return $data;
    }
}
