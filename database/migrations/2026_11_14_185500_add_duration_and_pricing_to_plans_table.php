<?php

use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'duration_months')) {
                $table->unsignedSmallInteger('duration_months')->default(1)->after('target_segment');
            }
            if (! Schema::hasColumn('plans', 'price_total')) {
                $table->decimal('price_total', 8, 2)->nullable()->after('price_monthly');
            }
            if (! Schema::hasColumn('plans', 'badge_text')) {
                $table->string('badge_text', 100)->nullable()->after('name');
            }
        });

        // Seed initial admission and job bundles into database so admin can manage them
        $bundles = [
            [
                'name' => 'Admission Crash Pack (3 Months)',
                'slug' => 'admission-crash-pack-3m',
                'target_segment' => 'admission',
                'duration_months' => 3,
                'price_total' => 450.00,
                'price_monthly' => 150.00,
                'badge_text' => '3 Months Crash Course',
                'description' => 'Intensive revision and full-length exam model test package for final university admission preparation.',
                'ai_chat_minutes' => 500,
                'ai_vision_limit' => 120,
                'upload_book_limit' => 5,
                'monthly_reupload_limit' => 5,
                'ai_exam_count' => 60,
                'ai_explanation_count' => 100,
                'features' => [
                    '৫০০ মিনিট প্রিমিয়াম AI ডাউট সলভ ও স্টাডি চ্যাট',
                    '১২০টি ম্যাথ, ফিজিক্স ও কেমিস্ট্রি সমীকরণ ছবি বিশ্লেষণ',
                    '৬০টি পূর্ণাঙ্গ বিশ্ববিদ্যালয় মডেল টেস্ট ও নেগেটিভ মার্কিং',
                    '১০০টি বিস্তারিত AI প্রশ্ন ব্যাখ্যা ও ট্রিকস',
                    '৫টি স্পেশাল ভর্তি প্রস্তুতি বই ও হ্যান্ডনোট শেলফ',
                    'ভর্তি পরীক্ষা স্পেশাল সাপোর্ট ও ডাউট ক্লিয়ারিং',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
            [
                'name' => 'Admission Success Pass (6 Months)',
                'slug' => 'admission-success-pass-6m',
                'target_segment' => 'admission',
                'duration_months' => 6,
                'price_total' => 850.00,
                'price_monthly' => 141.67,
                'badge_text' => '6 Months Complete Pass • 20% Mega Savings',
                'description' => 'Complete end-to-end coverage from post-HSC to university admission.',
                'ai_chat_minutes' => 1200,
                'ai_vision_limit' => 350,
                'upload_book_limit' => 15,
                'monthly_reupload_limit' => 15,
                'ai_exam_count' => 150,
                'ai_explanation_count' => 300,
                'features' => [
                    '১২০০ মিনিট আনলিমিটেড AI পার্সোনাল টিউটর চ্যাট',
                    '৩৫০টি কমপ্লেক্স ডায়াগ্রাম ও ক্যালকুলেশন ইমেজ সলভার',
                    '১৫০টি অধ্যায়ভিত্তিক ও ভার্সিটি স্ট্যান্ডার্ড মডেল টেস্ট',
                    '৩০০টি প্রশ্ন গভীর বিশ্লেষণ ও AI ব্যাখ্যা কোটা',
                    '১৫টি বই ও লেকচার শিট এক সাথে আপলোড ও রি-আপলোড',
                    'মেডিকেল, বুয়েট, ঢাবি ও গুচ্ছ স্পেশাল কোহোর্ট সাপোর্ট',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
            [
                'name' => 'Govt Job Crash Pack (3 Months)',
                'slug' => 'govt-job-crash-pack-3m',
                'target_segment' => 'job',
                'duration_months' => 3,
                'price_total' => 500.00,
                'price_monthly' => 166.67,
                'badge_text' => '3 Months Job Prep',
                'description' => 'Fast-track preparation for bank, ministry, and 10th-20th grade recruitment exams.',
                'ai_chat_minutes' => 500,
                'ai_vision_limit' => 100,
                'upload_book_limit' => 5,
                'monthly_reupload_limit' => 5,
                'ai_exam_count' => 80,
                'ai_explanation_count' => 150,
                'features' => [
                    '৫০০ মিনিট সরকারি চাকরি ও ব্যাংক স্পেশাল AI অ্যাসিস্ট্যান্ট',
                    '১০০টি ম্যাথ শর্টকাট ও জিকে চার্ট বিশ্লেষণ',
                    '৮০টি মন্ত্রণালয় ও সমন্বিত ব্যাংক প্রিলিমিনারি টেস্ট',
                    '১৫০টি নিয়োগ পরীক্ষার প্রশ্নের তাৎক্ষণিক AI ব্যাখ্যা',
                    '৫টি সরকারি নিয়োগ সহায়িকা ও জব সল্যুশন বুক শেলফ',
                    'সার্কুলার এলার্ট ও জব ট্র্যাকিং সুবিধা',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
            [
                'name' => 'BCS & Bank Complete Pass (6 Months)',
                'slug' => 'bcs-bank-complete-pass-6m',
                'target_segment' => 'job',
                'duration_months' => 6,
                'price_total' => 950.00,
                'price_monthly' => 158.33,
                'badge_text' => '6 Months Complete Pass • Best Value',
                'description' => 'Comprehensive BPSC 50th/51th BCS Preli & Senior Officer Bank recruitment master pass.',
                'ai_chat_minutes' => 1500,
                'ai_vision_limit' => 450,
                'upload_book_limit' => 20,
                'monthly_reupload_limit' => 20,
                'ai_exam_count' => 200,
                'ai_explanation_count' => 500,
                'features' => [
                    '১৫০০ মিনিট প্রফেশনাল বিসিএস ক্যাডার স্টাডি গাইডেন্স',
                    '৪৫০টি বড় আকারের ডেটা টেবিল ও ইমেজ অ্যানালাইসিস',
                    '২০০টি পূর্ণাঙ্গ ২০০ মার্কসের বিসিএস ও ব্যাংক মডেল টেস্ট',
                    '৫০০টি প্রশ্নের ডিপ AI ব্যাখ্যা ও রেফারেন্স সোর্স',
                    '২০টি বই ও জব ডাইজেস্ট রিডিং সাপোর্ট',
                    'ভুল খাতা আনলিমিটেড অ্যানালাইসিস ও রি-টেস্ট',
                ],
                'is_active' => true,
                'is_free' => false,
            ],
        ];

        foreach ($bundles as $bundle) {
            Plan::firstOrCreate(['slug' => $bundle['slug']], $bundle);
        }
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['duration_months', 'price_total', 'badge_text']);
        });
    }
};
