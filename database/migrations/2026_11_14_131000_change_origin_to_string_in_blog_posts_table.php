<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'origin')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE blog_posts MODIFY origin VARCHAR(50) NOT NULL DEFAULT 'manual'");
            } else {
                Schema::table('blog_posts', function (Blueprint $table) {
                    $table->string('origin', 50)->default('manual')->change();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'origin')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE blog_posts MODIFY origin ENUM('manual', 'auto') NOT NULL DEFAULT 'manual'");
            }
        }
    }
};
