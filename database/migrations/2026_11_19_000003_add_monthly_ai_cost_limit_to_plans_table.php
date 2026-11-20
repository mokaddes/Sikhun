<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'monthly_ai_cost_limit_usd')) {
                $table->decimal('monthly_ai_cost_limit_usd', 10, 4)->nullable()->after('ai_model');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'monthly_ai_cost_limit_usd')) {
                $table->dropColumn('monthly_ai_cost_limit_usd');
            }
        });
    }
};
