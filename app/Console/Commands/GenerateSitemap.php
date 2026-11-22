<?php

namespace App\Console\Commands;

use App\Http\Controllers\Public\SitemapController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Refresh the dynamic XML sitemap cache (and remove any stale static public/sitemap.xml)';

    public function handle(SitemapController $controller): int
    {
        $base = rtrim(env('SEO_SITEMAP_BASE_URL', config('app.url', 'https://sikhun.com')), '/');

        config(['app.url' => $base]);
        URL::forceRootUrl($base);
        URL::forceScheme('https');

        // Remove any legacy static public/sitemap.xml so Nginx always delegates
        // /sitemap.xml to SitemapController::index() (single source of truth).
        $staticPath = public_path('sitemap.xml');
        if (is_file($staticPath)) {
            @unlink($staticPath);
        }

        SitemapController::clearCache();
        $xml = $controller->buildSitemapXml();
        Cache::put('sitemap_xml_payload', $xml, 3600);

        $urlCount = substr_count($xml, '<url>');
        $this->info("Dynamic sitemap cache refreshed with {$urlCount} URLs.");

        return self::SUCCESS;
    }
}
