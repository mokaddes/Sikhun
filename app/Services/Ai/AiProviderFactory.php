<?php

namespace App\Services\Ai;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use App\Services\Ai\Providers\ClaudeProvider;
use App\Services\Ai\Providers\CustomProvider;
use App\Services\Ai\Providers\DeepSeekProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\GroqProvider;
use App\Services\Ai\Providers\HuggingFaceProvider;
use App\Services\Ai\Providers\OllamaProvider;
use App\Services\Ai\Providers\OpenAiProvider;
use App\Services\Ai\Providers\VllmProvider;

/**
 * The ONLY way any code in this app should ever talk to an AI model.
 * Never call OpenAI/Anthropic/etc SDKs directly from a job or controller —
 * always go through here, so swapping providers is an admin-panel change,
 * not a code change.
 */
class AiProviderFactory
{
    public static function make(int $providerId): AiProviderContract
    {
        return self::instantiate(AiProvider::findOrFail($providerId));
    }

    /**
     * @param string $useCase configured AI feature, including book_chat, book_ocr,
     *                        book_retrieval, and book_structure
     */
    public static function default(string $useCase): AiProviderContract
    {
        $mapping = \App\Models\AiProviderUseCase::where('use_case', $useCase)
            ->where('is_default', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->with('provider')
            ->first();

        if ($mapping && $mapping->provider) {
            return self::instantiate($mapping->provider);
        }

        // Fallback to book_chat or any active provider so unmapped secondary use-cases
        // (like book_structure) don't crash background jobs.
        $fallbackMapping = \App\Models\AiProviderUseCase::whereIn('use_case', ['book_chat', 'book_ocr'])
            ->where('is_default', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->with('provider')
            ->first();

        if ($fallbackMapping && $fallbackMapping->provider) {
            return self::instantiate($fallbackMapping->provider);
        }

        $activeProvider = AiProvider::where('is_active', true)->orderBy('id')->first();
        if ($activeProvider) {
            return self::instantiate($activeProvider);
        }

        throw new \RuntimeException("No active default AI provider configured for '{$useCase}'. Set one up in /admin/ai-providers.");
    }

    private static function instantiate(AiProvider $provider): AiProviderContract
    {
        return match ($provider->type) {
            'openai' => new OpenAiProvider($provider),
            'gemini' => new GeminiProvider($provider),
            'claude' => new ClaudeProvider($provider),
            'groq' => new GroqProvider($provider),
            'deepseek' => new DeepSeekProvider($provider),
            'ollama' => new OllamaProvider($provider),
            'vllm' => new VllmProvider($provider),
            'huggingface' => new HuggingFaceProvider($provider),
            'custom' => new CustomProvider($provider),
            default => throw new \InvalidArgumentException("Unknown AI provider type: {$provider->type}"),
        };
    }
}
