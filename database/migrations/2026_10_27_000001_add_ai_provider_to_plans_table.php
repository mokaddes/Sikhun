<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('ai_provider_id')->nullable()->after('trial_ai_minutes')->constrained('ai_providers')->nullOnDelete();
            $table->string('ai_model', 100)->nullable()->after('ai_provider_id');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['ai_provider_id']);
            $table->dropColumn(['ai_provider_id', 'ai_model']);
        });
    }
};
