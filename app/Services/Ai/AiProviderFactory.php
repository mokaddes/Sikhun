<?php

namespace App\Services\Ai;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use App\Models\Student;
use App\Services\Ai\Providers\AnthropicCompatibleProvider;
use App\Services\Ai\Providers\CustomProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\OpenAiCompatibleProvider;

/**
 * The ONLY way any code in this app should ever talk to an AI model.
 * Never call OpenAI/Anthropic/etc SDKs directly from a job or controller —
 * always go through here, so swapping providers is an admin-panel change,
 * not a code change.
 */
class AiProviderFactory
{
    public static function make(int $providerId, ?string $useCase = null, array $context = []): AiProviderContract
    {
        $provider = AiProvider::findOrFail($providerId);
        return new LoggingAiProviderDecorator(
            self::instantiate($provider),
            $provider,
            $useCase ?: 'direct_call',
            $context
        );
    }

    /**
     * Resolve the AI provider for a use case, taking into account the student's subscription plan.
     *
     * @param string $useCase configured AI feature, including book_chat, exam_gen,
     *                        flashcard_gen, essay_grade, schedule_gen, notification_gen,
     *                        support_bot, book_ocr, book_retrieval, book_structure,
     *                        blog_translate, blog_rewrite
     * @param array $context optional context array containing 'student' or 'student_id'
     */
    public static function default(string $useCase, array $context = []): AiProviderContract
    {
        // 1. Resolve student from context or active web auth session
        $student = $context['student'] ?? null;
        if (! $student && isset($context['student_id'])) {
            $student = Student::find($context['student_id']);
        }
        if (! $student && auth('web')->check() && auth('web')->user() instanceof Student) {
            $student = auth('web')->user();
        }

        // 2. Plan-wise AI Provider & Model Resolution:
        // If this is a student-facing feature and the student has an active subscription with an assigned plan-level AI provider:
        $studentFacingUseCases = ['book_chat', 'exam_gen', 'flashcard_gen', 'essay_grade', 'schedule_gen'];
        if ($student instanceof Student && in_array($useCase, $studentFacingUseCases, true)) {
            $plan = $student->activeSubscription?->plan;
            if ($plan && $plan->ai_provider_id) {
                $planProvider = $plan->aiProvider;
                if ($planProvider && $planProvider->is_active) {
                    $effectiveProvider = clone $planProvider;
                    if (! empty($plan->ai_model)) {
                        $effectiveProvider->model_name = trim($plan->ai_model);
                    }

                    return new LoggingAiProviderDecorator(
                        self::instantiate($effectiveProvider),
                        $effectiveProvider,
                        $useCase,
                        array_merge($context, [
                            'student_id' => $student->id,
                            'student_name' => $student->name,
                            'plan_id' => $plan->id,
                            'plan_name' => $plan->name,
                        ])
                    );
                }
            }
        }

        // 3. System Default Provider for this specific use case
        $mapping = \App\Models\AiProviderUseCase::where('use_case', $useCase)
            ->where('is_default', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->with('provider')
            ->first();

        if ($mapping && $mapping->provider) {
            return new LoggingAiProviderDecorator(
                self::instantiate($mapping->provider),
                $mapping->provider,
                $useCase,
                $context
            );
        }

        // Fallback to book_chat or any active provider so unmapped secondary use-cases
        // (like book_structure) don't crash background jobs.
        $fallbackMapping = \App\Models\AiProviderUseCase::whereIn('use_case', ['book_chat', 'book_ocr'])
            ->where('is_default', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->with('provider')
            ->first();

        if ($fallbackMapping && $fallbackMapping->provider) {
            return new LoggingAiProviderDecorator(
                self::instantiate($fallbackMapping->provider),
                $fallbackMapping->provider,
                $useCase,
                $context
            );
        }

        $activeProvider = AiProvider::where('is_active', true)->orderBy('id')->first();
        if ($activeProvider) {
            return new LoggingAiProviderDecorator(
                self::instantiate($activeProvider),
                $activeProvider,
                $useCase,
                $context
            );
        }

        throw new \RuntimeException("No active default AI provider configured for '{$useCase}'. Set one up in /admin/ai-providers.");
    }

    public static function instantiate(AiProvider $provider): AiProviderContract
    {
        return match ($provider->type) {
            'openai_compatible', 'openai', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface' => new OpenAiCompatibleProvider($provider),
            'anthropic_compatible', 'claude' => new AnthropicCompatibleProvider($provider),
            'google_gemini', 'gemini' => new GeminiProvider($provider),
            'custom' => new CustomProvider($provider),
            default => new OpenAiCompatibleProvider($provider),
        };
    }
}
