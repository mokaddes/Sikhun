<?php

namespace App\Http\Controllers\Api;

use App\Services\SiteSettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends BaseApiController
{
    public function index(SiteSettingService $settings): JsonResponse
    {
        $all = $settings->all();

        return $this->success([
            'site_name' => $all['site_name'] ?? 'Sikhun.com',
            'site_name_bn' => $all['site_name_bn'] ?? 'শিখুন',
            'site_tagline' => $all['site_tagline'] ?? 'YOUR AI STUDY DESK',
            'site_tagline_bn' => $all['site_tagline_bn'] ?? 'আপনার এআই পড়ার টেবিল',
            'logo_url' => !empty($all['site_logo']) ? asset('storage/'.$all['site_logo']) : null,
            'favicon_url' => !empty($all['site_favicon']) ? asset('storage/'.$all['site_favicon']) : null,
            'seo_image_url' => !empty($all['seo_image']) ? asset('storage/'.$all['seo_image']) : null,
            'site_email' => $all['site_email'] ?? 'support@sikhun.com',
            'site_phone' => $all['site_phone'] ?? null,
            'facebook_url' => $all['facebook_url'] ?? null,
            'youtube_url' => $all['youtube_url'] ?? null,
            'app_version' => '1.0.0',
        ]);
    }
}
