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
                'name' => 'ফ্রি / Free',
                'slug' => 'free',
                'description' => 'প্রয়োজনীয় পড়াশোনার টুলসহ বিনামূল্যের পরিকল্পনা / A free starting plan with essential study tools.',
                'price_monthly' => 0,
                'ai_chat_minutes' => 20,
                'ai_vision_limit' => 3,
                'upload_book_limit' => 1,
                'monthly_reupload_limit' => 1,
                'ai_exam_count' => 2,
                'trial_ai_minutes' => 0,
                'features' => [
                    'লাইব্রেরির সব ফ্রি বই / Free library books',
                    'মাসে ২০টি AI চ্যাট / 20 AI chats per month',
                    '১টি নিজস্ব বই / 1 personal book slot',
                    'মাসে ২টি AI পরীক্ষা / 2 AI exams per month',
                ],
                'is_active' => true,
                'is_free' => true,
            ],
            [
                'name' => 'স্ট্যান্ডার্ড লার্নার',
                'slug' => 'standard',
                'description' => 'নিয়মিত স্টাডি এবং নির্ভুল পাঠ্যবই সহায়তার জন্য',
                'price_monthly' => 149.00,
                'ai_chat_minutes' => 150,
                'ai_vision_limit' => 20,
                'upload_book_limit' => 3,
                'monthly_reupload_limit' => 5,
                'ai_exam_count' => 30,
                'trial_ai_minutes' => 10,
                'features' => [
                    'AI Book Chat (১৫০ মিনিট/মাস)',
                    '২০টি AI ভিশন / ছবি সমাধান',
                    '৩টি নিজস্ব বই স্লট (মাসে ৫ বার রিলোড)',
                    '৩০টি AI পরীক্ষা/মাস',
                    'ফ্ল্যাশকার্ড জেনারেটর',
                    'সাধারণ সাপোর্ট',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
            [

                'name' => 'প্রো স্কলার',
                'slug' => 'pro',
                'description' => 'SSC, HSC এবং বোর্ড পরীক্ষার পূর্ণাঙ্গ প্রস্তুতির সেরা পছন্দ',
                'price_monthly' => 299.00,
                'ai_chat_minutes' => 500,
                'ai_vision_limit' => 100,
                'upload_book_limit' => 10,
                'monthly_reupload_limit' => 20,
                'ai_exam_count' => 100,
                'trial_ai_minutes' => 10,
                'features' => [
                    'AI Book Chat (৫০০ মিনিট/মাস)',
                    '১০০টি AI ভিশন / ছবি সমাধান',
                    '১০টি নিজস্ব বই স্লট (মাসে ২০ বার রিলোড)',
                    '১০০টি AI পরীক্ষা ও মডেল টেস্ট',
                    'এসে গ্রেডার ও স্টাডি শিডিউল',
                    'প্রায়োরিটি সাপোর্ট ও দ্রুত রেসপন্স',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
            [

                'name' => 'এলিট এচিভার',
                'slug' => 'elite',
                'description' => 'বিশ্ববিদ্যালয় ভর্তি (BUET, Medical, DU) ও বিসিএস প্রস্তুতিতে সর্বোচ্চ নির্ভুলতা',
                'price_monthly' => 599.00,
                'ai_chat_minutes' => 1200,
                'ai_vision_limit' => 300,
                'upload_book_limit' => 25,
                'monthly_reupload_limit' => 50,
                'ai_exam_count' => 999,
                'trial_ai_minutes' => 15,
                'features' => [
                    'হাই-প্রিসিশন AI চ্যাট (১২০০ মিনিট/মাস)',
                    '৩০০টি AI ভিশন / ছবি সমাধান',
                    '২৫টি নিজস্ব বই স্লট (মাসে ৫০ বার রিলোড)',
                    'আনলিমিটেড AI পরীক্ষা ও মডেল টেস্ট',
                    'ডিটেইলড রিটেন / এসে মূল্যায়ন',
                    'ভিআইপি প্রায়োরিটি সাপোর্ট',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
