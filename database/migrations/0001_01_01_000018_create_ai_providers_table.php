<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['openai', 'gemini', 'claude', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface']);
            $table->text('api_key')->nullable(); // encrypted cast in model
            $table->string('model_name', 200);
            $table->string('api_endpoint', 500)->nullable();
            $table->string('use_case', 50); // book_chat, exam_gen, flashcard_gen, essay_grade, schedule_gen, notification_gen, support_bot
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_tokens')->default(2000);
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->timestamps();

            $table->index('use_case');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
