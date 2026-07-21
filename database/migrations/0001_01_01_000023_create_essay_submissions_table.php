<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('essay_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('grading_type', ['hsc_bangla', 'hsc_english', 'general', 'custom_rubric']);
            $table->longText('essay_text');
            $table->json('result')->nullable(); // {total_score, max_score, breakdown, feedback, improved_version}
            $table->enum('status', ['grading', 'completed', 'failed'])->default('grading');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('essay_submissions');
    }
};
