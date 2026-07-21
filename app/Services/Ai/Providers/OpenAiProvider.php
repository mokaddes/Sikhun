<?php

namespace App\Services\Ai\Providers;

class OpenAiProvider extends AbstractOpenAiCompatibleProvider
{
    protected function baseUrl(): string
    {
        return 'https://api.openai.com/v1';
    }
}
