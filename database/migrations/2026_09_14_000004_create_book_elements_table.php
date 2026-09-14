<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Generic parsed document element in reading order: text runs, headings,
 * tables, images, formulas, lists, captions, quotes. The `type` column
 * discriminates; parser-specific payload lives in `metadata` (json) so we
 * never hard-code one vendor's schema across the app.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('book_pages')->nullOnDelete();
            $table->string('type', 20); // text|heading|table|image|formula|list|caption|quote|other
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->json('bbox')->nullable(); // {x, y, width, height} on the page
            $table->unsignedInteger('page_number')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['book_id', 'type']);
            $table->index(['book_id', 'sort_order']);
            $table->index('chapter_id');
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_elements');
    }
};
