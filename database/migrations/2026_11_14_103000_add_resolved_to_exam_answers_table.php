<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_answers', 'resolved')) {
                $table->boolean('resolved')->default(false)->after('is_correct');
            }
            if (! Schema::hasColumn('exam_answers', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resolved');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            if (Schema::hasColumn('exam_answers', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
            if (Schema::hasColumn('exam_answers', 'resolved')) {
                $table->dropColumn('resolved');
            }
        });
    }
};
