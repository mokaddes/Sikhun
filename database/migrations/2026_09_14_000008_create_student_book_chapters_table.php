<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chapter-wise purchase ownership — one row per (student, book, chapter),
 * enforced by a DB unique constraint so two simultaneous purchase requests
 * can never create duplicate ownership, no matter which code path wins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_book_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('book_chapters')->cascadeOnDelete();
            $table->string('source', 30)->default('purchased'); // purchased|admin_gift|free
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'book_id', 'chapter_id']);
            $table->index(['student_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_book_chapters');
    }
};
