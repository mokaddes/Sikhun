<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Rebuild all 4 subscription plans:
     * - Free Plan (0 Tk / Monthly) - Deepseek V4 flash
     * - Pro Study (99 Tk / Monthly) - Deepseek V4 flash
     * - Master Prep (199 Tk / Monthly) - Blue Minds AI
     * - Exam Pro (399 Tk / Monthly) - Agent Router (gpt-6.1-sol)
     */
    public function run(): void
    {
        $plans = [
            [
                'id' => 1,
                'name' => 'Free Plan',
                'slug' => 'free',
                'description' => 'নতুন শিক্ষার্থীদের জন্য বেসিক ট্রায়াল প্যাকেজ',
                'price_monthly' => 0.00,
                'ai_chat_minutes' => 20,
                'ai_vision_resolutions' => 3,
                'personal_book_uploads' => 1,
                'book_swaps_per_month' => 1,
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
                'id' => 2,
                'name' => 'Pro Study',
                'slug' => 'pro-study',
                'description' => 'দৈনন্দিন পড়াশোনা ও নিয়মিত অনুশীলনের সেরা পছন্দ',
                'price_monthly' => 99.00,
                'ai_chat_minutes' => 120,
                'ai_vision_resolutions' => 20,
                'personal_book_uploads' => 5,
                'book_swaps_per_month' => 5,
                'ai_exam_count' => 30,
                'trial_ai_minutes' => 0,
                'ai_provider_id' => 6, // Deepseek V4 flash
                'ai_model' => 'DeepSeek V4 Flash',
                'features' => [
                    'AI চ্যাট (১২০ মিনিট/মাস)',
                    '২০টি AI Vision ইমেজ সমাধান',
                    '৫টি পার্সোনাল বই আপলোড',
                    'মাসিক ৫ বার বই পরিবর্তন',
                    '৩০টি স্পেশাল AI মডেল টেস্ট',
                    'চ্যাপ্টার ভিত্তিক প্রশ্ন ব্যাংক অ্যাক্সেস',
                    'ইমেইল ও চ্যাট সাপোর্ট',
                ],
                'is_free' => false,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Master Prep',
                'slug' => 'master-prep',
                'description' => 'ভর্তি ও চাকরির কঠিন প্রস্তুতির জন্য হাই-পাওয়ার্ড প্যাকেজ',
                'price_monthly' => 199.00,
                'ai_chat_minutes' => 300,
                'ai_vision_resolutions' => 60,
                'personal_book_uploads' => 15,
                'book_swaps_per_month' => 15,
                'ai_exam_count' => 100,
                'trial_ai_minutes' => 0,
                'ai_provider_id' => 4, // Blue Minds
                'ai_model' => 'Blue Minds Reasoning Pro',
                'features' => [
                    'AI চ্যাট (৩০০ মিনিট/মাস)',
                    '৬০টি AI Vision সমাধান',
                    '১৫টি পার্সোনাল বই আপলোড',
                    'মাসিক ১৫ বার বই পরিবর্তন',
                    '১০০টি পূর্ণাঙ্গ মডেল টেস্ট',
                    'স্মার্ট হাইপার-অ্যানালাইটিক্স রিপোর্ট',
                    'ব্লু মাইন্ডস হাই-অ্যাকিউরেসি এআই',
                    'প্রাইওরিটি সাপোর্ট',
                ],
                'is_free' => false,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Exam Pro',
                'slug' => 'exam-pro',
                'description' => 'সর্বোচ্চ ক্ষমতা সম্পন্ন আনলিমিটেড প্রিমিয়াম বান্ডেল',
                'price_monthly' => 399.00,
                'ai_chat_minutes' => 800,
                'ai_vision_resolutions' => 200,
                'personal_book_uploads' => 50,
                'book_swaps_per_month' => 50,
                'ai_exam_count' => 500,
                'trial_ai_minutes' => 0,
                'ai_provider_id' => 5, // Agent Router (gpt-6.1-sol)
                'ai_model' => 'Agent Router (gpt-6.1-sol)',
                'features' => [
                    'AI চ্যাট (৮০০ মিনিট/মাস)',
                    '২০০টি AI Vision সমস্যা সমাধান',
                    '৫০টি পার্সোনাল বই আপলোড',
                    'আনলিমিটেড বই পরিবর্তন',
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

            if (isset($plan['ai_provider_id']) && ! \App\Models\AiProvider::whereKey($plan['ai_provider_id'])->exists()) {
                $plan['ai_provider_id'] = null;
            }

            $existing = Plan::find($id);
            if ($existing) {
                $existing->update($plan);
            } else {
                Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
            }
        }
    }
}
