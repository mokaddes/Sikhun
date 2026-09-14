<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Formulas (stored as LaTeX when the parser provides it) so math/science
 * questions get "F = ma" rendered and explained instead of lost during
 * plain text extraction.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('book_pages')->nullOnDelete();
            $table->foreignId('element_id')->nullable()->constrained('book_elements')->nullOnDelete();
            $table->text('latex')->nullable();
            $table->text('content')->nullable();
            $table->json('bbox')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'chapter_id']);
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_formulas');
    }
};
