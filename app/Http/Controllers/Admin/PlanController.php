<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\AiProvider;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        $settings = app(\App\Services\SiteSettingService::class);
        $customConfig = [
            'custom_plan_enabled' => filter_var($settings->get('custom_plan_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'custom_plan_base_fee' => (float) $settings->get('custom_plan_base_fee', 35.0),
            'custom_plan_min_monthly' => (float) $settings->get('custom_plan_min_monthly', 149.0),
            'custom_plan_rate_minute' => (float) $settings->get('custom_plan_rate_minute', 0.22),
            'custom_plan_rate_image' => (float) $settings->get('custom_plan_rate_image', 1.10),
            'custom_plan_rate_book' => (float) $settings->get('custom_plan_rate_book', 14.00),
            'custom_plan_rate_reupload' => (float) $settings->get('custom_plan_rate_reupload', 5.00),
            'custom_plan_rate_quiz' => (float) $settings->get('custom_plan_rate_quiz', 2.50),
            'custom_plan_rate_explanation' => (float) $settings->get('custom_plan_rate_explanation', 1.00),
        ];

        return Inertia::render('Admin/Plans/Index', [
            'plans' => Plan::with(['aiProvider:id,name,type,model_name'])->withCount('subscriptions')->orderBy('price_monthly')->get(),
            'customPlanConfig' => $customConfig,
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

    public function syncToPagePilot(\App\Services\Social\PagePilotSyncService $sync): RedirectResponse
    {
        $result = $sync->syncPlansToPagePilot();

        if ($result['success'] ?? false) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function updateCustomConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'custom_plan_enabled' => ['required', 'boolean'],
            'custom_plan_base_fee' => ['required', 'numeric', 'min:0'],
            'custom_plan_min_monthly' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_minute' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_image' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_book' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_reupload' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_quiz' => ['required', 'numeric', 'min:0'],
            'custom_plan_rate_explanation' => ['required', 'numeric', 'min:0'],
        ]);

        $settings = app(\App\Services\SiteSettingService::class);
        foreach ($validated as $key => $val) {
            $settings->set($key, (string) $val);
        }

        return back()->with('success', 'কাস্টম প্ল্যান কনফিগারেশন সফলভাবে আপডেট করা হয়েছে।');
    }

    private function prepared(PlanRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_free'] = $request->boolean('is_free');
        $data['duration_months'] = max(1, (int) ($data['duration_months'] ?? 1));
        if (! isset($data['price_total']) || $data['price_total'] === '' || $data['price_total'] === null) {
            $data['price_total'] = round((float) $data['price_monthly'] * $data['duration_months'], 2);
        } else {
            $data['price_total'] = (float) $data['price_total'];
        }
        $data['badge_text'] = ! empty($data['badge_text']) ? trim($data['badge_text']) : null;
        $data['features'] = $data['features']
            ? array_values(array_filter(array_map('trim', explode("\n", $data['features']))))
            : [];
        $data['ai_vision_limit'] = (int) ($data['ai_vision_limit'] ?? 0);
        $data['ai_explanation_count'] = (int) ($data['ai_explanation_count'] ?? 50);
        $data['upload_book_limit'] = (int) ($data['upload_book_limit'] ?? 1);
        $data['monthly_reupload_limit'] = (int) ($data['monthly_reupload_limit'] ?? 2);

        return $data;
    }
}
