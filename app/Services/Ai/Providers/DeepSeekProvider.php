<?php

namespace App\Services\Ai\Providers;

class DeepSeekProvider extends AbstractOpenAiCompatibleProvider
{
    protected function baseUrl(): string
    {
        return 'https://api.deepseek.com/v1';
    }
}
