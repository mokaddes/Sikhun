<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['admission_notices', 'job_circulars', 'blog_posts', 'courses'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'fb_shared_count')) {
                        $table->unsignedInteger('fb_shared_count')->default(0);
                    }
                    if (!Schema::hasColumn($tableName, 'fb_last_shared_at')) {
                        $table->timestamp('fb_last_shared_at')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'fb_post_id')) {
                        $table->string('fb_post_id')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['admission_notices', 'job_circulars', 'blog_posts', 'courses'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $cols = array_filter(['fb_shared_count', 'fb_last_shared_at', 'fb_post_id'], fn($c) => Schema::hasColumn($tableName, $c));
                    if (!empty($cols)) {
                        $table->dropColumn($cols);
                    }
                });
            }
        }
    }
};
