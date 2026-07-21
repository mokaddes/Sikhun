<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublicationRequest;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Publications/Index', [
            'publications' => Publication::withCount('books')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Publications/Form', ['publication' => null]);
    }

    public function store(PublicationRequest $request): RedirectResponse
    {
        Publication::create($request->validated());

        return redirect()->route('admin.publications.index')->with('success', 'Publication created.');
    }

    public function edit(Publication $publication): Response
    {
        return Inertia::render('Admin/Publications/Form', ['publication' => $publication]);
    }

    public function update(PublicationRequest $request, Publication $publication): RedirectResponse
    {
        $publication->update($request->validated());

        return redirect()->route('admin.publications.index')->with('success', 'Publication updated.');
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $publication->delete();

        return back()->with('success', 'Publication deleted.');
    }
}
