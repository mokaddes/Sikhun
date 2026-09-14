<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Images extracted from book PDFs. Binary data lives on the private
 * storage disk (path), never in MySQL; the row keeps provenance (page,
 * chapter, bbox) plus optional AI description / OCR text for
 * image-aware RAG. Public URLs are minted only through signed,
 * access-checked endpoints — never stored.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('book_pages')->nullOnDelete();
            $table->foreignId('element_id')->nullable()->constrained('book_elements')->nullOnDelete();
            $table->string('path')->nullable(); // private disk path
            $table->text('alt_text')->nullable();
            $table->text('description')->nullable(); // AI-generated, populated lazily
            $table->text('ocr_text')->nullable();
            $table->json('bbox')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'chapter_id']);
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_images');
    }
};
