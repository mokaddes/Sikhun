<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PDF-processing lifecycle tracking on books. The admin uploads a PDF, a
 * queued job parses it into chapters/pages/elements/tables/images/formulas,
 * and these columns record where in that lifecycle each book is — so the
 * admin UI can show status and offer a retry, and so we never reprocess an
 * unchanged PDF twice (pdf_content_hash).
 *
 * Additive only — every column is nullable/defaulted so existing rows stay
 * valid without a backfill step.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('processing_status', 20)->default('pending')->after('total_pages'); // pending|processing|completed|failed
            $table->text('processing_error')->nullable()->after('processing_status');
            $table->timestamp('processing_started_at')->nullable()->after('processing_error');
            $table->timestamp('processing_completed_at')->nullable()->after('processing_started_at');
            $table->string('parser_name', 50)->nullable()->after('processing_completed_at');
            $table->string('parser_version', 50)->nullable()->after('parser_name');
            $table->timestamp('parsed_at')->nullable()->after('parser_version');
            $table->string('pdf_content_hash', 64)->nullable()->after('parsed_at');
            $table->boolean('chapter_purchase_enabled')->default(false)->after('pdf_content_hash');

            $table->index('processing_status');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['processing_status']);
            $table->dropColumn([
                'processing_status', 'processing_error', 'processing_started_at', 'processing_completed_at',
                'parser_name', 'parser_version', 'parsed_at', 'pdf_content_hash', 'chapter_purchase_enabled',
            ]);
        });
    }
};
