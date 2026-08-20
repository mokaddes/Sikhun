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
    private const IMAGE_KEYS = ['site_logo', 'site_favicon', 'seo_image'];

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
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_email' => ['required', 'email'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_logo' => $request->hasFile('site_logo') ? ['nullable', 'mimes:png,jpg,jpeg,webp,svg', 'max:5120'] : ['nullable'],
            'site_favicon' => $request->hasFile('site_favicon') ? ['nullable', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:5120'] : ['nullable'],
            'seo_image' => $request->hasFile('seo_image') ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'] : ['nullable'],
            'remove_site_logo' => ['nullable', 'boolean'],
            'remove_site_favicon' => ['nullable', 'boolean'],
            'remove_seo_image' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'referrer_reward_amount' => ['required', 'numeric', 'min:0'],
            'referee_reward_amount' => ['required', 'numeric', 'min:0'],
            'max_referral_per_month' => ['required', 'integer', 'min:0'],
            'support_bot_system_prompt' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach ($validated as $key => $value) {
            // An untouched image field arrives as an empty string or null;
            // never let that clobber an already-stored image path.
            if (in_array($key, self::IMAGE_KEYS) && in_array($value, ['', null], true)) {
                continue;
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
}
