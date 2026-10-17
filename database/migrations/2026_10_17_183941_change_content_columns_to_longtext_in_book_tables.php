<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_chapters MODIFY content LONGTEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_pages MODIFY content LONGTEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_elements MODIFY content LONGTEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_chunks MODIFY content LONGTEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE my_book_pages MODIFY content LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_chapters MODIFY content TEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_pages MODIFY content TEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_elements MODIFY content TEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE book_chunks MODIFY content TEXT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE my_book_pages MODIFY content TEXT NULL');
    }
};
