<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    private const IMAGE_KEYS = ['site_logo', 'site_favicon', 'seo_image'];

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
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_email' => ['required', 'email'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_logo' => ['nullable', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'site_favicon' => ['nullable', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
            'seo_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
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
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            Cache::forget("site_setting:{$key}");
        }

        foreach (self::IMAGE_KEYS as $key) {
            if ($request->boolean('remove_'.$key)) {
                $this->deleteStoredImage($key);
                SiteSetting::updateOrCreate(['key' => $key], ['value' => null]);
                Cache::forget("site_setting:{$key}");
            }

            if ($request->hasFile($key)) {
                $this->deleteStoredImage($key);
                $path = $request->file($key)->store('site', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
                Cache::forget("site_setting:{$key}");
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
}
