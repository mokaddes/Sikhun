<?php

namespace App\Services\Blog;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ArticleExtractor
{
    private const USER_AGENT = 'SikhunBot/1.0 (+https://sikhun.com; educational content aggregator)';

    /**
     * Fetch and extract title + readable text from an article URL.
     * Falls back to $fallbackContent (e.g. from RSS feed) if page fetch fails or is shorter.
     *
     * @return array{title: string, content: string, language: string}
     */
    public function extract(
        string $url,
        ?string $titleSelector = null,
        ?string $contentSelector = null,
        ?string $fallbackTitle = null,
        ?string $fallbackContent = null
    ): array {
        $html = '';

        if ($this->isAllowedByRobots($url)) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => self::USER_AGENT,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                    ->timeout(20)
                    ->retry(2, 1000, throw: false)
                    ->get($url);

                if ($response->successful()) {
                    $html = (string) $response->body();
                }
            } catch (\Throwable) {
                // Fall back to RSS content if available
            }
        }

        $extracted = $html !== ''
            ? $this->extractFromHtml($html, $titleSelector, $contentSelector)
            : ['title' => '', 'content' => ''];

        $title = trim($extracted['title'] ?: (string) $fallbackTitle);
        $content = trim($extracted['content']);
        $fallbackClean = $this->cleanHtmlText((string) $fallbackContent);

        if (mb_strlen($content, 'UTF-8') < 200 && mb_strlen($fallbackClean, 'UTF-8') > mb_strlen($content, 'UTF-8')) {
            $content = $fallbackClean;
        }

        return [
            'title' => $title,
            'content' => $content,
            'language' => $this->detectLanguage($title . ' ' . $content),
        ];
    }

    /**
     * Extract title and readable body paragraphs from raw HTML.
     *
     * @return array{title: string, content: string}
     */
    public function extractFromHtml(string $html, ?string $titleSelector = null, ?string $contentSelector = null): array
    {
        $crawler = new Crawler($html);

        // Remove noise nodes before extracting text
        $crawler->filter('script, style, noscript, iframe, nav, footer, header, aside, form, svg, .ads, .advertisement, .sidebar, .comments')->each(function (Crawler $node) {
            foreach ($node as $domElement) {
                $domElement->parentNode?->removeChild($domElement);
            }
        });

        $title = '';
        if ($titleSelector) {
            try {
                if ($crawler->filter($titleSelector)->count() > 0) {
                    $title = trim($crawler->filter($titleSelector)->first()->text(''));
                }
            } catch (\Throwable) {
            }
        }

        if ($title === '') {
            foreach (['article h1', 'main h1', 'h1', 'title'] as $sel) {
                if ($crawler->filter($sel)->count() > 0) {
                    $title = trim($crawler->filter($sel)->first()->text(''));
                    if ($title !== '') {
                        break;
                    }
                }
            }
        }

        $content = '';
        if ($contentSelector) {
            try {
                if ($crawler->filter($contentSelector)->count() > 0) {
                    $content = $this->extractParagraphs($crawler->filter($contentSelector)->first());
                }
            } catch (\Throwable) {
            }
        }

        if (mb_strlen($content, 'UTF-8') < 150) {
            // Readability-style fallback: check <article>, <main>, or best block by <p> text length
            $bestText = '';
            $candidates = ['article', 'main', '[role="main"]', '.post-content', '.article-content', '.entry-content', 'body'];

            foreach ($candidates as $candidateSelector) {
                if ($crawler->filter($candidateSelector)->count() > 0) {
                    $candidateText = $this->extractParagraphs($crawler->filter($candidateSelector)->first());
                    if (mb_strlen($candidateText, 'UTF-8') > mb_strlen($bestText, 'UTF-8')) {
                        $bestText = $candidateText;
                    }
                    if (mb_strlen($bestText, 'UTF-8') >= 500) {
                        break;
                    }
                }
            }

            if ($bestText !== '') {
                $content = $bestText;
            }
        }

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    public function detectLanguage(string $text): string
    {
        $lettersOnly = preg_replace('/[^\p{L}]+/u', '', $text) ?? '';
        $totalLen = mb_strlen($lettersOnly, 'UTF-8');
        if ($totalLen === 0) {
            return 'en';
        }

        preg_match_all('/[\x{0980}-\x{09FF}]/u', $lettersOnly, $matches);
        $bengaliCount = count($matches[0]);

        return ($bengaliCount / $totalLen) >= 0.30 ? 'bn' : 'en';
    }

    public function isAllowedByRobots(string $url): bool
    {
        $parts = parse_url($url);
        if (! $parts || empty($parts['host'])) {
            return false;
        }

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'];
        $path = $parts['path'] ?? '/';

        $disallowed = Cache::remember("robots_disallow:{$host}", 21600, function () use ($scheme, $host) {
            try {
                $res = Http::withHeaders(['User-Agent' => self::USER_AGENT])
                    ->timeout(8)
                    ->get("{$scheme}://{$host}/robots.txt");

                if (! $res->successful()) {
                    return [];
                }

                return $this->parseRobotsDisallow((string) $res->body());
            } catch (\Throwable) {
                return [];
            }
        });

        foreach ($disallowed as $rule) {
            if ($rule === '/') {
                return false;
            }
            if ($rule !== '' && str_starts_with($path, $rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function parseRobotsDisallow(string $robotsTxt): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $robotsTxt) ?: [];
        $applies = false;
        $disallow = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/#.*$/', '', $line) ?? '');
            if ($line === '') {
                continue;
            }

            if (stripos($line, 'User-agent:') === 0) {
                $ua = trim(substr($line, 11));
                $applies = ($ua === '*' || stripos($ua, 'SikhunBot') !== false);
            } elseif ($applies && stripos($line, 'Disallow:') === 0) {
                $rule = trim(substr($line, 9));
                if ($rule !== '') {
                    $disallow[] = $rule;
                }
            }
        }

        return array_values(array_unique($disallow));
    }

    private function extractParagraphs(Crawler $container): string
    {
        $blocks = [];

        $container->filter('h1, h2, h3, p, li, blockquote')->each(function (Crawler $node) use (&$blocks) {
            $text = trim(preg_replace('/\s+/u', ' ', $node->text('')) ?? '');
            if (mb_strlen($text, 'UTF-8') >= 25) {
                $blocks[] = $text;
            }
        });

        if (empty($blocks)) {
            return trim(preg_replace('/\s+/u', ' ', $container->text('')) ?? '');
        }

        return implode("\n\n", array_values(array_unique($blocks)));
    }

    private function cleanHtmlText(string $raw): string
    {
        if (trim($raw) === '') {
            return '';
        }

        $text = preg_replace('/<\/(p|div|h[1-6]|li|br)\s*>/i', "\n\n", $raw) ?? $raw;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }
}
