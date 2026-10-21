<?php

namespace App\Services\Ai;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use Throwable;

class LoggingAiProviderDecorator implements AiProviderContract
{
    public function __construct(
        protected AiProviderContract $inner,
        protected AiProvider $provider,
        protected string $useCase = 'general',
        protected array $context = []
    ) {}

    public function getInner(): AiProviderContract
    {
        return $this->inner;
    }

    public function getProvider(): AiProvider
    {
        return $this->provider;
    }

    public function chat(array $messages, array $options = []): string
    {
        $start = microtime(true);
        $mergedOptions = array_merge($this->context, $options);
        $useCase = $options['use_case'] ?? $this->useCase;

        try {
            $response = $this->inner->chat($messages, $options);
            $latency = (int) round((microtime(true) - $start) * 1000);

            AiLoggerService::log(
                $this->provider,
                $useCase,
                $messages,
                $response,
                'success',
                null,
                $latency,
                $mergedOptions
            );

            return $response;
        } catch (Throwable $e) {
            $latency = (int) round((microtime(true) - $start) * 1000);

            AiLoggerService::log(
                $this->provider,
                $useCase,
                $messages,
                null,
                'failed',
                $e->getMessage(),
                $latency,
                $mergedOptions
            );

            throw $e;
        }
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $start = microtime(true);
        $mergedOptions = array_merge($this->context, $options);
        $useCase = $options['use_case'] ?? $this->useCase;
        $fullResponse = '';

        try {
            foreach ($this->inner->stream($messages, $options) as $chunk) {
                $fullResponse .= $chunk;
                yield $chunk;
            }

            $latency = (int) round((microtime(true) - $start) * 1000);

            AiLoggerService::log(
                $this->provider,
                $useCase,
                $messages,
                $fullResponse,
                'success',
                null,
                $latency,
                $mergedOptions
            );
        } catch (Throwable $e) {
            $latency = (int) round((microtime(true) - $start) * 1000);

            AiLoggerService::log(
                $this->provider,
                $useCase,
                $messages,
                $fullResponse !== '' ? $fullResponse : null,
                'failed',
                $e->getMessage(),
                $latency,
                $mergedOptions
            );

            throw $e;
        }
    }

    public function isAvailable(): bool
    {
        return $this->inner->isAvailable();
    }
}
