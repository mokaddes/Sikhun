<?php

namespace App\Services\Ai\Providers;

class GroqProvider extends AbstractOpenAiCompatibleProvider
{
    protected function baseUrl(): string
    {
        return 'https://api.groq.com/openai/v1';
    }
}
