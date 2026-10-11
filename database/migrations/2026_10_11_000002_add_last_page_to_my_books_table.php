<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Last page the student had open in one of their own uploaded PDFs, so the
 * reader resumes where they left off.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('my_books', function (Blueprint $table) {
            $table->unsignedInteger('last_page')->nullable()->after('total_pages');
        });
    }

    public function down(): void
    {
        Schema::table('my_books', function (Blueprint $table) {
            $table->dropColumn('last_page');
        });
    }
};
