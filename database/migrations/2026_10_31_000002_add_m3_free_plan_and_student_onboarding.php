<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('is_active');
        });

        DB::table('plans')->updateOrInsert(
            ['slug' => 'free'],
            [
                'name' => 'ফ্রি / Free',
                'description' => 'প্রয়োজনীয় পড়াশোনার টুলসহ বিনামূল্যের পরিকল্পনা / A free starting plan with essential study tools.',
                'price_monthly' => 0,
                'ai_chat_minutes' => 20,
                'ai_vision_limit' => 3,
                'upload_book_limit' => 1,
                'monthly_reupload_limit' => 1,
                'ai_exam_count' => 2,
                'trial_ai_minutes' => 0,
                'features' => json_encode([
                    'লাইব্রেরির সব ফ্রি বই / Free library books',
                    'মাসে ২০টি AI চ্যাট / 20 AI chats per month',
                    '১টি নিজস্ব বই / 1 personal book slot',
                    'মাসে ২টি AI পরীক্ষা / 2 AI exams per month',
                ]),
                'is_active' => true,
                'is_free' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Schema::table('students', function (Blueprint $table) {
            $table->json('onboarding_progress')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamp('first_ai_chat_at')->nullable();
            $table->timestamp('activated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_progress',
                'onboarding_completed_at',
                'first_ai_chat_at',
                'activated_at',
            ]);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
};
