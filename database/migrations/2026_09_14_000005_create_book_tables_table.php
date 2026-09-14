<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Structured table data extracted from PDFs — headers + rows as JSON plus
 * markdown/html renderings, so table-aware RAG can hand the LLM a real
 * table instead of a flattened text blob. `content` shape follows parser
 * output but is conventionally {headers: [...], rows: [[...], ...]}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('book_pages')->nullOnDelete();
            $table->foreignId('element_id')->nullable()->constrained('book_elements')->nullOnDelete();
            $table->string('title')->nullable();
            $table->json('content')->nullable();
            $table->text('markdown')->nullable();
            $table->text('html')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'chapter_id']);
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_tables');
    }
};
