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
                'name' => 'স্টার্টার',
                'slug' => 'starter',
                'description' => 'নতুন শিক্ষার্থীদের জন্য আদর্শ পরিকল্পনা',
                'price_monthly' => 99.00,
                'ai_chat_minutes' => 60,
                'ai_vision_limit' => 20,
                'upload_book_limit' => 3,
                'monthly_reupload_limit' => 5,
                'ai_exam_count' => 20,
                'trial_ai_minutes' => 10,
                'features' => [
                    'AI Chat (৬০ মিনিট/মাস)',
                    '২০টি AI ভিশন / ছবি সমাধান',
                    '৩টি নিজস্ব বই স্লট (মাসে ৫ বার রিলোড)',
                    '২০টি AI পরীক্ষা/মাস',
                    'ফ্ল্যাশকার্ড জেনারেটর',
                    'সাধারণ সাপোর্ট',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'প্রো',
                'slug' => 'pro',
                'description' => 'সিরিয়াস শিক্ষার্থীদের জন্য সেরা পছন্দ',
                'price_monthly' => 199.00,
                'ai_chat_minutes' => 300,
                'ai_vision_limit' => 100,
                'upload_book_limit' => 10,
                'monthly_reupload_limit' => 20,
                'ai_exam_count' => 100,
                'trial_ai_minutes' => 10,
                'features' => [
                    'AI Chat (৩০০ মিনিট/মাস)',
                    '১০০টি AI ভিশন / ছবি সমাধান',
                    '১০টি নিজস্ব বই স্লট (মাসে ২০ বার রিলোড)',
                    '১০০টি AI পরীক্ষা/মাস',
                    'এসে গ্রেডার ও স্টাডি শিডিউল',
                    'প্রায়োরিটি সাপোর্ট',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'প্রিমিয়াম',
                'slug' => 'premium',
                'description' => 'সীমাহীন অ্যাক্সেস — HSC টপারদের জন্য',
                'price_monthly' => 399.00,
                'ai_chat_minutes' => 999,
                'ai_vision_limit' => 300,
                'upload_book_limit' => 25,
                'monthly_reupload_limit' => 50,
                'ai_exam_count' => 999,
                'trial_ai_minutes' => 15,
                'features' => [
                    'আনলিমিটেড AI চ্যাট',
                    '৩০০টি AI ভিশন / ছবি সমাধান',
                    '২৫টি নিজস্ব বই স্লট (মাসে ৫০ বার রিলোড)',
                    'আনলিমিটেড AI পরীক্ষা',
                    'সব AI ফিচার ও হোয়াটসঅ্যাপ সাপোর্ট',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
