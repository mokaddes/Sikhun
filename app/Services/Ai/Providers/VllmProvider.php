<?php

namespace App\Services\Ai\Providers;

/**
 * Self-hosted vLLM exposes an OpenAI-compatible server — same wire format,
 * just a different (admin-configured) base URL instead of a public one.
 */
class VllmProvider extends AbstractOpenAiCompatibleProvider
{
    protected function baseUrl(): string
    {
        return rtrim($this->provider->api_endpoint ?: 'http://localhost:8000', '/').'/v1';
    }

    protected function headers(): array
    {
        // Local vLLM servers usually don't require a bearer token.
        return $this->provider->api_key ? ['Authorization' => 'Bearer '.$this->provider->api_key] : [];
    }
}
