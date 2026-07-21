<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Every SiteSetting lookup was previously an uncached `::where(...)->first()`
 * scattered across ReferralService, SupportController, ContactController,
 * etc — fine individually, but each one is a DB round-trip on a request
 * path that doesn't need fresh data every time. This centralizes it with
 * a 1-hour cache, invalidated on save (see Admin\SiteSettingController).
 */
class SiteSettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("site_setting:{$key}", 3600, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    public function forget(string $key): void
    {
        Cache::forget("site_setting:{$key}");
    }
}
