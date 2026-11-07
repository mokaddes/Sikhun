<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'id' => 4,
                'name' => 'ফ্রি / Free',
                'slug' => 'free',
                'description' => 'নতুন শিক্ষার্থীদের জন্য বেসিক ট্রায়াল প্যাকেজ',
                'price_monthly' => 0.00,
                'ai_chat_minutes' => 20,
                'ai_vision_limit' => 3,
                'upload_book_limit' => 1,
                'monthly_reupload_limit' => 1,
                'ai_exam_count' => 5,
                'trial_ai_minutes' => 10,
                'ai_provider_id' => 6, // Deepseek V4 flash
                'ai_model' => 'DeepSeek V4 Flash',
                'features' => [
                    'AI চ্যাট (২০ মিনিট/মাস)',
                    '৩টি AI Vision ইমেজ সমাধান',
                    '১টি পার্সোনাল বই আপলোড',
                    'মাসিক ১ বার বই পরিবর্তন',
                    '৫টি AI প্র্যাকটিস টেস্ট',
                    'ফ্রি ট্রায়াল সাপোর্ট',
                ],
                'is_free' => true,
                'is_active' => true,
            ],
            [
                'id' => 1,
                'name' => 'স্টার্টার লার্নার',
                'slug' => 'starter',
                'description' => 'সাশ্রয়ী মূল্যে পূর্ণাঙ্গ পাঠ্যবই পড়াশোনার সেরা পছন্দ',
                'price_monthly' => 99.00,
                'ai_chat_minutes' => 120,
                'ai_vision_limit' => 20,
                'upload_book_limit' => 3,
                'monthly_reupload_limit' => 3,
                'ai_exam_count' => 30,
                'trial_ai_minutes' => 10,
                'ai_provider_id' => 6, // Deepseek V4 flash
                'ai_model' => 'DeepSeek V4 Flash',
                'features' => [
                    'AI চ্যাট (১২০ মিনিট/মাস)',
                    '২০টি AI Vision ইমেজ সমাধান',
                    '৩টি নিজস্ব বই আপলোড স্লট',
                    'মাসিক ৩ বার বই পরিবর্তন',
                    '৩০টি AI অধ্যায়ভিত্তিক কুইজ',
                    'ফ্ল্যাশকার্ড জেনারেটর',
                    'স্ট্যান্ডার্ড সাপোর্ট',
                ],
                'is_free' => false,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'প্রো স্কলার',
                'slug' => 'pro',
                'description' => 'SSC, HSC এবং বোর্ড পরীক্ষার পূর্ণাঙ্গ প্রস্তুতির সেরা প্যাকেজ',
                'price_monthly' => 199.00,
                'ai_chat_minutes' => 400,
                'ai_vision_limit' => 80,
                'upload_book_limit' => 10,
                'monthly_reupload_limit' => 10,
                'ai_exam_count' => 120,
                'trial_ai_minutes' => 10,
                'ai_provider_id' => 4, // Blue Minds
                'ai_model' => 'nvidia/ising-calibration-1.5-31b',
                'features' => [
                    'AI চ্যাট (৪০০ মিনিট/মাস)',
                    '৮০টি AI Vision ইমেজ সমাধান',
                    '১০টি নিজস্ব বই আপলোড স্লট',
                    'মাসিক ১০ বার বই পরিবর্তন',
                    '১২০টি পূর্ণাঙ্গ AI মডেল টেস্ট',
                    'লিখিত সৃজনশীল খাতা মূল্যায়ন',
                    'স্মার্ট স্টাডি শিডিউল রুটিন',
                    'হাই-রিজনিং দ্রুত রেসপন্স',
                ],
                'is_free' => false,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'এলিট মাস্টার',
                'slug' => 'elite',
                'description' => 'বুয়েট, মেডিকেল, বিশ্ববিদ্যালয় ভর্তি ও বিসিএস প্রস্তুতিতে সর্বোচ্চ নির্ভুলতা',
                'price_monthly' => 399.00,
                'ai_chat_minutes' => 1200,
                'ai_vision_limit' => 250,
                'upload_book_limit' => 30,
                'monthly_reupload_limit' => 30,
                'ai_exam_count' => 500,
                'trial_ai_minutes' => 15,
                'ai_provider_id' => 5, // Agent Router (gpt-6.1-sol)
                'ai_model' => 'gpt-6.1-sol',
                'features' => [
                    'AI চ্যাট (১২০০ মিনিট/মাস - আনলিমিটেড প্রায়)',
                    '২৫০টি AI Vision ইমেজ সমাধান',
                    '৩০টি নিজস্ব বই আপলোড স্লট',
                    'মাসিক ৩০ বার বই পরিবর্তন',
                    'আনলিমিটেড অ্যাডভান্সড মডেল টেস্ট',
                    'টপ-টায়ার হাই-রিজনিং AI মডেল',
                    'জটিল সৃজনশীল ও রিটেন খাতা মূল্যায়ন',
                    'ভিআইপি প্রায়োরিটি ও ডেডিকেটেড হেল্প',
                ],
                'is_free' => false,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            $id = $plan['id'];
            unset($plan['id']);
            $existing = Plan::find($id);
            if ($existing) {
                $existing->update($plan);
            } else {
                Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
            }
        }
    }
}
