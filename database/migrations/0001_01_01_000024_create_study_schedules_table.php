<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('exam_date');
            $table->json('config'); // subjects, hours_per_day, weak_subjects, style, include_weekends
            $table->json('schedule_data')->nullable(); // day-by-day plan
            $table->enum('status', ['generating', 'active', 'completed', 'failed'])->default('generating');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_schedules');
    }
};
