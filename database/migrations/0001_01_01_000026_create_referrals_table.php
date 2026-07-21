<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('referee_student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('status', ['pending', 'rewarded'])->default('pending');
            $table->decimal('referrer_reward', 8, 2)->nullable();
            $table->decimal('referee_reward', 8, 2)->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->unique(['referrer_student_id', 'referee_student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
