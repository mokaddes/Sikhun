<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add a `custom` provider type: any OpenAI-compatible endpoint with a
        // full URL and admin-defined request headers (custom API key names, etc).
        DB::statement("ALTER TABLE ai_providers MODIFY type ENUM('openai','gemini','claude','groq','deepseek','ollama','vllm','huggingface','custom') NOT NULL");

        Schema::table('ai_providers', function (Blueprint $table) {
            $table->json('custom_headers')->nullable()->after('api_endpoint');
        });
    }

    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropColumn('custom_headers');
        });

        DB::statement("ALTER TABLE ai_providers MODIFY type ENUM('openai','gemini','claude','groq','deepseek','ollama','vllm','huggingface') NOT NULL");
    }
};
