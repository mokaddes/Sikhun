<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Text chunks extracted from a book's PDF, used to ground AI Chat answers
 * in the book's actual content (retrieval-augmented generation).
 *
 * NOTE — this is keyword-search RAG, not vector-similarity RAG: a real
 * embeddings pipeline needs pgvector (Postgres) or an external vector DB
 * (Qdrant/Pinecone), neither of which fit this MySQL-based stack without
 * adding new infrastructure. A MySQL FULLTEXT index gets ~80% of the
 * retrieval quality with zero extra moving parts, which is the right
 * trade-off until/unless retrieval accuracy becomes the bottleneck —
 * at which point swap BookChunkRetrievalService's query for a real
 * vector search without touching any calling code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->unsignedInteger('page_number')->nullable();
            $table->text('content');
            $table->timestamps();

            $table->index('book_id');
        });

        DB::statement('ALTER TABLE book_chunks ADD FULLTEXT fulltext_content (content)');
    }

    public function down(): void
    {
        Schema::dropIfExists('book_chunks');
    }
};
