<?php

namespace App\Services\Blog;

use App\Contracts\AiProviderContract;
use App\Services\Ai\AiProviderFactory;
use App\Services\SiteSettingService;
use Illuminate\Support\Str;

class BlogRewriter
{
    public function __construct(
        private readonly SiteSettingService $settings
    ) {}

    /**
     * Rewrite translated Bengali material into an original, well-structured,
     * SEO-optimized Bengali educational blog article.
     *
     * @return array{
     *   title: string,
     *   slug: string,
     *   excerpt: string,
     *   meta_title: string,
     *   meta_description: string,
     *   content: string
     * }
     */
    public function rewrite(
        string $titleBn,
        string $contentBn,
        ?string $originalTitle = null,
        ?string $sourceUrl = null,
        ?string $sourceName = null
    ): array {
        $provider = $this->resolveProvider();

        // Trim input material to avoid exceeding context limits while keeping key information
        $material = mb_substr($contentBn, 0, 8000, 'UTF-8');

        $systemPrompt = <<<'PROMPT'
You are a senior Bengali educational & science/technology writer for Sikhun.com (বাংলাদেশের AI-চালিত শিক্ষা প্ল্যাটফর্ম).
Your task is to completely rewrite the given reference material into an original, engaging, student-friendly, and SEO-optimized blog article in standard Bengali (প্রমিত বাংলা).

Requirements:
1. Do NOT copy sentences verbatim; explain the concepts in your own clear, educational voice suitable for SSC, HSC, University, and tech-curious students in Bangladesh.
2. Structure the HTML body (`content`) using semantic tags ONLY: `<h2>`, `<h3>`, `<p>`, `<ul>`, `<ol>`, `<li>`, `<strong>`, `<blockquote>`. Do NOT include `<h1>`, `<html>`, `<body>`, or `<script>`.
3. Return ONLY a valid JSON object with these exact keys:
{
  "title": "আকর্ষণীয় ও তথ্যবহুল বাংলা শিরোনাম",
  "slug": "english-ascii-kebab-case-slug",
  "excerpt": "২-৩ বাক্যে মূল সারাংশ (বাংলায়, ২৫০ অক্ষরের মধ্যে)",
  "meta_title": "SEO মেটা টাইটেল (বাংলায়, ৬০ অক্ষরের মধ্যে)",
  "meta_description": "SEO মেটা ডেসক্রিপশন (বাংলায়, ১৫৫ অক্ষরের মধ্যে)",
  "content": "<h2>...</h2><p>...</p>..."
}
PROMPT;

        $userPrompt = "Original Title: " . ($originalTitle ?: $titleBn) . "\n"
            . "Bengali Working Title: {$titleBn}\n\n"
            . "Reference Material (Bengali):\n{$material}";

        $rawResponse = trim($provider->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ]));

        $parsed = $this->parseJsonResponse($rawResponse, $titleBn, $contentBn, $originalTitle);

        if ($sourceUrl && $this->shouldAppendCredit()) {
            $host = parse_url($sourceUrl, PHP_URL_HOST) ?: ($sourceName ?: 'মূল সূত্র');
            $label = e($sourceName ?: $host);
            $safeUrl = e($sourceUrl);
            $parsed['content'] .= "\n<hr><p class=\"text-sm text-muted\"><em>তথ্যসূত্র ও অনুপ্রেরণা: <a href=\"{$safeUrl}\" target=\"_blank\" rel=\"noopener nofollow\">{$label}</a></em></p>";
        }

        return $parsed;
    }

    private function parseJsonResponse(string $raw, string $fallbackTitle, string $fallbackContent, ?string $originalTitle): array
    {
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($raw)) ?? $raw;

        // Extract JSON object if surrounded by extra text
        if (! str_starts_with(trim($cleaned), '{') && preg_match('/\{.*\}/s', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        $data = json_decode($cleaned, true);

        if (! is_array($data) || empty($data['content'])) {
            $paragraphs = array_filter(array_map('trim', preg_split('/\n\s*\n/', $fallbackContent) ?: []));
            $htmlContent = implode("\n", array_map(fn ($p) => '<p>' . e($p) . '</p>', $paragraphs));

            $data = [
                'title' => $fallbackTitle,
                'slug' => Str::slug($originalTitle ?: $fallbackTitle),
                'excerpt' => Str::limit(strip_tags($fallbackContent), 220),
                'meta_title' => Str::limit($fallbackTitle, 65),
                'meta_description' => Str::limit(strip_tags($fallbackContent), 155),
                'content' => $htmlContent ?: '<p>' . e($fallbackContent) . '</p>',
            ];
        }

        $title = trim((string) ($data['title'] ?? $fallbackTitle)) ?: $fallbackTitle;
        $slugCandidate = Str::slug((string) ($data['slug'] ?? $originalTitle ?? ''));
        if ($slugCandidate === '') {
            $slugCandidate = 'blog-' . Str::lower(Str::random(8));
        }

        $plainBody = strip_tags((string) $data['content']);
        $excerpt = trim((string) ($data['excerpt'] ?? '')) ?: Str::limit($plainBody, 220);
        $metaTitle = trim((string) ($data['meta_title'] ?? '')) ?: Str::limit($title, 65);
        $metaDesc = trim((string) ($data['meta_description'] ?? '')) ?: Str::limit($excerpt, 155);

        return [
            'title' => Str::limit($title, 450, ''),
            'slug' => $slugCandidate,
            'excerpt' => Str::limit($excerpt, 900, ''),
            'meta_title' => Str::limit($metaTitle, 280, ''),
            'meta_description' => Str::limit($metaDesc, 480, ''),
            'content' => $this->sanitizeHtml((string) $data['content']),
        ];
    }

    private function sanitizeHtml(string $html): string
    {
        return strip_tags($html, '<h2><h3><h4><p><ul><ol><li><strong><em><b><i><blockquote><code><pre><hr><br><a>');
    }

    private function shouldAppendCredit(): bool
    {
        $val = $this->settings->get('blog_credit_enabled', '1');

        return ! in_array((string) $val, ['0', 'false', 'off'], true);
    }

    private function resolveProvider(): AiProviderContract
    {
        foreach (['blog_rewrite', 'blog_translate', 'book_chat'] as $useCase) {
            try {
                return AiProviderFactory::default($useCase);
            } catch (\Throwable) {
                // try next fallback
            }
        }

        throw new \RuntimeException("No active default AI provider configured for 'blog_rewrite' or 'book_chat'. Configure one in /admin/ai-providers.");
    }
}
