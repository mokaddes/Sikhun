<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use App\Models\BookChunk;
use Illuminate\Support\Facades\Http;

/**
 * Generates embeddings for book chunks through any OpenAI-compatible
 * /embeddings endpoint (OpenAI, Ollama, vLLM, etc.). The provider/model is
 * resolved from the same admin-managed AiProvider rows the chat features
 * use — no separate credentials or config surface.
 *
 * Vectors are stored as JSON in book_chunks.embedding; hybrid retrieval
 * (FULLTEXT candidates + cosine re-rank in PHP) means we never need a
 * vector database for the corpus sizes Sikhun deals with (hundreds of
 * pages per book, not millions).
 */
class EmbeddingService
{
    /**
     * @param string[] $texts
     * @return array<int, float[]> vectors, aligned with $texts
     * @throws \RuntimeException when no embedding provider is configured
     */
    public function embed(array $texts): array
    {
        if (! $texts) {
            return [];
        }

        $provider = $this->resolveEmbeddingProvider();

        $response = Http::withToken($provider->api_key)
            ->timeout(60)
            ->post($this->embeddingsUrl($provider), [
                'model' => $provider->model_name,
                'input' => array_values($texts),
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Embedding request failed: '.$response->body());
        }

        // OpenAI returns data ordered by index; sort defensively.
        $data = collect($response->json('data', []))
            ->sortBy('index')
            ->pluck('embedding')
            ->all();

        if (count($data) !== count($texts)) {
            throw new \RuntimeException('Embedding provider returned '.count($data).' vectors for '.count($texts).' inputs.');
        }

        return $data;
    }

    public function embedQuery(string $text): array
    {
        return $this->embed([$text])[0];
    }

    /**
     * Fill embeddings for all of a book's chunks that lack one, in batches.
     *
     * @return int number of chunks embedded
     */
    public function embedBookChunks(int $bookId, int $batchSize = 64): int
    {
        $remaining = BookChunk::where('book_id', $bookId)
            ->whereNull('embedding')
            ->whereNotNull('content')
            ->orderBy('chunk_index');

        $total = 0;

        $remaining->chunkById($batchSize, function ($batch) use (&$total) {
            $vectors = $this->embed($batch->pluck('content')->all());

            foreach ($batch as $i => $chunk) {
                $chunk->update(['embedding' => $vectors[$i]]);
            }

            $total += count($batch);
        });

        return $total;
    }

    /**
     * Cosine similarity between two equal-length vectors.
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Reuses the admin-managed provider of the given use case when it is
     * OpenAI-compatible; otherwise looks for any active OpenAI-compatible
     * provider. Embeddings work on any of them by pointing at /embeddings.
     */
    private function resolveEmbeddingProvider(): AiProvider
    {
        // Prefer an explicit provider marked for embeddings via model
        // naming convention the admin sets up; fall back to the book_chat
        // default's provider if it is OpenAI-compatible.
        $useCase = \App\Models\AiProviderUseCase::where('use_case', 'book_chat')
            ->where('is_default', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->with('provider')
            ->first();

        if ($useCase && in_array($useCase->provider->type, ['openai', 'groq', 'deepseek', 'vllm', 'ollama', 'custom'], true)) {
            return $useCase->provider;
        }

        $fallback = AiProvider::query()
            ->where('is_active', true)
            ->whereIn('type', ['openai', 'deepseek', 'vllm', 'ollama', 'custom'])
            ->first();

        if (! $fallback) {
            throw new \RuntimeException('No active OpenAI-compatible AI provider for embeddings. Configure one in /admin/ai-providers.');
        }

        return $fallback;
    }

    private function embeddingsUrl(AiProvider $provider): string
    {
        // Custom providers paste a full base URL; strip a trailing path so
        // /embeddings lands correctly either way.
        $base = rtrim((string) ($provider->api_endpoint ?: 'https://api.openai.com/v1'), '/');

        return $base.'/embeddings';
    }
}
