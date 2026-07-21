<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the old one-use-case-per-row design (which forced an admin to
 * create duplicate AiProvider rows with the same API key just to cover
 * multiple use cases) with a proper many-to-many: one set of credentials
 * can now be assigned to any number of use cases, each independently
 * markable as the default for that use case.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_provider_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('use_case', 50);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['ai_provider_id', 'use_case']);
            $table->index('use_case');
        });

        // Migrate existing data: every current ai_providers row has exactly
        // one use_case + is_default value — carry those into the new table
        // before the old columns are dropped, so nothing already configured
        // is lost.
        $existing = DB::table('ai_providers')->select('id', 'use_case', 'is_default')->get();

        foreach ($existing as $row) {
            if (! $row->use_case) {
                continue;
            }

            DB::table('ai_provider_use_cases')->insert([
                'ai_provider_id' => $row->id,
                'use_case' => $row->use_case,
                'is_default' => (bool) $row->is_default,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropColumn(['use_case', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->string('use_case', 50)->nullable()->after('api_endpoint');
            $table->boolean('is_default')->default(false)->after('use_case');
        });

        $rows = DB::table('ai_provider_use_cases')->get();
        foreach ($rows as $row) {
            DB::table('ai_providers')->where('id', $row->ai_provider_id)->update([
                'use_case' => $row->use_case,
                'is_default' => $row->is_default,
            ]);
        }

        Schema::dropIfExists('ai_provider_use_cases');
    }
};
