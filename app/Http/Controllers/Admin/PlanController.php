<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\Book;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Plans/Index', [
            'plans' => Plan::withCount('subscriptions')->orderBy('price_monthly')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Plans/Form', [
            'plan' => null,
            'books' => Book::published()->get(['id', 'title']),
        ]);
    }

    public function store(PlanRequest $request): RedirectResponse
    {
        Plan::create($this->prepared($request));

        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan): Response
    {
        return Inertia::render('Admin/Plans/Form', [
            'plan' => array_merge($plan->toArray(), [
                'features' => implode("\n", $plan->features ?? []),
            ]),
            'books' => Book::published()->get(['id', 'title']),
        ]);
    }

    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($this->prepared($request));

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return back()->with('success', 'Plan deleted.');
    }

    private function prepared(PlanRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['features'] = $data['features']
            ? array_values(array_filter(array_map('trim', explode("\n", $data['features']))))
            : [];
        $data['gift_book_ids'] = $data['gift_book_ids'] ?? [];

        return $data;
    }
}
