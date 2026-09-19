<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Source traceability for OpenDataLoader-produced parse data (additive,
 * backward compatible — smalot books simply have nulls and keep working).
 *
 *   book_elements.source_id  → the source element id assigned by the
 *   OpenDataLoader worker, kept so AI answers / admin debugging can point
 *   back at the exact extracted element ("the element with ODL id 3-h-2").
 *
 *   book_chunks.element_ids  → book_elements.id list of every element that
 *   fed this chunk (headings included), so each RAG chunk is traceable to
 *   the exact source elements in reading order — required for AI citation
 *   ("Chapter 3 → Page 42 → element 101/102").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_elements', function (Blueprint $table) {
            $table->string('source_id', 100)->nullable()->after('id');
        });

        Schema::table('book_elements', function (Blueprint $table) {
            // Element lookups are always scoped to a book first.
            $table->index(['book_id', 'source_id']);
        });

        Schema::table('book_chunks', function (Blueprint $table) {
            $table->json('element_ids')->nullable()->after('metadata');
        });
    }

    public function down(): void
    {
        Schema::table('book_elements', function (Blueprint $table) {
            $table->dropIndex(['book_id', 'source_id']);
            $table->dropColumn('source_id');
        });

        Schema::table('book_chunks', function (Blueprint $table) {
            $table->dropColumn('element_ids');
        });
    }
};
