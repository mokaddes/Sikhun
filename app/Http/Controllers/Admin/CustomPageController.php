<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomPageRequest;
use App\Models\CustomPage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomPageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => CustomPage::orderBy('title_bn')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Form', ['page' => null]);
    }

    public function store(CustomPageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        // Keep the legacy single-language columns roughly in sync (Bengali
        // as the primary), purely so nothing that might still read them
        // (e.g. an old cached sitemap entry) sees a blank value.
        $data['title'] = $data['title_bn'];
        $data['content'] = $data['content_bn'];
        $data['meta_title'] = $data['meta_title_bn'];
        $data['meta_description'] = $data['meta_description_bn'];

        CustomPage::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(CustomPage $page): Response
    {
        return Inertia::render('Admin/Pages/Form', ['page' => $page]);
    }

    public function update(CustomPageRequest $request, CustomPage $page): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['title'] = $data['title_bn'];
        $data['content'] = $data['content_bn'];
        $data['meta_title'] = $data['meta_title_bn'];
        $data['meta_description'] = $data['meta_description_bn'];

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CustomPage $page): RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Page deleted.');
    }
}
