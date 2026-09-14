<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Page-level representation of a parsed book. Lets AI answers cite an
 * exact page ("Chapter 3 — Page 42") and lets chapter purchase gating
 * resolve "what chapter is page N in" without re-reading the PDF.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->unsignedInteger('page_number');
            $table->foreignId('chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'page_number']);
            $table->index('chapter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_pages');
    }
};
