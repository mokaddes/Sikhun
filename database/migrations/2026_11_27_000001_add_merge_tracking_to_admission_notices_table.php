<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_notices', function (Blueprint $table) {
            if (! Schema::hasColumn('admission_notices', 'update_history')) {
                $table->json('update_history')->nullable()->after('source_url');
            }
            if (! Schema::hasColumn('admission_notices', 'merged_slugs')) {
                $table->json('merged_slugs')->nullable()->after('update_history');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admission_notices', function (Blueprint $table) {
            $cols = array_values(array_filter(
                ['update_history', 'merged_slugs'],
                fn (string $col) => Schema::hasColumn('admission_notices', $col)
            ));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
