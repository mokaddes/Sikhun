<?php

namespace App\Services\Blog;

use App\Models\BlogSource;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class BlogScraperService
{
    private const USER_AGENT = 'SikhunBot/1.0 (+https://sikhun.com; educational content aggregator)';

    public function __construct(
        private readonly ArticleExtractor $extractor
    ) {}

    /**
     * Fetch candidate article links/items from a BlogSource (RSS, Sitemap, or HTML list page).
     *
     * @return array<int, array{url: string, title: string, raw_content: string}>
     */
    public function fetchCandidates(BlogSource $source): array
    {
        if (! $this->extractor->isAllowedByRobots($source->url)) {
            throw new \RuntimeException("Scraping disallowed by robots.txt for {$source->url}");
        }

        $response = Http::withHeaders([
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'application/rss+xml, application/atom+xml, application/xml, text/xml, text/html, */*',
        ])
            ->timeout(25)
            ->retry(2, 1000)
            ->get($source->url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} fetching source {$source->url}");
        }

        $body = (string) $response->body();

        return match ($source->type) {
            'rss' => $this->parseRssOrAtom($body, $source->url),
            'sitemap' => $this->parseSitemap($body),
            'html_list' => $this->parseHtmlList($body, $source->url, $source->link_selector),
            default => $this->parseRssOrAtom($body, $source->url),
        };
    }

    /**
     * Parse RSS 2.0, RDF (RSS 1.0, e.g. arXiv), or Atom XML feeds.
     *
     * @return array<int, array{url: string, title: string, raw_content: string}>
     */
    public function parseRssOrAtom(string $xmlString, string $baseUrl = ''): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $xml) {
            throw new \RuntimeException('Invalid XML/RSS feed response.');
        }

        $items = [];

        // RSS 2.0: <rss><channel><item>
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $nsContent = $item->children('http://purl.org/rss/1.0/modules/content/');
                $rawContent = isset($nsContent->encoded)
                    ? (string) $nsContent->encoded
                    : (string) ($item->description ?? '');

                $link = trim((string) ($item->link ?? ''));
                if ($link !== '') {
                    $items[] = [
                        'url' => $this->resolveUrl($link, $baseUrl),
                        'title' => trim((string) ($item->title ?? '')),
                        'raw_content' => trim($rawContent),
                    ];
                }
            }
        }
        // RDF / RSS 1.0 (e.g. arXiv): <rdf:RDF><item>
        elseif (isset($xml->item)) {
            foreach ($xml->item as $item) {
                $link = trim((string) ($item->link ?? ''));
                if ($link !== '') {
                    $items[] = [
                        'url' => $this->resolveUrl($link, $baseUrl),
                        'title' => trim((string) ($item->title ?? '')),
                        'raw_content' => trim((string) ($item->description ?? '')),
                    ];
                }
            }
        }
        // Atom: <feed><entry>
        elseif (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $link = '';
                foreach ($entry->link as $linkNode) {
                    $rel = (string) ($linkNode['rel'] ?? 'alternate');
                    $href = (string) ($linkNode['href'] ?? '');
                    if ($href !== '' && ($rel === 'alternate' || $link === '')) {
                        $link = $href;
                    }
                }
                if ($link === '' && isset($entry->id)) {
                    $idStr = trim((string) $entry->id);
                    if (str_starts_with($idStr, 'http')) {
                        $link = $idStr;
                    }
                }

                $rawContent = (string) ($entry->content ?? $entry->summary ?? '');

                if ($link !== '') {
                    $items[] = [
                        'url' => $this->resolveUrl($link, $baseUrl),
                        'title' => trim((string) ($entry->title ?? '')),
                        'raw_content' => trim($rawContent),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Parse XML Sitemap <urlset><url><loc>.
     *
     * @return array<int, array{url: string, title: string, raw_content: string}>
     */
    public function parseSitemap(string $xmlString): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $xml) {
            throw new \RuntimeException('Invalid XML sitemap response.');
        }

        $items = [];
        foreach ($xml->url ?? [] as $urlNode) {
            $loc = trim((string) ($urlNode->loc ?? ''));
            if ($loc !== '') {
                $items[] = [
                    'url' => $loc,
                    'title' => '',
                    'raw_content' => '',
                ];
            }
        }

        return $items;
    }

    /**
     * Parse an HTML listing page for article links using a CSS selector.
     *
     * @return array<int, array{url: string, title: string, raw_content: string}>
     */
    public function parseHtmlList(string $html, string $baseUrl, ?string $linkSelector = null): array
    {
        $crawler = new Crawler($html, $baseUrl);
        $selector = $linkSelector ?: 'article a[href], main a[href], h2 a[href], h3 a[href]';

        $items = [];
        $seen = [];

        $crawler->filter($selector)->each(function (Crawler $node) use ($baseUrl, &$items, &$seen) {
            $href = trim((string) $node->attr('href'));
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) {
                return;
            }

            $fullUrl = $this->resolveUrl($href, $baseUrl);
            if (isset($seen[$fullUrl])) {
                return;
            }
            $seen[$fullUrl] = true;

            $items[] = [
                'url' => $fullUrl,
                'title' => trim($node->text('')),
                'raw_content' => '',
            ];
        });

        return $items;
    }

    private function resolveUrl(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $parts = parse_url($baseUrl);
        if (! $parts || empty($parts['host'])) {
            return $url;
        }

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'];
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        if (str_starts_with($url, '//')) {
            return "{$scheme}:{$url}";
        }

        if (str_starts_with($url, '/')) {
            return "{$scheme}://{$host}{$port}{$url}";
        }

        $basePath = isset($parts['path']) ? rtrim(dirname($parts['path']), '/\\') : '';

        return "{$scheme}://{$host}{$port}{$basePath}/{$url}";
    }
}
