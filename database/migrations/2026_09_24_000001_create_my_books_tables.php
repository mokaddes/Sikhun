<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->integer('total_pages')->default(0);
            $table->string('processing_status')->default('pending');
            $table->text('processing_error')->nullable();
            $table->string('pdf_content_hash')->nullable();
            $table->timestamps();

            $table->index('student_id');
        });

        Schema::create('my_book_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_book_id')->constrained('my_books')->cascadeOnDelete();
            $table->integer('page_number');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->unique(['my_book_id', 'page_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_book_pages');
        Schema::dropIfExists('my_books');
    }
};