<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_sessions', function (Blueprint $table) {
            $table->foreignId('source_my_book_id')
                ->nullable()
                ->after('source_book_id')
                ->references('id')
                ->on('my_books')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ai_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_my_book_id');
        });
    }
};