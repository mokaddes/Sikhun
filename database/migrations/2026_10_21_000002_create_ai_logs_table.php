<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('user_type', 30)->default('system'); // student, admin, system, guest
            $table->string('user_name', 255)->nullable();
            $table->string('user_email', 255)->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->string('provider_name', 255)->nullable();
            $table->string('provider_type', 50)->nullable(); // openai_compatible, anthropic_compatible, google_gemini, custom
            $table->string('model_name', 200)->nullable();
            $table->string('use_case', 50)->index(); // book_chat, exam_gen, flashcard_gen, essay_grade, schedule_gen, etc.
            $table->longText('prompt')->nullable();
            $table->longText('response')->nullable();
            $table->string('status', 20)->default('success')->index(); // success, failed
            $table->text('error_message')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['use_case', 'created_at']);
            $table->index(['student_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['user_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
