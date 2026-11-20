<?php

namespace App\Services;

use App\Models\Book;
use App\Models\QuestionBank;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Scalable search abstraction: uses Meilisearch when MEILISEARCH_HOST is
 * configured, with cached MySQL/SQLite query fallback.
 */
class SearchService
{
    public function searchBooks(string $query, int $limit = 15): Collection
    {
        $q = trim($query);
        if ($q === '') {
            return collect();
        }

        $host = trim((string) env('MEILISEARCH_HOST', ''));
        if ($host !== '') {
            try {
                $resp = Http::withToken((string) env('MEILISEARCH_KEY', ''))
                    ->timeout(3)
                    ->post(rtrim($host, '/').'/indexes/books/search', ['q' => $q, 'limit' => $limit]);

                if ($resp->successful()) {
                    $ids = collect($resp->json('hits', []))->pluck('id')->filter()->all();
                    if (! empty($ids)) {
                        return Book::published()->whereIn('id', $ids)->get();
                    }
                }
            } catch (\Throwable) {
                // Fall back to DB search
            }
        }

        return Cache::remember('search_books:'.md5($q).':'.$limit, 120, function () use ($q, $limit) {
            return Book::published()
                ->where(fn ($builder) => $builder
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%"))
                ->limit($limit)
                ->get();
        });
    }

    public function searchQuestions(string $query, ?string $segment = null, int $limit = 20): Collection
    {
        $q = trim($query);
        if ($q === '') {
            return collect();
        }

        return QuestionBank::published()
            ->when($segment, fn ($builder) => $builder->where('segment', $segment))
            ->where(fn ($builder) => $builder
                ->where('question_text', 'like', "%{$q}%")
                ->orWhere('origin_tag', 'like', "%{$q}%"))
            ->limit($limit)
            ->get();
    }
}
