<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remembers the last page a student had open (per book, per session-day).
 * `pages_read` tracks the *furthest* page reached; `last_page` tracks where
 * they actually stopped, so the reader can resume there next time.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->unsignedInteger('last_page')->nullable()->after('pages_read');
        });
    }

    public function down(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->dropColumn('last_page');
        });
    }
};
