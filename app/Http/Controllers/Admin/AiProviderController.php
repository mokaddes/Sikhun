<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiProviderRequest;
use App\Models\AiProvider;
use App\Models\AiProviderUseCase;
use App\Services\Ai\AiConnectionTester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AiProviderController extends Controller
{
    public const USE_CASE_RECOMMENDATIONS = [
        'book_chat' => [
            'models' => ['Groq — Llama 3.3 70B', 'Google — Gemini 2.0 Flash'],
            'reason' => 'Fast first-token response.',
        ],
        'exam_gen' => [
            'models' => ['OpenAI — GPT-4o-mini', 'DeepSeek — V3'],
            'reason' => 'Reliable structured JSON output.',
        ],
        'flashcard_gen' => [
            'models' => ['Google — Gemini Flash', 'Groq — Llama'],
            'reason' => 'Low cost and fast generation.',
        ],
        'essay_grade' => [
            'models' => ['Anthropic — Claude Sonnet', 'OpenAI — GPT-4o'],
            'reason' => 'Strong rubric-based evaluation.',
        ],
        'schedule_gen' => [
            'models' => ['OpenAI — GPT-4o-mini'],
            'reason' => 'Good structured output at low cost.',
        ],
        'support_bot' => [
            'models' => ['Groq — Llama', 'Google — Gemini Flash'],
            'reason' => 'Low-cost support responses.',
        ],
        'book_ocr' => [
            'models' => ['Google — Gemini Vision', 'OpenAI — GPT-4o Vision'],
            'reason' => 'Multimodal document recognition.',
        ],
        'blog_rewrite' => [
            'models' => ['DeepSeek', 'OpenAI — GPT-4o-mini'],
            'reason' => 'Cost-effective with strong Bengali writing.',
        ],
    ];

    public const PROVIDER_TYPES = [
        'openai_compatible' => [
            'label' => 'OpenAI Compatible',
            'recommended' => true,
            'description' => 'OpenAI, Groq, DeepSeek, Ollama, vLLM, OpenRouter, and other compatible APIs.',
            'endpoint_hint' => 'Base URL or /chat/completions endpoint (e.g. https://api.openai.com/v1, https://api.deepseek.com/v1, https://openrouter.ai/api/v1). Leave blank to default to https://api.openai.com/v1.',
            'default_endpoint' => 'https://api.openai.com/v1',
            'default_model' => 'gpt-4o',
        ],
        'anthropic_compatible' => [
            'label' => 'Anthropic Compatible',
            'recommended' => false,
            'description' => 'Anthropic Claude API and providers implementing the Anthropic Messages API.',
            'endpoint_hint' => 'Anthropic base URL (defaults to https://api.anthropic.com if left blank).',
            'default_endpoint' => 'https://api.anthropic.com',
            'default_model' => 'claude-3-5-sonnet-20241022',
        ],
        'google_gemini' => [
            'label' => 'Google Gemini',
            'recommended' => false,
            'description' => 'Google Gemini API and its native request/response format.',
            'endpoint_hint' => 'Gemini API base URL (defaults to https://generativelanguage.googleapis.com if left blank).',
            'default_endpoint' => 'https://generativelanguage.googleapis.com',
            'default_model' => 'gemini-1.5-flash',
        ],
        'custom' => [
            'label' => 'Custom API',
            'recommended' => false,
            'description' => 'Custom base URL, authentication, headers, and API implementation.',
            'endpoint_hint' => 'Full URL to the chat/completions endpoint (e.g. https://your-server.com/api/v1/chat/completions).',
            'default_endpoint' => '',
            'default_model' => '',
        ],
    ];

    public function index(): Response
    {
        return Inertia::render('Admin/AiProviders/Index', [
            'providers' => AiProvider::with('useCases')->orderBy('name')->get(),
            'providerTypes' => self::PROVIDER_TYPES,
            'recommendations' => self::USE_CASE_RECOMMENDATIONS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/AiProviders/Form', [
            'provider' => null,
            'providerTypes' => self::PROVIDER_TYPES,
        ]);
    }

    public function store(AiProviderRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['use_cases', 'default_use_cases']);
        $data['is_active'] = $request->boolean('is_active');
        $data['custom_headers'] = $this->cleanHeaders($request->custom_headers ?? []);

        DB::transaction(function () use ($data, $request) {
            $provider = AiProvider::create($data);
            $this->syncUseCases($provider, $request->use_cases, $request->default_use_cases ?? []);
        });

        return redirect()->route('admin.ai-providers.index')->with('success', 'AI provider created.');
    }

    public function edit(AiProvider $aiProvider): Response
    {
        return Inertia::render('Admin/AiProviders/Form', [
            'provider' => $aiProvider->load('useCases'),
            'providerTypes' => self::PROVIDER_TYPES,
        ]);
    }

    public function update(AiProviderRequest $request, AiProvider $aiProvider): RedirectResponse
    {
        $data = $request->safe()->except(['use_cases', 'default_use_cases']);
        $data['is_active'] = $request->boolean('is_active');
        $data['custom_headers'] = $this->cleanHeaders($request->custom_headers ?? []);

        // Don't overwrite an existing key with a blank field (form left empty on purpose)
        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        DB::transaction(function () use ($data, $request, $aiProvider) {
            $aiProvider->update($data);
            $this->syncUseCases($aiProvider, $request->use_cases, $request->default_use_cases ?? []);
        });

        return redirect()->route('admin.ai-providers.index')->with('success', 'AI provider updated.');
    }

    public function destroy(AiProvider $aiProvider): RedirectResponse
    {
        $aiProvider->delete(); // use_cases cascade via FK

        return back()->with('success', 'AI provider deleted.');
    }

    public function test(AiProvider $aiProvider, AiConnectionTester $tester): JsonResponse
    {
        Log::info('Testing AI provider connection:', [
            'id' => $aiProvider->id,
            'name' => $aiProvider->name,
            'type' => $aiProvider->type,
            'url' => $aiProvider->api_endpoint,
            'headers' => $aiProvider->custom_headers,
            'model' => $aiProvider->model_name,
        ]);

        return response()->json($tester->test($aiProvider));
    }

    /**
     * Drops empty header rows from the form (admin-added-but-not-filled)
     * and re-indexes so the stored JSON stays a clean list of {name, value}.
     */
    private function cleanHeaders(array $headers): array
    {
        return array_values(array_filter($headers, fn ($h) => trim((string) ($h['name'] ?? '')) !== ''));
    }

    /**
     * Replaces this provider's use-case assignments wholesale on every
     * save — simpler and safer than diffing.
     */
    private function syncUseCases(AiProvider $provider, array $useCases, array $defaultUseCases): void
    {
        $provider->useCases()->delete();

        foreach ($useCases as $useCase) {
            $isDefault = in_array($useCase, $defaultUseCases, true);

            if ($isDefault) {
                // Only one default per use case across the whole platform.
                AiProviderUseCase::where('use_case', $useCase)->update(['is_default' => false]);
            }

            $provider->useCases()->create(['use_case' => $useCase, 'is_default' => $isDefault]);
        }
    }
}
