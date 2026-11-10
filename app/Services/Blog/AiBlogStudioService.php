<?php

namespace App\Services\Blog;

use App\Contracts\AiProviderContract;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\Ai\AiProviderFactory;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiBlogStudioService
{
    public function __construct(
        private readonly SiteSettingService $settings
    ) {}

    /**
     * Generate a complete educational blog article from topic with visuals, diagrams, and tables.
     *
     * @return array{
     *   title: string,
     *   slug: string,
     *   excerpt: string,
     *   content: string,
     *   featured_image_url: string,
     *   meta_title: string,
     *   meta_description: string,
     *   suggested_category_id: ?int
     * }
     */
    public function generateArticle(
        string $topic,
        string $targetAudience = 'hsc',
        string $tone = 'educational'
    ): array {
        $provider = $this->resolveProvider();

        $systemPrompt = <<<PROMPT
You are a master Bengali educational author, curriculum specialist, and science/technology writer for Sikhun.com (বাংলাদেশের শীর্ষ AI শিক্ষা প্ল্যাটফর্ম).
Your mission is to write a comprehensive, crystal-clear, engaging, and high-scoring educational blog guide in standard Bengali (প্রমিত বাংলা).

MANDATORY STRUCTURAL REQUIREMENTS:
1. Include an intuitive, step-by-step FLOWCHART diagram using Mermaid syntax inside a `<div class="mermaid-diagram bg-slate-900 text-slate-100 p-4 rounded-2xl my-6 font-mono text-xs overflow-x-auto"><pre class="mermaid">graph TD\n...</pre></div>`.
2. Include at least ONE well-structured comparison or reference TABLE (`<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-6 text-sm">...</table>`) with formatted headers and clear rows.
3. Include realistic solved examples with step-by-step workings.
4. Include a "গুরুত্বপূর্ণ টিপস ও ট্রিক্স" (Key Takeaways) callout box: `<div class="p-4 rounded-xl border border-orange-500/30 bg-orange-500/10 my-6 text-sm text-gray-800 dark:text-gray-200">...</div>`.
5. Return ONLY a valid JSON object with the exact keys:
{
  "title": "আকর্ষণীয় ও তথ্যবহুল বাংলা শিরোনাম",
  "slug": "english-ascii-kebab-slug",
  "excerpt": "২-৩ বাক্যে মূল সারাংশ (বাংলায়, ২৫০ অক্ষরের মধ্যে)",
  "featured_image_keyword": "technology / coding / mathematics / education / science",
  "meta_title": "SEO টাইটেল (৬০ অক্ষরের মধ্যে)",
  "meta_description": "SEO মেটা বর্ণনা (১৫৫ অক্ষরের মধ্যে)",
  "content": "<h2>...</h2><p>...</p><div class=\"mermaid-diagram\">...</div><table>...</table>..."
}
PROMPT;

        $userPrompt = "TOPIC: {$topic}\nTARGET AUDIENCE: {$targetAudience}\nTONE: {$tone}\n\nPlease generate the complete article adhering to all rules.";

        try {
            if ($provider) {
                $rawResponse = $provider->chat([
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ], [
                    'temperature' => 0.4,
                ]);

                $cleaned = trim($rawResponse);
                if (str_starts_with($cleaned, '```json')) {
                    $cleaned = preg_replace('/^```json\s*/', '', $cleaned);
                    $cleaned = preg_replace('/\s*```$/', '', $cleaned);
                } elseif (str_starts_with($cleaned, '```')) {
                    $cleaned = preg_replace('/^```\s*/', '', $cleaned);
                    $cleaned = preg_replace('/\s*```$/', '', $cleaned);
                }

                if (! str_starts_with(trim($cleaned), '{') && preg_match('/\{.*\}/s', $cleaned, $matches)) {
                    $cleaned = $matches[0];
                }

                $data = json_decode($cleaned, true);
                if (is_array($data) && ! empty($data['title']) && ! empty($data['content'])) {
                    return $this->formatArticlePayload($data, $topic);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AiBlogStudio generate failed, using synthesized educational fallback: ' . $e->getMessage());
        }

        return $this->generateFallbackArticle($topic, $targetAudience);
    }

    /**
     * Enhance an existing article section with AI
     */
    public function enhanceArticle(string $content, string $action, string $topic = ''): string
    {
        $provider = $this->resolveProvider();

        $prompts = [
            'expand' => "Take the existing article content and expand it with deeper conceptual explanations, real-world applications, and practical study techniques in Bengali. Return updated HTML only.",
            'add_quiz' => "Add a section at the end titled '<h2>অনুশীলন প্রশ্ন ও মডেল টেস্ট (Practice Questions)</h2>' with 4-5 standard multiple-choice questions (MCQs), options, and marked correct answers with explanations in Bengali. Return the appended HTML block.",
            'add_table' => "Generate a comprehensive, beautifully styled HTML comparison table (`<table class=\"w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm\">...</table>`) related to this topic. Return the table HTML only.",
            'add_flowchart' => "Create a step-by-step Mermaid flowchart diagram wrapped in `<div class=\"mermaid-diagram bg-slate-900 text-slate-100 p-4 rounded-xl my-4 text-xs font-mono overflow-x-auto\"><pre class=\"mermaid\">graph TD\n...</pre></div>` illustrating the core concept/algorithm. Return the div HTML only.",
        ];

        $instruction = $prompts[$action] ?? $prompts['expand'];

        try {
            if ($provider) {
                $response = $provider->chat([
                    ['role' => 'system', 'content' => "You are an educational AI assistant for Sikhun.com. {$instruction}"],
                    ['role' => 'user', 'content' => "Topic: {$topic}\n\nCurrent Content:\n" . Str::limit($content, 4000)],
                ]);

                $cleanHtml = trim(preg_replace('/^```html\s*|\s*```$/', '', trim($response)));
                if (! empty($cleanHtml)) {
                    if (in_array($action, ['add_quiz', 'add_table', 'add_flowchart'], true)) {
                        return $content . "\n\n" . $cleanHtml;
                    }
                    return $cleanHtml;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AiBlogStudio enhance failed: ' . $e->getMessage());
        }

        // Fallback enhancement blocks if provider not connected
        return match ($action) {
            'add_quiz' => $content . "\n\n" . $this->fallbackQuizHtml($topic),
            'add_table' => $content . "\n\n" . $this->fallbackTableHtml($topic),
            'add_flowchart' => $content . "\n\n" . $this->fallbackFlowchartHtml($topic),
            default => $content . "\n\n" . "<div class=\"p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 my-4\"><p class=\"text-sm font-semibold\">💡 পরীক্ষার জন্য বিশেষ নির্দেশনা: এই অধ্যায়ের প্রতিটি ধাপ নিয়মিত খাতায় লিখে অনুশীলন করুন। বিগত ৫ বছরের বোর্ড প্রশ্নগুলো বারবার সমাধান করলে পরীক্ষায় পূর্ণ নম্বর অর্জন করা সহজ হবে।</p></div>",
        };
    }

    private function formatArticlePayload(array $data, string $topic): array
    {
        $title = trim((string) ($data['title'] ?? $topic));
        $slug = Str::slug((string) ($data['slug'] ?? $topic));
        if ($slug === '') {
            $slug = 'blog-' . Str::lower(Str::random(8));
        }

        $excerpt = trim((string) ($data['excerpt'] ?? Str::limit(strip_tags((string) ($data['content'] ?? '')), 220)));
        $metaTitle = trim((string) ($data['meta_title'] ?? Str::limit($title, 60)));
        $metaDesc = trim((string) ($data['meta_description'] ?? Str::limit($excerpt, 155)));
        $content = (string) ($data['content'] ?? '');

        // Resolve matching category
        $category = BlogCategory::where('name_bn', 'like', '%আইসিটি%')
            ->orWhere('name_bn', 'like', '%বিজ্ঞান%')
            ->orWhere('name_bn', 'like', '%শিক্ষা%')
            ->first();

        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image_url' => $this->pickCoverImage($topic),
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'suggested_category_id' => $category?->id,
        ];
    }

    /**
     * Fallback structured generator with Flowchart and Table for popular topics like HSC ICT Number System
     */
    public function generateFallbackArticle(string $topic, string $targetAudience): array
    {
        $isNumberSystem = mb_stripos($topic, 'number') !== false || mb_stripos($topic, 'সংখ্যা') !== false || mb_stripos($topic, 'ict') !== false;

        $title = $isNumberSystem
            ? 'HSC ICT সংখ্যা পদ্ধতি (Number System): বাইনারি, অক্টাল, ডেসিমেল ও হেক্সাডেসিমেল রূপান্তরের সহজ নিয়ম'
            : "{$topic}: সহজ ব্যাখ্যা, ফ্লোচার্ট ও পূর্ণাঙ্গ গাইডলাইন";

        $slug = Str::slug($isNumberSystem ? 'hsc-ict-number-system-complete-guide' : $topic);

        $excerpt = 'এইচএসসি আইসিটি ৩য় অধ্যায়ের সংখ্যা পদ্ধতির বিভিন্ন রূপান্তর, বেস এবং রূপান্তরের শর্টকাট টেকনিক ও ফ্লোচার্টসহ পূর্ণাঙ্গ স্টাডি নোট।';

        $content = <<<HTML
<h2>১. সংখ্যা পদ্ধতি (Number System) কী ও এর প্রকারভেদ</h2>
<p>কোনো সংখ্যাকে লিখে প্রকাশ করার পদ্ধতিকে সংখ্যা পদ্ধতি বলা হয়। এইচএসসি আইসিটি পাঠ্যবই অনুযায়ী পজিশনাল সংখ্যা পদ্ধতি প্রধানত ৪ প্রকার। প্রতিটি পদ্ধতির নিজস্ব ভিত্তি বা বেস (Base) থাকে:</p>

<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-6 text-sm">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-800 text-left">
            <th class="p-3 border border-gray-300 dark:border-gray-700">সংখ্যা পদ্ধতির নাম</th>
            <th class="p-3 border border-gray-300 dark:border-gray-700">বেস (Base)</th>
            <th class="p-3 border border-gray-300 dark:border-gray-700">ব্যবহৃত মৌলিক চিহ্ন বা প্রতীক</th>
            <th class="p-3 border border-gray-300 dark:border-gray-700">উদাহরণ</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-bold">বাইনারি (Binary)</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">২</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">০, ১</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-mono">(101101)₂</td>
        </tr>
        <tr>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-bold">অক্টাল (Octal)</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">৮</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">০, ১, ২, ৩, ৪, ৫, ৬, ৭</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-mono">(375)₈</td>
        </tr>
        <tr>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-bold">ডেসিমেল বা দশমিক (Decimal)</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">১০</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">০ থেকে ৯ পর্যন্ত ১০টি অংক</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-mono">(94)₁₀</td>
        </tr>
        <tr>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-bold">হেক্সাডেসিমেল (Hexadecimal)</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">১৬</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700">০-৯ এবং A, B, C, D, E, F</td>
            <td class="p-3 border border-gray-300 dark:border-gray-700 font-mono">(2AF)₁₆</td>
        </tr>
    </tbody>
</table>

<h2>২. সংখ্যা রূপান্তরের ফ্লোচার্ট (Conversion Flowchart)</h2>
<p>যেকোনো সংখ্যা পদ্ধতি থেকে অন্য পদ্ধতিতে রূপান্তরের সুনির্দিষ্ট ধাপগুলো নিচের ফ্লোচার্টে দেখানো হলো:</p>

<div class="mermaid-diagram bg-slate-900 text-slate-100 p-5 rounded-2xl my-6 font-mono text-xs overflow-x-auto shadow-inner">
<pre class="mermaid">
graph TD
    Start([রূপান্তর শুরু]) --> InputNum[/প্রদত্ত সংখ্যা ও বেস গ্রহণ/]
    InputNum --> CheckType{টার্গেট বেস কী?}
    CheckType -->|ডেসিমেল থেকে অন্য বেস| StepDiv[পূর্ণাংশকে বেস দিয়ে ভাগ ও ভগ্নাংশকে বেস দিয়ে গুণ]
    CheckType -->|অন্য বেস থেকে ডেসিমেল| StepMul[স্থানীয় মান বা বেসের পাওয়ার দিয়ে গুণ করে যোগ]
    CheckType -->|বাইনারি ↔ অক্টাল| StepOct[৩-বিট গ্রুপিং পদ্ধতি]
    CheckType -->|বাইনারি ↔ হেক্সাডেসিমেল| StepHex[৪-বিট গ্রুপিং পদ্ধতি]
    StepDiv --> OutputNum[/ফলাফল সংরক্ষণ/]
    StepMul --> OutputNum
    StepOct --> OutputNum
    StepHex --> OutputNum
    OutputNum --> Finish([সমাপ্ত])
</pre>
</div>

<h2>৩. ডেসিমেল থেকে বাইনারি রূপান্তরের সহজ নিয়ম</h2>
<p>ধরা যাক, দশমিক সংখ্যা <strong>(25)₁₀</strong> কে বাইনারিতে রূপান্তর করতে হবে।</p>
<ol>
    <li>২৫ কে ২ দিয়ে ভাগ করলে ভাগফল ১২ এবং ভাগশেষ <strong>১</strong> (LSB)।</li>
    <li>১২ কে ২ দিয়ে ভাগ করলে ভাগফল ৬ এবং ভাগশেষ <strong>০</strong>।</li>
    <li>৬ কে ২ দিয়ে ভাগ করলে ভাগফল ৩ এবং ভাগশেষ <strong>০</strong>।</li>
    <li>৩ কে ২ দিয়ে ভাগ করলে ভাগফল ১ এবং ভাগশেষ <strong>১</strong>।</li>
    <li>১ কে ২ দিয়ে ভাগ করলে ভাগফল ০ এবং ভাগশেষ <strong>১</strong> (MSB)।</li>
</ol>
<p>নিচ থেকে উপরে ভাগশেষগুলো সাজালে পাই: <strong>(11001)₂</strong>। অর্থাৎ, <strong>(25)₁₀ = (11001)₂</strong>।</p>

<div class="p-4 rounded-xl border border-orange-500/30 bg-orange-500/10 my-6 text-sm text-gray-800 dark:text-gray-200">
    <strong class="text-orange-500 block mb-1">💡 দ্রুত রূপান্তরের ৮-৪-২-১ টেকনিক:</strong>
    ১, ২, ৪, ৮, ১৬, ৩২, ৬৪... এভাবে দ্বিগুণ করে মান হিসাব করে মুখে মুখেই যেকোনো সংখ্যার বাইনারি নির্ণয় করা সম্ভব। যেমন ২৫ = ১৬ + ৮ + ১, সুতরাং ১ ১ ০ ০ ১ = (11001)₂।
</div>

<h2>৪. বিগত বোর্ড পরীক্ষার গুরুত্বপূর্ণ সৃজনশীল প্রশ্নসমূহ</h2>
<ul>
    <li>১-এর পরিপূরক ও ২-এর পরিপূরকের (2's Complement) মাধ্যমে ঋণাত্মক সংখ্যার যোগ ও বিয়োগ।</li>
    <li>BCD কোড ও বাইনারি সংখ্যার মধ্যকার মূল পার্থক্য ব্যাখ্যা।</li>
    <li>ASCII ও Unicode কোডের বৈশিষ্ট্যাবলী।</li>
</ul>
HTML;

        $category = BlogCategory::first();

        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&auto=format&fit=crop&q=80',
            'meta_title' => Str::limit($title, 60),
            'meta_description' => Str::limit($excerpt, 155),
            'suggested_category_id' => $category?->id,
        ];
    }

    private function fallbackQuizHtml(string $topic): string
    {
        return <<<HTML
<h2>অনুশীলন প্রশ্ন ও মডেল টেস্ট (Practice Questions)</h2>
<div class="space-y-4 my-4 text-xs md:text-sm">
    <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
        <p class="font-bold text-gray-900 dark:text-white mb-2">১. (1011)₂ সংখ্যাটির সমতুল্য ডেসিমেল মান কত?</p>
        <p class="text-gray-600 dark:text-gray-300">ক) 9 &nbsp;&nbsp; খ) 10 &nbsp;&nbsp; <strong>গ) 11 (সঠিক উত্তর)</strong> &nbsp;&nbsp; ঘ) 13</p>
        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-2">ব্যাখ্যা: 1×2³ + 0×2² + 1×2¹ + 1×2⁰ = 8 + 0 + 2 + 1 = 11।</p>
    </div>
    <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
        <p class="font-bold text-gray-900 dark:text-white mb-2">২. হেক্সাডেসিমেল পদ্ধতিতে 'F' এর মান ডেসিমেল এ কত?</p>
        <p class="text-gray-600 dark:text-gray-300">ক) 10 &nbsp;&nbsp; খ) 12 &nbsp;&nbsp; গ) 14 &nbsp;&nbsp; <strong>ঘ) 15 (সঠিক উত্তর)</strong></p>
        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-2">ব্যাখ্যা: A=10, B=11, C=12, D=13, E=14, F=15।</p>
    </div>
</div>
HTML;
    }

    private function fallbackTableHtml(string $topic): string
    {
        return <<<HTML
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-6 text-sm">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-800 text-left">
            <th class="p-2 border border-gray-300 dark:border-gray-700">ডেসিমেল</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">বাইনারি (৪-বিট)</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">অক্টাল</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">হেক্সাডেসিমেল</th>
        </tr>
    </thead>
    <tbody>
        <tr><td class="p-2 border">0</td><td class="p-2 border font-mono">0000</td><td class="p-2 border">0</td><td class="p-2 border">0</td></tr>
        <tr><td class="p-2 border">5</td><td class="p-2 border font-mono">0101</td><td class="p-2 border">5</td><td class="p-2 border">5</td></tr>
        <tr><td class="p-2 border">8</td><td class="p-2 border font-mono">1000</td><td class="p-2 border">10</td><td class="p-2 border">8</td></tr>
        <tr><td class="p-2 border">10</td><td class="p-2 border font-mono">1010</td><td class="p-2 border">12</td><td class="p-2 border font-bold text-orange-500">A</td></tr>
        <tr><td class="p-2 border">15</td><td class="p-2 border font-mono">1111</td><td class="p-2 border">17</td><td class="p-2 border font-bold text-orange-500">F</td></tr>
    </tbody>
</table>
HTML;
    }

    private function fallbackFlowchartHtml(string $topic): string
    {
        return <<<HTML
<div class="mermaid-diagram bg-slate-900 text-slate-100 p-5 rounded-2xl my-6 font-mono text-xs overflow-x-auto shadow-inner">
<pre class="mermaid">
graph LR
    A[প্রদত্ত সমস্যা] --> B[সংজ্ঞায়ন ও পর্যবেক্ষণ]
    B --> C[ধাপভিত্তিক অ্যালগরিদম]
    C --> D[বাস্তবায়ন ও যাচাই]
    D --> E[চূড়ান্ত সমাধান]
</pre>
</div>
HTML;
    }

    private function pickCoverImage(string $topic): string
    {
        $text = mb_strtolower($topic);

        if (str_contains($text, 'ict') || str_contains($text, 'code') || str_contains($text, 'number') || str_contains($text, 'tech') || str_contains($text, 'প্রোগ্রাম')) {
            return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&auto=format&fit=crop&q=80';
        }

        if (str_contains($text, 'math') || str_contains($text, 'গণিত')) {
            return 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=1200&auto=format&fit=crop&q=80';
        }

        if (str_contains($text, 'physics') || str_contains($text, 'পদার্থ') || str_contains($text, 'রসায়ন') || str_contains($text, 'বিজ্ঞান')) {
            return 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=1200&auto=format&fit=crop&q=80';
        }

        return 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=1200&auto=format&fit=crop&q=80';
    }

    private function resolveProvider(): ?AiProviderContract
    {
        foreach (['blog_rewrite', 'blog_translate', 'book_chat'] as $useCase) {
            try {
                return AiProviderFactory::default($useCase);
            } catch (\Throwable) {
                // Next fallback
            }
        }

        return null;
    }
}
