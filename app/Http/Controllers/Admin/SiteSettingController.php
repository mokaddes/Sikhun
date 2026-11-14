<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    private const IMAGE_KEYS = [
        'site_logo', 'site_favicon', 'seo_image',
        'seo_admission_image', 'seo_job_image', 'seo_blog_image',
    ];

    private const REQUIRED_FIELDS = [
        'site_name',
        'site_email',
        'referrer_reward_amount',
        'referee_reward_amount',
        'max_referral_per_month',
    ];

    public function edit(): Response
    {
        $settings = SiteSetting::pluck('value', 'key');

        // Resolve stored file paths into public URLs for the image fields
        // so the form can preview the currently-saved image.
        $settings = $settings->merge(collect(self::IMAGE_KEYS)->mapWithKeys(function ($key) use ($settings) {
            $path = $settings[$key] ?? null;

            return [$key.'_url' => $path ? asset('storage/'.$path) : null];
        }));

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {

        $this->backfillRequiredFields($request);

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_name_bn' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_tagline_bn' => ['nullable', 'string', 'max:255'],
            'site_email' => ['required', 'email'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_logo' => $request->hasFile('site_logo') ? ['nullable', 'mimes:png,jpg,jpeg,webp,svg', 'max:5120'] : ['nullable'],
            'site_favicon' => $request->hasFile('site_favicon') ? ['nullable', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:5120'] : ['nullable'],
            'seo_image' => $request->hasFile('seo_image') ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'] : ['nullable'],
            'seo_admission_image' => $request->hasFile('seo_admission_image') ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'] : ['nullable'],
            'seo_job_image' => $request->hasFile('seo_job_image') ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'] : ['nullable'],
            'seo_blog_image' => $request->hasFile('seo_blog_image') ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'] : ['nullable'],
            'remove_site_logo' => ['nullable', 'boolean'],
            'remove_site_favicon' => ['nullable', 'boolean'],
            'remove_seo_image' => ['nullable', 'boolean'],
            'remove_seo_admission_image' => ['nullable', 'boolean'],
            'remove_seo_job_image' => ['nullable', 'boolean'],
            'remove_seo_blog_image' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'referrer_reward_amount' => ['required', 'numeric', 'min:0'],
            'referee_reward_amount' => ['required', 'numeric', 'min:0'],
            'max_referral_per_month' => ['required', 'integer', 'min:0'],
            'referral_reward_tiers' => ['nullable', 'string', 'max:5000', function ($attribute, $value, $fail) {
                if ($value === null || trim($value) === '') {
                    return;
                }

                $tiers = json_decode($value, true);
                if (! is_array($tiers) || ! array_is_list($tiers)) {
                    $fail('Reward tiers must be a JSON array.');
                    return;
                }

                $milestones = [];
                foreach ($tiers as $tier) {
                    if (! is_array($tier)
                        || ! isset($tier['milestone'], $tier['referrer_reward'], $tier['referee_reward'])
                        || filter_var($tier['milestone'], FILTER_VALIDATE_INT) === false
                        || (int) $tier['milestone'] < 1
                        || ! is_numeric($tier['referrer_reward']) || (float) $tier['referrer_reward'] < 0
                        || ! is_numeric($tier['referee_reward']) || (float) $tier['referee_reward'] < 0) {
                        $fail('Each tier needs a positive milestone and non-negative reward amounts.');
                        return;
                    }

                    if (in_array((int) $tier['milestone'], $milestones, true)) {
                        $fail('Reward tier milestones must be unique.');
                        return;
                    }
                    $milestones[] = (int) $tier['milestone'];
                }
            }],
            'support_bot_system_prompt' => ['nullable', 'string', 'max:2000'],
            'blog_digest_email' => ['nullable', 'email', 'max:255'],
            'blog_credit_enabled' => ['nullable', 'boolean'],
            'blog_daily_ai_limit' => ['nullable', 'integer', 'min:1', 'max:200'],
            'pagepilot_webhook_url' => ['nullable', 'string', 'max:500'],
            'pagepilot_api_key' => ['nullable', 'string', 'max:255'],
            'pagepilot_auto_publish' => ['nullable', 'boolean'],
        ]);

        foreach ($validated as $key => $value) {
            // File uploads and their remove flags are persisted separately
            // below; never send UploadedFile objects through the text setting
            // model's generic value column.
            if (in_array($key, self::IMAGE_KEYS, true) || str_starts_with($key, 'remove_')) {
                continue;
            }

            if ($key === 'referral_reward_tiers') {
                $value = $value ? json_decode($value, true) : null;
            }

            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            app(SiteSettingService::class)->forget($key);
        }

        Log::info('request', [
            'request' => $request->all(),
        ]);
        foreach (self::IMAGE_KEYS as $key) {
            if ($request->boolean('remove_'.$key)) {
                $this->deleteStoredImage($key);
                SiteSetting::updateOrCreate(['key' => $key], ['value' => null]);
                app(SiteSettingService::class)->forget($key);
            }
            Log::info('image key', [
                'key' => $key,
                'is_file' => $request->hasFile($key),
                'is_requeseted' => $request->get($key),
                ]);

            if ($request->hasFile($key)) {
                $this->deleteStoredImage($key);
                $path = $request->file($key)->store('site', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
                app(SiteSettingService::class)->forget($key);
            }
        }

        return back()->with('success', 'Settings saved.');
    }

    private function deleteStoredImage(string $key): void
    {
        $path = SiteSetting::where('key', $key)->value('value');

        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * A stale frontend may submit required fields as empty strings/nulls even
     * though a value is already stored. Backfill those from the DB so the
     * save never fails with "required" errors for values that already exist.
     */
    private function backfillRequiredFields(Request $request): void
    {
        $current = SiteSetting::pluck('value', 'key');

        foreach (self::REQUIRED_FIELDS as $key) {
            $submitted = $request->input($key);

            if (($submitted === null || $submitted === '') && !empty($current[$key])) {
                $request->merge([$key => $current[$key]]);
            }
        }
    }

    public function testPagePilot(\App\Services\Social\PagePilotSocialService $social): RedirectResponse
    {
        $result = $social->testConnection();

        if ($result['success'] ?? false) {
            return back()->with('success', 'PagePilotBD টেস্ট সফল হয়েছে! ফেসবুক পেজে টেস্ট পোস্ট ও প্রথম কমেন্ট পৌঁছেছে।');
        }

        return back()->with('error', 'PagePilotBD সংযোগে ব্যর্থ হয়েছে: ' . ($result['message'] ?? 'অজানা ত্রুটি'));
    }

    public function syncPagePilotKnowledge(\App\Services\Social\PagePilotSyncService $sync): RedirectResponse
    {
        $result = $sync->syncKnowledgeToPagePilot();

        if ($result['success'] ?? false) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
