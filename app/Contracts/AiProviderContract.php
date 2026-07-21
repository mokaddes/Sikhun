<?php

namespace App\Contracts;

interface AiProviderContract
{
    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function chat(array $messages, array $options = []): string;

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @return \Generator<string> yields text chunks as they arrive
     */
    public function stream(array $messages, array $options = []): \Generator;

    public function isAvailable(): bool;
}
