<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Sikhun.com',
            'site_tagline' => 'বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম',
            'site_email' => 'support@sikhun.com',
            'site_phone' => '+880 1XXXXXXXXX',
            'site_logo' => null,
            'site_favicon' => null,
            'seo_image' => null,
            'meta_title' => 'Sikhun.com — AI-Powered Learning for Bangladeshi Students',
            'meta_description' => 'ডিজিটালি বই পড়ুন, AI-এর সাথে চ্যাট করুন, পরীক্ষা দিন এবং ফ্ল্যাশকার্ড তৈরি করুন।',
            'meta_keywords' => 'sikhun, শিখুন, HSC, SSC, Bangladesh education, AI learning',
            'referrer_reward_amount' => 20,
            'referee_reward_amount' => 20,
            'max_referral_per_month' => 10,
            'ai_trial_minutes_default' => 10,
            'registration_open' => true,
            'maintenance_mode' => false,
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
