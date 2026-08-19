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
use Inertia\Inertia;
use Inertia\Response;

class AiProviderController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AiProviders/Index', [
            'providers' => AiProvider::with('useCases')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/AiProviders/Form', ['provider' => null]);
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
     * save — simpler and safer than diffing, and the list is short (max
     * 7 rows) so there's no performance concern. Marking a use case
     * default here automatically un-defaults any OTHER provider that was
     * previously default for that same use case (only one default per
     * use case, platform-wide).
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
