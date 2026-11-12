<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->cascadeOnDelete();
            $table->foreignId('question_bank_id')->nullable()->constrained('question_banks')->nullOnDelete();
            $table->string('question_identifier', 128)->nullable();
            $table->text('question_text');
            $table->string('question_type', 32)->default('mcq');
            $table->json('options')->nullable();
            $table->string('correct_option', 32)->nullable();
            $table->text('correct_answer')->nullable();
            $table->text('student_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_name', 128)->nullable();
            $table->string('topic_name', 128)->nullable();
            $table->string('origin_tag', 128)->nullable();
            $table->string('institution', 128)->nullable();
            $table->string('exam_name', 128)->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'is_correct']);
            $table->index(['student_id', 'subject_id']);
            $table->index(['student_id', 'origin_tag']);
            $table->index(['student_id', 'institution']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
