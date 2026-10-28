<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('ai_vision_limit')->default(0)->after('ai_chat_minutes');
            $table->unsignedInteger('upload_book_limit')->default(1)->after('ai_vision_limit');
            $table->unsignedInteger('monthly_reupload_limit')->default(2)->after('upload_book_limit');
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('ai_vision_remaining')->default(0)->after('ai_chat_minutes_remaining');
            $table->unsignedInteger('monthly_reuploads_remaining')->default(0)->after('ai_vision_remaining');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('ai_trial_vision_used')->default(0)->after('ai_trial_minutes_used');
            $table->unsignedInteger('trial_reuploads_count')->default(0)->after('ai_trial_vision_used');
            $table->string('trial_reuploads_month', 7)->nullable()->after('trial_reuploads_count');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['ai_trial_vision_used', 'trial_reuploads_count', 'trial_reuploads_month']);
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['ai_vision_remaining', 'monthly_reuploads_remaining']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['ai_vision_limit', 'upload_book_limit', 'monthly_reupload_limit']);
        });
    }
};
