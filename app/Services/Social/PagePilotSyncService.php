<?php

namespace App\Services\Social;

use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\JobCircular;
use App\Models\Plan;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagePilotSyncService
{
    public function __construct(protected SiteSettingService $settings) {}

    /**
     * Check if PagePilotBD sync is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->settings->get('pagepilot_webhook_url'))
            && ! empty($this->settings->get('pagepilot_webhook_key'));
    }

    /**
     * Resolve base PagePilot page URL for sync endpoints.
     */
    protected function getPageEndpoint(string $endpoint): ?string
    {
        $triggerUrl = trim((string) $this->settings->get('pagepilot_webhook_url'));
        if (empty($triggerUrl)) {
            return null;
        }

        // Replace /trigger with /sync-products or /sync-knowledge
        if (str_ends_with($triggerUrl, '/trigger')) {
            return substr($triggerUrl, 0, -8) . '/' . ltrim($endpoint, '/');
        }

        return rtrim($triggerUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Sync active subscription plans as products to PagePilotBD.
     */
    public function syncPlansToPagePilot(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PagePilotBD Webhook URL বা API Key সাইট সেটিংসে কনফিগার করা নেই।',
            ];
        }

        $url = $this->getPageEndpoint('sync-products');
        $apiKey = trim((string) $this->settings->get('pagepilot_webhook_key'));

        $plans = Plan::where('is_active', true)->get();
        if ($plans->isEmpty()) {
            return [
                'success' => false,
                'message' => 'কোনো সক্রিয় প্ল্যান পাওয়া যায়নি।',
            ];
        }

        $productsPayload = [];
        foreach ($plans as $plan) {
            $segmentLabel = match ($plan->target_segment) {
                'admission' => 'বিশ্ববিদ্যালয় ভর্তি',
                'job' => 'বিসিএস ও চাকরি',
                'hsc' => 'এইচএসসি (HSC)',
                'ssc' => 'এসএসসি (SSC)',
                'custom' => 'কাস্টম প্ল্যান',
                default => 'সকল সেগমেন্ট',
            };

            $featuresList = ! empty($plan->features) ? "• " . implode("\n• ", $plan->features) : '';
            $desc = "টার্গেট সেগমেন্ট: {$segmentLabel}\n"
                . "মাসিক ফি: ৳" . number_format($plan->price_monthly) . "\n"
                . "এআই এক্সপ্লেনেশন লিমিট: {$plan->ai_explanation_count} টি\n"
                . "এআই মক টেস্ট: {$plan->ai_exam_count} টি\n"
                . ($featuresList ? "\nপ্যাকেজের প্রধান সুবিধাসমূহ:\n{$featuresList}" : "");

            $productsPayload[] = [
                'sku' => "sikhun_plan_{$plan->id}_{$plan->slug}",
                'name' => "{$plan->name} ({$segmentLabel})",
                'description' => $desc,
                'price' => (float) $plan->price_monthly,
                'search_keywords' => "plan, subscription, {$plan->target_segment}, প্যাকেজ, প্ল্যান, সাবস্ক্রিপশন, ভর্তি, চাকরি, {$plan->name}, {$segmentLabel}",
                'product_link' => url('/pricing'),
                'order_url' => url('/pricing?plan=' . $plan->slug),
                'is_active' => true,
            ];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'X-PagePilot-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($url, [
                    'products' => $productsPayload,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->settings->set('pagepilot_last_plans_synced_at', now()->toDateTimeString());

                return [
                    'success' => true,
                    'message' => $data['message'] ?? (count($productsPayload) . 'টি প্ল্যান পেজপাইলটে সফলভাবে সিঙ্ক হয়েছে।'),
                    'synced_count' => count($productsPayload),
                ];
            }

            Log::error('PagePilotSync: Failed to sync plans', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'পেজপাইলট সিঙ্কে ত্রুটি: ' . ($response->json('message') ?? 'HTTP ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            Log::error('PagePilotSync: Exception while syncing plans: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'পেজপাইলট সিঙ্ক এক্সেপশন: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync site knowledge items (admissions, jobs, blogs) to PagePilotBD.
     */
    public function syncKnowledgeToPagePilot(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PagePilotBD Webhook URL বা API Key সাইট সেটিংসে কনফিগার করা নেই।',
            ];
        }

        $url = $this->getPageEndpoint('sync-knowledge');
        $apiKey = trim((string) $this->settings->get('pagepilot_webhook_key'));

        $items = [];

        // 1. Admission notices (last 20 published)
        $admissions = AdmissionNotice::published()->latest('id')->limit(20)->get();
        foreach ($admissions as $adm) {
            $items[] = [
                'type' => 'admission',
                'title' => $adm->title,
                'url' => url('/admission-notice/' . $adm->slug),
                'summary' => "প্রতিষ্ঠান: {$adm->institution_name} | শেষ তারিখ: {$adm->application_deadline_text} | ফি: {$adm->application_fee}",
                'keywords' => array_values(array_filter([$adm->institution_name, 'ভর্তি', 'সার্কুলার', 'admission', $adm->short_name])),
            ];
        }

        // 2. Job circulars (last 20 active)
        $jobs = JobCircular::where('deadline', '>=', now()->toDateString())->latest('id')->limit(20)->get();
        foreach ($jobs as $job) {
            $items[] = [
                'type' => 'job',
                'title' => $job->title,
                'url' => url('/job-news/' . $job->slug),
                'summary' => "প্রতিষ্ঠান: {$job->organization} | পদসংখ্যা: {$job->vacancy} | শেষ তারিখ: " . ($job->deadline ? $job->deadline->format('d/m/Y') : 'চলমান'),
                'keywords' => array_values(array_filter([$job->organization, 'চাকরি', 'সার্কুলার', 'job', 'নিয়োগ', $job->category])),
            ];
        }

        // 3. Blog posts (last 20 published)
        $blogs = BlogPost::where('status', 'published')->latest('published_at')->limit(20)->get();
        foreach ($blogs as $blog) {
            $items[] = [
                'type' => 'blog',
                'title' => $blog->title,
                'url' => url('/blog/' . $blog->slug),
                'summary' => $blog->excerpt ?: mb_substr(strip_tags($blog->content), 0, 200),
                'keywords' => array_values(array_filter(['ব্লগ', 'পড়াশোনা', 'গাইড', 'blog', $blog->category?->name])),
            ];
        }

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'সিঙ্ক করার মতো কোনো নলেজ আইটেম পাওয়া যায়নি।',
            ];
        }

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'X-PagePilot-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($url, [
                    'items' => $items,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->settings->set('pagepilot_last_knowledge_synced_at', now()->toDateTimeString());

                return [
                    'success' => true,
                    'message' => $data['message'] ?? (count($items) . 'টি নলেজ আইটেম পেজপাইলটে সফলভাবে সিঙ্ক হয়েছে।'),
                    'synced_count' => count($items),
                ];
            }

            Log::error('PagePilotSync: Failed to sync knowledge', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'পেজপাইলট নলেজ সিঙ্কে ত্রুটি: ' . ($response->json('message') ?? 'HTTP ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            Log::error('PagePilotSync: Exception while syncing knowledge: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'নলেজ সিঙ্ক এক্সেপশন: ' . $e->getMessage(),
            ];
        }
    }
}
