<?php

namespace App\Services\Media;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Proxies Google Drive video files through our own server so the student
 * can preview course demo videos on-site without ever seeing or accessing
 * the raw Google Drive URL.
 */
class GoogleDriveStreamService
{
    public function extractFileId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        if (preg_match('~drive\.google\.com/file/d/([a-zA-Z0-9_-]+)~', $url, $m)) {
            return $m[1];
        }

        if (preg_match('~drive\.google\.com/(?:open|uc)\?[^#]*\bid=([a-zA-Z0-9_-]+)~', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function isGoogleDriveUrl(?string $url): bool
    {
        return $this->extractFileId($url) !== null;
    }

    public function stream(string $sourceUrl, Request $request): StreamedResponse
    {
        $fileId = $this->extractFileId($sourceUrl);
        $targetUrl = $fileId
            ? "https://drive.usercontent.google.com/download?id={$fileId}&export=download&confirm=t"
            : $sourceUrl;

        $upstreamHeaders = [
            'User-Agent' => 'Mozilla/5.0 (compatible; SikhunMediaProxy/1.0)',
        ];

        $range = $request->header('Range');
        if ($range) {
            $upstreamHeaders['Range'] = $range;
        }

        $response = Http::withHeaders($upstreamHeaders)
            ->withOptions(['stream' => true, 'allow_redirects' => true])
            ->timeout(60)
            ->get($targetUrl);

        if (! $response->successful() && $response->status() !== 206) {
            abort(404, 'Demo video is currently unavailable.');
        }

        $status = $response->status() === 206 ? 206 : 200;
        $contentType = $response->header('Content-Type') ?: 'video/mp4';
        if (str_contains(strtolower($contentType), 'text/html')) {
            abort(404, 'Demo video could not be streamed from source.');
        }

        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Accept-Ranges' => $response->header('Accept-Ranges') ?: 'bytes',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($len = $response->header('Content-Length')) {
            $headers['Content-Length'] = $len;
        }
        if ($contentRange = $response->header('Content-Range')) {
            $headers['Content-Range'] = $contentRange;
        }

        $body = $response->toPsrResponse()->getBody();

        return response()->stream(function () use ($body) {
            while (! $body->eof()) {
                echo $body->read(1024 * 64);
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, $status, $headers);
    }
}
