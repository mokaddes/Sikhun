<?php

namespace App\Services\Ai\Providers;

use App\Models\AiProvider;

class OpenAiCompatibleProvider extends AbstractOpenAiCompatibleProvider
{
    public function __construct(AiProvider $provider)
    {
        parent::__construct($provider);
    }

    protected function baseUrl(): string
    {
        $endpoint = trim((string) ($this->provider->api_endpoint ?: ''));

        if ($endpoint === '') {
            return 'https://api.openai.com/v1';
        }

        // If the admin pasted the full .../chat/completions URL, strip it for baseUrl()
        if (str_ends_with($endpoint, '/chat/completions')) {
            return substr($endpoint, 0, -17);
        }

        return rtrim($endpoint, '/');
    }

    protected function chatEndpoint(): string
    {
        $endpoint = trim((string) ($this->provider->api_endpoint ?: ''));

        if ($endpoint !== '' && str_ends_with($endpoint, '/chat/completions')) {
            return $endpoint;
        }

        return $this->baseUrl().'/chat/completions';
    }

    protected function headers(): array
    {
        $headers = [];

        // Apply custom headers first
        foreach ((array) $this->provider->custom_headers as $header) {
            $name = trim((string) ($header['name'] ?? ''));
            if ($name !== '') {
                $headers[$name] = (string) ($header['value'] ?? '');
            }
        }

        // Apply Bearer api_key if no explicit Authorization header was specified
        if (! isset($headers['Authorization']) && ! empty($this->provider->api_key)) {
            $headers['Authorization'] = 'Bearer '.$this->provider->api_key;
        }

        return $headers;
    }
}
