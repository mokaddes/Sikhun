<?php

use App\Models\SiteSetting;
use App\Services\SiteSettingService;
use Illuminate\Database\Migrations\Migration;

/**
 * Ensure a `site_name` row exists in the site_settings table so the site
 * name is always available to the header, footer, navbar and dashboard.
 * Uses updateOrInsert so an existing admin-configured value is preserved.
 */
return new class extends Migration
{
    public function up(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'site_name'],
            ['value' => config('app.name', 'Sikhun.com')],
        );

        app(SiteSettingService::class)->forget('site_name');
        app(SiteSettingService::class)->forget('site_logo');
        app(SiteSettingService::class)->forget('site_favicon');
        app(SiteSettingService::class)->forget('seo_image');
    }

    public function down(): void
    {
        SiteSetting::query()->where('key', 'site_name')->delete();

        app(SiteSettingService::class)->forget('site_name');
    }
};