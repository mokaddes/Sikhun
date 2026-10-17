<?php

namespace App\Services\Blog;

use App\Models\BlogBlocklist;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogDeduplicator
{
    private const SIMILARITY_THRESHOLD = 85.0;

    /**
     * Normalize a URL by stripping tracking query params and fragments so
     * the same article reached via two campaign links hashes identically.
     */
    public function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        $parts = parse_url($url);
        if (! $parts || empty($parts['host'])) {
            return Str::lower($url);
        }

        $scheme = isset($parts['scheme']) ? Str::lower($parts['scheme']) : 'https';
        $host = Str::lower($parts['host']);
        $host = preg_replace('/^www\./', '', $host);
        $port = isset($parts['port']) && ! in_array($parts['port'], [80, 443], true) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $path = '/' . ltrim(rtrim($path, '/'), '/');

        $query = '';
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $params);
            $stripPrefixes = ['utm_', 'fbclid', 'gclid', 'mc_', 'ref', 'source'];
            $filtered = [];
            foreach ($params as $key => $val) {
                $lowerKey = Str::lower((string) $key);
                $skip = false;
                foreach ($stripPrefixes as $prefix) {
                    if ($lowerKey === $prefix || str_starts_with($lowerKey, $prefix)) {
                        $skip = true;
                        break;
                    }
                }
                if (! $skip) {
                    $filtered[$key] = $val;
                }
            }
            ksort($filtered);
            if (! empty($filtered)) {
                $query = '?' . http_build_query($filtered);
            }
        }

        return "{$scheme}://{$host}{$port}{$path}{$query}";
    }

    public function hashUrl(?string $url): ?string
    {
        if (! $url || trim($url) === '') {
            return null;
        }

        $normalized = $this->normalizeUrl($url);

        return $normalized !== '' ? sha1($normalized) : null;
    }

    public function hashContent(?string $content): ?string
    {
        if (! $content) {
            return null;
        }

        $plain = strip_tags($content);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = mb_strtolower($plain, 'UTF-8');
        $plain = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $plain) ?? $plain;
        $plain = trim(preg_replace('/\s+/u', ' ', $plain) ?? $plain);

        if (mb_strlen($plain, 'UTF-8') < 40) {
            return null;
        }

        return sha1($plain);
    }

    public function fingerprintTitle(?string $title): ?string
    {
        if (! $title) {
            return null;
        }

        $clean = strip_tags($title);
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = mb_strtolower($clean, 'UTF-8');
        $clean = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $clean) ?? $clean;
        $clean = trim(preg_replace('/\s+/u', ' ', $clean) ?? $clean);

        return $clean !== '' ? mb_substr($clean, 0, 250, 'UTF-8') : null;
    }

    /**
     * Check if a candidate article matches any entry in blog_blocklist
     * by URL hash, content hash, or >= 85% title similarity.
     */
    public function isBlocked(?string $url, ?string $title = null, ?string $content = null): bool
    {
        $urlHash = $this->hashUrl($url);
        if ($urlHash && BlogBlocklist::where('source_url_hash', $urlHash)->exists()) {
            return true;
        }

        $contentHash = $this->hashContent($content);
        if ($contentHash && BlogBlocklist::where('content_hash', $contentHash)->exists()) {
            return true;
        }

        $titleFp = $this->fingerprintTitle($title);
        if ($titleFp && mb_strlen($titleFp, 'UTF-8') >= 12) {
            if (BlogBlocklist::where('title_fingerprint', $titleFp)->exists()) {
                return true;
            }

            $existingTitles = BlogBlocklist::whereNotNull('title_fingerprint')
                ->pluck('title_fingerprint');

            foreach ($existingTitles as $existing) {
                if ($this->titlesAreSimilar($titleFp, $existing)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a candidate article already exists in blog_posts (including soft-deleted)
     * by URL hash, content hash, or >= 85% title similarity.
     */
    public function isDuplicate(?string $url, ?string $title = null, ?string $content = null, ?int $ignorePostId = null): bool
    {
        $urlHash = $this->hashUrl($url);
        if ($urlHash) {
            $query = BlogPost::withTrashed()->where('source_url_hash', $urlHash);
            if ($ignorePostId) {
                $query->where('id', '!=', $ignorePostId);
            }
            if ($query->exists()) {
                return true;
            }
        }

        $contentHash = $this->hashContent($content);
        if ($contentHash) {
            $query = BlogPost::withTrashed()->where('content_hash', $contentHash);
            if ($ignorePostId) {
                $query->where('id', '!=', $ignorePostId);
            }
            if ($query->exists()) {
                return true;
            }
        }

        $titleFp = $this->fingerprintTitle($title);
        if ($titleFp && mb_strlen($titleFp, 'UTF-8') >= 12) {
            $exactQuery = BlogPost::withTrashed()->where('title_fingerprint', $titleFp);
            if ($ignorePostId) {
                $exactQuery->where('id', '!=', $ignorePostId);
            }
            if ($exactQuery->exists()) {
                return true;
            }

            $candidatesQuery = BlogPost::withTrashed()->whereNotNull('title_fingerprint');
            if ($ignorePostId) {
                $candidatesQuery->where('id', '!=', $ignorePostId);
            }

            foreach ($candidatesQuery->pluck('title_fingerprint') as $existing) {
                if ($this->titlesAreSimilar($titleFp, $existing)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function titlesAreSimilar(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }

        $lenA = mb_strlen($a, 'UTF-8');
        $lenB = mb_strlen($b, 'UTF-8');
        if ($lenA === 0 || $lenB === 0) {
            return false;
        }

        // Quick length ratio guard so very different length strings don't run similar_text
        if (min($lenA, $lenB) / max($lenA, $lenB) < 0.7) {
            return false;
        }

        $percent = 0.0;
        similar_text($a, $b, $percent);

        return $percent >= self::SIMILARITY_THRESHOLD;
    }
}
