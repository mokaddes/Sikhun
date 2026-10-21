<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change type to varchar(50) so it supports the 4 standardized provider types cleanly
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE ai_providers MODIFY type VARCHAR(50) NOT NULL");
        } else {
            Schema::table('ai_providers', function (Blueprint $table) {
                $table->string('type', 50)->change();
            });
        }

        // Migrate any old type keys to the 4 canonical types:
        // openai_compatible, anthropic_compatible, google_gemini, custom
        DB::table('ai_providers')
            ->whereIn('type', ['openai', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface'])
            ->update(['type' => 'openai_compatible']);

        DB::table('ai_providers')
            ->where('type', 'claude')
            ->update(['type' => 'anthropic_compatible']);

        DB::table('ai_providers')
            ->where('type', 'gemini')
            ->update(['type' => 'google_gemini']);
    }

    public function down(): void
    {
        // Rollback legacy mappings if ever needed
        DB::table('ai_providers')
            ->where('type', 'openai_compatible')
            ->update(['type' => 'openai']);

        DB::table('ai_providers')
            ->where('type', 'anthropic_compatible')
            ->update(['type' => 'claude']);

        DB::table('ai_providers')
            ->where('type', 'google_gemini')
            ->update(['type' => 'gemini']);
    }
};
