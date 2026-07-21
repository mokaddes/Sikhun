<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('source_type', ['book', 'chapter', 'page', 'topic', 'paragraph']);
            $table->foreignId('source_book_id')->nullable()->references('id')->on('books')->nullOnDelete();
            $table->string('source_chapter', 100)->nullable();
            $table->integer('source_page')->nullable();
            $table->text('source_text')->nullable();
            $table->json('config'); // {type, count, duration, mode}
            $table->json('questions')->nullable();
            $table->json('answers')->nullable();
            $table->integer('score')->default(0);
            $table->integer('total')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->enum('mode', ['practice', 'exam']);
            $table->enum('status', ['generating', 'in_progress', 'completed', 'abandoned', 'failed'])->default('generating');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'mode', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
