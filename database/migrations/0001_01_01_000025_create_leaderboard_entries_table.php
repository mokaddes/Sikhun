<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->cascadeOnDelete();
            $table->string('subject', 100)->nullable();
            $table->foreignId('book_id')->nullable()->references('id')->on('books')->nullOnDelete();
            $table->enum('student_type', ['ssc', 'hsc', 'university', 'job']);
            $table->integer('score');
            $table->integer('total');
            $table->decimal('percentage', 5, 2);
            $table->integer('questions_count');
            $table->unsignedTinyInteger('week_number');
            $table->unsignedTinyInteger('month_number');
            $table->unsignedSmallInteger('year');
            $table->timestamps();

            $table->index(['student_type', 'year', 'week_number']);
            $table->index(['student_type', 'year', 'month_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
    }
};
