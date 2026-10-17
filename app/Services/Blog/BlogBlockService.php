<?php

namespace App\Services\Blog;

use App\Models\BlogBlocklist;
use App\Models\BlogPost;

class BlogBlockService
{
    public function __construct(private readonly BlogDeduplicator $deduplicator) {}

    /**
     * Add a blog post's hashes/fingerprints to the permanent blocklist and
     * soft-delete the post so it is unpublished and skipped on future scrapes.
     */
    public function blockPost(BlogPost $post, ?int $adminId = null, string $reason = 'Blocked by admin'): BlogBlocklist
    {
        $urlHash = $post->source_url_hash ?: $this->deduplicator->hashUrl($post->source_url);
        $contentHash = $post->content_hash ?: $this->deduplicator->hashContent($post->content);
        $titleFp = $post->title_fingerprint ?: $this->deduplicator->fingerprintTitle($post->source_title ?: $post->title);

        $attributes = [
            'content_hash' => $contentHash,
            'title_fingerprint' => $titleFp,
            'source_url' => $post->source_url,
            'source_title' => $post->source_title ?: $post->title,
            'reason' => $reason,
            'blocked_by' => $adminId,
        ];

        if ($urlHash) {
            $entry = BlogBlocklist::updateOrCreate(
                ['source_url_hash' => $urlHash],
                $attributes
            );
        } else {
            $entry = BlogBlocklist::create(array_merge(['source_url_hash' => null], $attributes));
        }

        if ($post->status !== 'draft') {
            $post->update(['status' => 'draft']);
        }

        if (! $post->trashed()) {
            $post->delete();
        }

        return $entry;
    }

    /**
     * Block an arbitrary external URL directly so the scraper never imports it.
     */
    public function blockUrl(string $url, ?string $title = null, ?string $reason = 'Manually blocked URL', ?int $adminId = null): BlogBlocklist
    {
        $urlHash = $this->deduplicator->hashUrl($url);
        $titleFp = $this->deduplicator->fingerprintTitle($title);

        return BlogBlocklist::updateOrCreate(
            ['source_url_hash' => $urlHash],
            [
                'title_fingerprint' => $titleFp,
                'source_url' => $url,
                'source_title' => $title,
                'reason' => $reason,
                'blocked_by' => $adminId,
            ]
        );
    }
}
