<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrades book_chunks from "flat text bag" to structure-aware RAG units:
 * each chunk now traces to its chapter and page (so access filtering and
 * citations work), carries a metadata bag (heading path, related
 * tables/images/formulas), and optionally stores an embedding vector as
 * JSON for hybrid retrieval (MySQL FULLTEXT candidates re-ranked by
 * cosine similarity — see BookChunkRetrievalService).
 *
 * Existing rows keep working untouched: new columns are all nullable.
 * They get chapter_id/page_id backfilled only when their book is
 * reprocessed by the new pipeline.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_chunks', function (Blueprint $table) {
            $table->foreignId('chapter_id')->nullable()->after('book_id')->constrained('book_chapters')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->after('chapter_id')->constrained('book_pages')->nullOnDelete();
            $table->json('metadata')->nullable()->after('content');
            $table->json('embedding')->nullable()->after('metadata');
        });

        // Composite access-scoping index: the RAG query is always
        // "chunks of book X (optionally restricted to these chapter ids)".
        // (Blueprint index rather than raw SQL so sqlite tests work too.)
        Schema::table('book_chunks', function (Blueprint $table) {
            $table->index(['book_id', 'chapter_id'], 'book_chunks_book_chapter_idx');
        });
    }

    public function down(): void
    {
        Schema::table('book_chunks', function (Blueprint $table) {
            $table->dropIndex('book_chunks_book_chapter_idx');
        });

        Schema::table('book_chunks', function (Blueprint $table) {
            $table->dropColumn(['chapter_id', 'page_id', 'metadata', 'embedding']);
        });
    }
};
