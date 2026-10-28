<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\AiProvider;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Plans/Index', [
            'plans' => Plan::with(['aiProvider:id,name,type,model_name'])->withCount('subscriptions')->orderBy('price_monthly')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Plans/Form', [
            'plan' => null,
            'providers' => AiProvider::where('is_active', true)->orderBy('name')->get(['id', 'name', 'type', 'model_name']),
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
            'providers' => AiProvider::where('is_active', true)->orderBy('name')->get(['id', 'name', 'type', 'model_name']),
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
        $data['ai_vision_limit'] = (int) ($data['ai_vision_limit'] ?? 0);
        $data['upload_book_limit'] = (int) ($data['upload_book_limit'] ?? 1);
        $data['monthly_reupload_limit'] = (int) ($data['monthly_reupload_limit'] ?? 2);

        return $data;
    }
}
