<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hierarchical chapter/section structure parsed out of a book's PDF
 * (parent_id gives Chapter → Section 2.1 → Section 2.1.1 nesting).
 *
 * start_page/end_page map each chapter onto the ORIGINAL PDF's page
 * numbering, so chapter navigation can deep-link into the existing
 * watermarked page-image reader without any reader changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('book_chapters')->cascadeOnDelete();
            $table->string('chapter_number', 20)->nullable();
            $table->string('title');
            $table->string('slug');
            $table->unsignedTinyInteger('level')->default(1); // 1 = chapter, 2 = section, 3 = subsection…
            $table->unsignedInteger('start_page')->nullable();
            $table->unsignedInteger('end_page')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['book_id', 'sort_order']);
            $table->index(['book_id', 'parent_id']);
            $table->index('start_page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
