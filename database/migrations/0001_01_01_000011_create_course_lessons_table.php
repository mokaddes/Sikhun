<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'text', 'pdf'])->default('video');
            $table->string('video_url')->nullable();
            $table->longText('text_content')->nullable();
            $table->string('pdf_path')->nullable();
            $table->boolean('is_free_preview')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
