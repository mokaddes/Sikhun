<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_providers', 'cost_per_1m_input')) {
                $table->decimal('cost_per_1m_input', 10, 4)->default(0)->after('temperature');
            }
            if (! Schema::hasColumn('ai_providers', 'cost_per_1m_output')) {
                $table->decimal('cost_per_1m_output', 10, 4)->default(0)->after('cost_per_1m_input');
            }
        });

        Schema::table('ai_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_logs', 'cost')) {
                $table->decimal('cost', 12, 6)->default(0)->after('total_tokens');
            }
        });

        // Set realistic market costs per 1M tokens for existing providers
        $providers = DB::table('ai_providers')->get();
        foreach ($providers as $provider) {
            $name = strtolower($provider->name . ' ' . $provider->model_name . ' ' . $provider->type);
            $inputCost = 0.15; // default fallback per 1M
            $outputCost = 0.60;

            if (str_contains($name, 'deepseek')) {
                $inputCost = 0.15;
                $outputCost = 0.30;
            } elseif (str_contains($name, 'gemini')) {
                $inputCost = 0.08;
                $outputCost = 0.30;
            } elseif (str_contains($name, 'claude') || str_contains($name, 'anthropic')) {
                $inputCost = 3.00;
                $outputCost = 15.00;
            } elseif (str_contains($name, 'gpt-4o-mini') || str_contains($name, 'agent router') || str_contains($name, 'openai')) {
                $inputCost = 0.15;
                $outputCost = 0.60;
            } elseif (str_contains($name, 'groq') || str_contains($name, 'llama')) {
                $inputCost = 0.59;
                $outputCost = 0.79;
            } elseif (str_contains($name, 'bge') || str_contains($name, 'retrieval') || str_contains($name, 'embedding')) {
                $inputCost = 0.02;
                $outputCost = 0.02;
            } elseif (str_contains($name, 'blue minds') || str_contains($name, 'nvidia')) {
                $inputCost = 0.20;
                $outputCost = 0.50;
            }

            DB::table('ai_providers')->where('id', $provider->id)->update([
                'cost_per_1m_input' => $inputCost,
                'cost_per_1m_output' => $outputCost,
            ]);
        }

        // Backfill historical logs cost where cost is 0
        $logs = DB::table('ai_logs')->where('cost', '<=', 0)->where('status', 'success')->get();
        foreach ($logs as $log) {
            $inputTokens = (int) ($log->input_tokens ?? 0);
            $outputTokens = (int) ($log->output_tokens ?? 0);
            if ($inputTokens <= 0 && $outputTokens <= 0) {
                continue;
            }

            // Default rates: ~$0.15/1M input, ~$0.30/1M output
            $inputRate = 0.15;
            $outputRate = 0.30;

            if ($log->provider_id) {
                $p = DB::table('ai_providers')->where('id', $log->provider_id)->first();
                if ($p && ($p->cost_per_1m_input > 0 || $p->cost_per_1m_output > 0)) {
                    $inputRate = (float) $p->cost_per_1m_input;
                    $outputRate = (float) $p->cost_per_1m_output;
                }
            }

            $cost = (($inputTokens / 1000000) * $inputRate) + (($outputTokens / 1000000) * $outputRate);
            DB::table('ai_logs')->where('id', $log->id)->update([
                'cost' => round($cost, 6),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropColumn(['cost_per_1m_input', 'cost_per_1m_output']);
        });

        Schema::table('ai_logs', function (Blueprint $table) {
            $table->dropColumn(['cost']);
        });
    }
};
