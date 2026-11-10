<?php

namespace App\Services\Social;

use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\JobCircular;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagePilotSocialService
{
    public function getWebhookUrl(): ?string
    {
        return SiteSetting::where('key', 'pagepilot_webhook_url')->value('value')
            ?: config('services.pagepilot.webhook_url', 'https://pagepilotbd.com/api/v1/pages/1299709296552706/trigger');
    }

    public function getApiKey(): ?string
    {
        return SiteSetting::where('key', 'pagepilot_api_key')->value('value')
            ?: config('services.pagepilot.api_key', 'pp_sec_44350dm0rtZ3kshrDTYM9CcmXS1VaPH2y2sh');
    }

    public function isAutoPublishEnabled(): bool
    {
        $val = SiteSetting::where('key', 'pagepilot_auto_publish')->value('value');
        if ($val === null) {
            return true; // default enabled
        }

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public function sendPost(array $payload): array
    {
        $url = $this->getWebhookUrl();
        $key = $this->getApiKey();

        if (empty($url) || empty($key)) {
            return [
                'success' => false,
                'message' => 'PagePilotBD API Webhook URL বা API Key কনফিগার করা নেই।',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'X-PagePilot-Key' => $key,
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PagePilotSocialService: Trigger failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $response->json('message') ?? ('PagePilot API Error (HTTP ' . $response->status() . ')'),
            ];
        } catch (\Throwable $e) {
            Log::error('PagePilotSocialService: Exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'সার্ভার সংযোগে ত্রুটি: ' . $e->getMessage(),
            ];
        }
    }

    public function publishAdmissionNotice(AdmissionNotice $notice): array
    {
        $imageUrl = $notice->featured_image
            ? (str_starts_with($notice->featured_image, 'http') ? $notice->featured_image : asset('storage/' . $notice->featured_image))
            : null;

        $summary = "ভর্তি সংক্রান্ত গুরুত্বপূর্ণ সময়সূচি:\n"
            . ($notice->application_start_text ? "• আবেদন শুরু: {$notice->application_start_text}\n" : "")
            . ($notice->application_deadline_text ? "• শেষ তারিখ: {$notice->application_deadline_text}\n" : "");

        return $this->sendPost([
            'title' => $notice->title,
            'url' => url('/admission-notice/' . $notice->slug),
            'type' => 'admission',
            'category' => $notice->category ?? 'বিশ্ববিদ্যালয় ভর্তি',
            'summary' => $summary,
            'image_url' => $imageUrl,
        ]);
    }

    public function publishJobCircular(JobCircular $job): array
    {
        $imageUrl = $job->image_url
            ? (str_starts_with($job->image_url, 'http') ? $job->image_url : asset('storage/' . $job->image_url))
            : null;

        $summary = "প্রতিষ্ঠানের নাম: {$job->organization}\n"
            . ($job->job_type ? "ধরন: {$job->job_type} | " : "")
            . ($job->vacancy ? "পদসংখ্যা: {$job->vacancy} | " : "")
            . ($job->deadline ? "আবেদনের শেষ তারিখ: " . $job->deadline->format('d/m/Y') : "");

        return $this->sendPost([
            'title' => $job->title,
            'url' => url('/job-news/' . $job->slug),
            'type' => 'job',
            'category' => $job->category ?? 'চাকরির খবর',
            'summary' => $summary,
            'image_url' => $imageUrl,
        ]);
    }

    public function publishBlogPost(BlogPost $post): array
    {
        $imageUrl = $post->featured_image_url;

        return $this->sendPost([
            'title' => $post->title,
            'url' => url('/blog/' . $post->slug),
            'type' => 'blog',
            'category' => $post->category?->name ?? 'শিক্ষা ও ক্যারিয়ার',
            'summary' => $post->excerpt,
            'image_url' => $imageUrl,
        ]);
    }

    public function testConnection(): array
    {
        return $this->sendPost([
            'title' => 'শিখুন (Sikhun.com) x PagePilotBD স্বয়ংক্রিয় ইন্টিগ্রেশন টেস্ট',
            'url' => 'https://sikhun.com',
            'type' => 'general',
            'category' => 'সিস্টেম টেস্ট',
            'summary' => 'এটি একটি টেস্ট পোস্ট। শিখুন.কম ওয়েবসাইট ও ফেসবুক পেজ স্বয়ংক্রিয়ভাবে সংযুক্ত হয়েছে।',
        ]);
    }
}
