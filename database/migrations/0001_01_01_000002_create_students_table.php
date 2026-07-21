<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['ssc', 'hsc', 'university', 'job']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->integer('ai_trial_minutes_used')->default(0);
            $table->string('referral_code', 20)->unique();
            $table->foreignId('referred_by_student_id')->nullable()->references('id')->on('students')->nullOnDelete();
            $table->string('avatar')->nullable();
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('system');
            $table->boolean('leaderboard_opt_out')->default(false);
            $table->rememberToken();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('students');
    }
};
