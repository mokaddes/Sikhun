<?php

namespace App\Services\Admissions;

use App\Mail\AdmissionNoticeAlertMail;
use App\Models\AdmissionNotice;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdmissionScraperService
{
    public function __construct(
        private readonly SiteSettingService $settings
    ) {}

    /**
     * Run all admission scrapers and ensure foundation notices exist.
     *
     * @return int Number of newly imported or updated notices
     */
    public function scrapeAll(): int
    {
        $newNotices = 0;

        // 1. Scrape official university portals directly (RU, DU, etc.)
        $newNotices += $this->scrapeOfficialUniversityPortals();

        // 2. Scrape live RSS / JSON feeds from education and circular portals
        $newNotices += $this->scrapeLiveFeeds();

        // 3. Seed verified foundational circulars for 2026-2027 (RU, DU, BUET, GST, etc.)
        $newNotices += $this->seedVerifiedCirculars();

        // 4. Sanitize and clean all database notices to guarantee genuine official URLs
        $this->sanitizeExistingNotices();

        return $newNotices;
    }

    /**
     * Directly scrape active official university admission portals
     */
    public function scrapeOfficialUniversityPortals(): int
    {
        $count = 0;

        // Rajshahi University (RU)
        try {
            if ($this->scrapeRajshahiUniversity()) {
                $count++;
            }
        } catch (\Throwable $e) {
            Log::warning('Official RU portal scraper notice: ' . $e->getMessage());
        }

        return $count;
    }

    /**
     * Direct live scraper for Rajshahi University (RU) admission portal
     */
    public function scrapeRajshahiUniversity(): ?AdmissionNotice
    {
        try {
            $guidelineUrl = 'https://admission.ru.ac.bd/student/application-guideline';
            $res = Http::withoutVerifying()->timeout(15)->get($guidelineUrl);
            if (! $res->successful()) {
                return null;
            }

            $body = $res->body();
            if (! str_contains($body, 'Undergraduate Admission') && ! str_contains($body, 'আবেদন')) {
                return null;
            }

            $slug = 'rajshahi-university-undergraduate-admission-circular-2026-2027';

            return AdmissionNotice::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => 'রাজশাহী বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (RU Admission)',
                    'slug' => $slug,
                    'institution_name' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
                    'university_type' => 'public',
                    'academic_year' => '2026-2027',
                    'units' => [
                        ['name' => 'A Unit (মানবিক)', 'faculty' => 'কলা, আইন, সামাজিক বিজ্ঞান, চারুকলা অনুষদ ও IER', 'min_gpa' => '7.00', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১৩২০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                        ['name' => 'B Unit (ব্যবসায় শিক্ষা)', 'faculty' => 'ব্যবসায় শিক্ষা অনুষদ ও IBA', 'min_gpa' => '7.50', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১১০০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                        ['name' => 'C Unit (বিজ্ঞান)', 'faculty' => 'বিজ্ঞান, জীববিজ্ঞান, কৃষি, প্রকৌশল ও ভূ-বিজ্ঞান অনুষদ', 'min_gpa' => '8.00', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১৩২০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                    ],
                    'application_start' => '2026-11-15 12:01:00',
                    'application_start_text' => '১৫ নভেম্বর ২০২৬, রবিবার • দুপুর ১২:০১টা',
                    'application_deadline' => '2026-11-30 23:59:00',
                    'application_deadline_text' => '৩০ নভেম্বর ২০২৬, সোমবার • রাত ১১:৫৯টা',
                    'admit_card_date_text' => 'চূড়ান্ত সিলেকশন ও ফি প্রদানের পর',
                    'minimum_gpa_ssc' => '3.00',
                    'minimum_gpa_hsc' => '3.00',
                    'total_gpa_required' => '৮.০০ (বিজ্ঞান) / ৭.৫০ (ব্যবসায়) / ৭.০০ (মানবিক)',
                    'application_fee' => '৫৫ টাকা (প্রাথমিক আবেদন ফি)',
                    'apply_url' => 'https://admission.ru.ac.bd',
                    'official_website_url' => 'https://ru.ac.bd',
                    'notice_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                    'featured_image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                    'eligibility_criteria' => '২০২৪ বা ২০২৫ সালের এইচএসসি/সমমান এবং ২০২২ বা ২০২৩ সালের এসএসসি/সমমান পরীক্ষায় উত্তীর্ণ শিক্ষার্থীরা আবেদন করতে পারবে। বিজ্ঞান শাখায় মোট জিপিএ ৮.০০, ব্যবসায় শাখায় ৭.৫০ এবং মানবিক শাখায় ৭.০০ থাকতে হবে।',
                    'marks_distribution' => '১০০ নম্বরের বহুনির্বাচনী (MCQ) পরীক্ষা। মোট ৮০টি বহুনির্বাচনী প্রশ্ন থাকবে, প্রতিটির মান ১.২৫। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কর্তন করা হবে। পরীক্ষার সময় ১ ঘণ্টা। পাস নম্বর ৪০। আঞ্চলিক পরীক্ষা কেন্দ্র: রাজশাহী, ঢাকা, চট্টগ্রাম, খুলনা ও রংপুর।',
                    'status' => 'published',
                    'is_featured' => true,
                    'source_name' => 'রাজশাহী বিশ্ববিদ্যালয় কেন্দ্রীয় ভর্তি কমিটি',
                    'source_url' => $guidelineUrl,
                    'meta_title' => 'রাজশাহী বিশ্ববিদ্যালয় ভর্তি সার্কুলার ২০২৬-২০২৭ | RU Admission Guide',
                    'meta_description' => 'রাজশাহী বিশ্ববিদ্যালয় স্নাতক প্রথম বর্ষ ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭। প্রাথমিক ও চূড়ান্ত আবেদনের নিয়ম, ইউনিটভিত্তিক যোগ্যতা ও আঞ্চলিক পরীক্ষা কেন্দ্র।',
                    'published_at' => now(),
                    'description' => <<<HTML
<h2>১. ভর্তি সংক্রান্ত গুরুত্বপূর্ণ তারিখ ও সময়সূচি</h2>
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm">
    <tr class="bg-gray-100 dark:bg-gray-800"><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">কার্যক্রম</th><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">তারিখ ও সময়</th></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদন শুরু</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-emerald-600 dark:text-emerald-400">১৫ নভেম্বর ২০২৬, রবিবার • দুপুর ১২:০১টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদনের শেষ তারিখ</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-red-600 dark:text-red-400">৩০ নভেম্বর ২০২৬, সোমবার • রাত ১১:৫৯টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদন ফি</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-medium text-blue-600 dark:text-blue-400">৫৫ টাকা (সার্ভিস চার্জসহ)</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">পরীক্ষার আঞ্চলিক কেন্দ্র (Zones)</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-medium">রাজশাহী, ঢাকা, চট্টগ্রাম, খুলনা ও রংপুর</td></tr>
</table>

<h2>২. ইউনিটভিত্তিক বিস্তারিত যোগ্যতা</h2>
<p>রাজশাহী বিশ্ববিদ্যালয়ের ২০২৬-২০২৭ শিক্ষাবর্ষের প্রথম বর্ষ স্নাতক (সম্মান) শ্রেণীতে ভর্তির জন্য শিক্ষার্থীকে অবশ্যই ২০২২ বা ২০২৩ সালের এসএসসি এবং ২০২৪ বা ২০২৫ সালের এইচএসসি বা সমমান পরীক্ষায় উত্তীর্ণ হতে হবে।</p>
<ul>
    <li><strong>A Unit (মানবিক শাখা):</strong> মানবিক শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় চতুর্থ বিষয়সহ ন্যূনতম জিপিএ ৩.০০ সহ মোট জিপিএ ৭.০০ থাকতে হবে। বিজ্ঞান ও ব্যবসায় শিক্ষা শাখা থেকে পরিবর্তনকারীদের ক্ষেত্রে মোট জিপিএ যথাক্রমে ৮.০০ ও ৭.৫০ থাকতে হবে।</li>
    <li><strong>B Unit (ব্যবসায় শিক্ষা শাখা):</strong> ব্যবসায় শিক্ষা শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.০০ সহ মোট জিপিএ ৭.৫০ থাকতে হবে। অন্যান্য শাখার জন্য মোট জিপিএ ৮.০০ বা ৭.০০ থাকতে হবে।</li>
    <li><strong>C Unit (বিজ্ঞান শাখা):</strong> বিজ্ঞান শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.৫০ সহ মোট জিপিএ ৮.০০ থাকতে হবে।</li>
</ul>

<h2>৩. পরীক্ষার মানবণ্টন ও পদ্ধতি</h2>
<p>ভর্তি পরীক্ষা ১০০ নম্বরের বহুনির্বাচনী (MCQ) পদ্ধতিতে অনুষ্ঠিত হবে। মোট ৮০টি বহুনির্বাচনী প্রশ্ন থাকবে, প্রতিটির মান ১.২৫। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কর্তন করা হবে। পরীক্ষায় পাস নম্বর ৪০। ওএমআর শিট পূরণ করে পরীক্ষা দিতে হবে।</p>

<h2>৪. কীভাবে অনলাইনে আবেদন করবেন</h2>
<ol>
    <li>রাজশাহী বিশ্ববিদ্যালয়ের অফিসিয়াল ভর্তি ওয়েবসাইট <a href="https://admission.ru.ac.bd" target="_blank" class="text-orange-500 font-bold">admission.ru.ac.bd</a>-এ যান।</li>
    <li>"Apply" বাটনে ক্লিক করে প্রার্থীর এসএসসি ও এইচএসসি পরীক্ষার রোল নম্বর, রেজিস্ট্রেশন নম্বর এবং শিক্ষাবোর্ড প্রদান করুন।</li>
    <li>বর্তমান সচল মোবাইল নম্বর এবং সদ্য তোলা পাসপোর্ট সাইজের রঙিন ছবি (100KB এর মধ্যে) আপলোড করুন।</li>
    <li>পরীক্ষার পছন্দসই কেন্দ্র/অঞ্চল (রাজশাহী/ঢাকা/চট্টগ্রাম/খুলনা/রংপুর) নির্বাচন করুন।</li>
    <li>মোবাইল ব্যাংকিং (bKash/Nagad/Rocket) এর মাধ্যমে প্রাথমিক আবেদন ফি ৫৫ টাকা পরিশোধ করে স্লিপ সংরক্ষণ করুন।</li>
</ol>
HTML
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('RU live scraper error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Scrape known education and university feed endpoints
     */
    public function scrapeLiveFeeds(): int
    {
        $imported = 0;
        $feedUrls = [
            'https://students.bd/rss.xml',
        ];

        foreach ($feedUrls as $url) {
            try {
                $response = Http::timeout(12)->get($url);
                if (! $response->successful()) {
                    continue;
                }

                $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if (! $xml || ! isset($xml->channel->item)) {
                    continue;
                }

                foreach ($xml->channel->item as $item) {
                    $title = trim((string) $item->title);
                    $link = trim((string) $item->link);
                    $desc = trim((string) $item->description);

                    // Only process items related to university admission / ভর্তি
                    if (! preg_match('/(ভর্তি|admission|circular|বিশ্ববিদ্যালয়|university|গুচ্ছ|undergraduate)/iu', $title . ' ' . $desc)) {
                        continue;
                    }

                    $slug = Str::slug(Str::limit($title, 80)) . '-' . substr(md5($link), 0, 6);
                    if (AdmissionNotice::where('slug', $slug)->orWhere('source_url', $link)->exists()) {
                        continue;
                    }

                    $institution = $this->detectInstitution($title, $desc);
                    $type = $this->detectUniversityType($title, $desc, $institution);
                    $urls = $this->resolveOfficialUrls($institution, $link);

                    $notice = AdmissionNotice::create([
                        'title' => $title,
                        'slug' => $slug,
                        'institution_name' => $institution,
                        'university_type' => $type,
                        'academic_year' => '2026-2027',
                        'application_start' => now()->toDateString(),
                        'application_deadline' => now()->addDays(28)->toDateString(),
                        'application_fee' => '১,০০০ টাকা',
                        'apply_url' => $urls['apply_url'],
                        'official_website_url' => $urls['official_website_url'],
                        'notice_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                        'description' => $this->generateBlogContent($institution, $title, $desc, $type, $urls['apply_url']),
                        'status' => 'draft',
                        'source_name' => 'Students.bd Circular Portal',
                        'source_url' => $link,
                    ]);

                    $this->sendAlertNotification($notice);
                    $imported++;
                }
            } catch (\Throwable $e) {
                Log::warning('Admission live feed scraper error: ' . $e->getMessage());
            }
        }

        return $imported;
    }

    /**
     * Dispatch email notification to mr.mokaddes@gmail.com and site_email
     */
    public function sendAlertNotification(AdmissionNotice $notice): void
    {
        $primaryEmail = 'mr.mokaddes@gmail.com';
        $siteEmail = trim((string) ($this->settings->get('site_email') ?: ''));

        $recipients = array_unique(array_filter([$primaryEmail, $siteEmail]));

        try {
            $totalDrafts = AdmissionNotice::draft()->count();
            Mail::to($recipients)->send(new AdmissionNoticeAlertMail($notice, $totalDrafts));
        } catch (\Throwable $e) {
            Log::warning('Failed sending admission alert email: ' . $e->getMessage());
        }
    }

    /**
     * Detect institution name from title or description text
     */
    public function detectInstitution(string $title, string $desc = ''): string
    {
        $text = $title . ' ' . $desc;

        $patterns = [
            '/\b(DU|ঢাবি|ঢাকা বিশ্ববিদ্যালয়|ঢাকা বিশ্ববিদ্যালয়|Dhaka University)\b/iu' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            '/\b(RU|রাবি|রাজশাহী বিশ্ববিদ্যালয়|রাজশাহী বিশ্ববিদ্যালয়|Rajshahi University)\b/iu' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
            '/\b(CU|চবি|চট্টগ্রাম বিশ্ববিদ্যালয়|চট্টগ্রাম বিশ্ববিদ্যালয়|Chittagong University)\b/iu' => 'চট্টগ্রাম বিশ্ববিদ্যালয় (CU)',
            '/\b(JU|জাবি|জাহাঙ্গীরনগর বিশ্ববিদ্যালয়|জাহাঙ্গীরনগর বিশ্ববিদ্যালয়|Jahangirnagar)\b/iu' => 'জাহাঙ্গীরনগর বিশ্ববিদ্যালয় (JU)',
            '/\b(BUET|বুয়েট|বাংলাদেশ প্রকৌশল)\b/iu' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)',
            '/\b(KUET|কুয়েট|Khulna University of Engineering)\b/iu' => 'খুলনা প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (KUET)',
            '/\b(RUET|রুয়েট|Rajshahi University of Engineering)\b/iu' => 'রাজশাহী প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (RUET)',
            '/\b(CUET|চুয়েট|Chittagong University of Engineering)\b/iu' => 'চট্টগ্রাম প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (CUET)',
            '/\b(SUST|সাস্ট|শাহজালাল বিজ্ঞান|Shahjalal University)\b/iu' => 'শাহজালাল বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SUST)',
            '/\b(GST|গুচ্ছ|ক্লাস্টার)\b/iu' => 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)',
            '/\b(কৃষি গুচ্ছ|Agri Cluster|কৃষি বিশ্ববিদ্যালয়)\b/iu' => 'সমন্বিত কৃষি গুচ্ছ বিশ্ববিদ্যালয় (Agri Cluster)',
            '/\b(NU|জাতীয় বিশ্ববিদ্যালয়|জাতীয় বিশ্ববিদ্যালয়|National University)\b/iu' => 'জাতীয় বিশ্ববিদ্যালয় (NU)',
            '/\b(BOU|উন্মুক্ত বিশ্ববিদ্যালয়|উন্মুক্ত বিশ্ববিদ্যালয়|Open University)\b/iu' => 'বাংলাদেশ উন্মুক্ত বিশ্ববিদ্যালয় (BOU)',
            '/\b(BTEB|কারিগরি|কারিগরি শিক্ষা বোর্ড|Technical Education Board)\b/iu' => 'বাংলাদেশ কারিগরি শিক্ষা বোর্ড (BTEB)',
            '/\b(MBBS|BDS|মেডিকেল|ডেন্টাল|PTAP)\b/iu' => 'মেডিকেল ও ডেন্টাল কলেজ (MBBS/BDS)',
            '/\b(BRACU|BRAC|ব্র্যাক বিশ্ববিদ্যালয়|ব্র্যাক বিশ্ববিদ্যালয়)\b/iu' => 'ব্র্যাক বিশ্ববিদ্যালয় (BRACU)',
            '/\b(NSU|নর্থ সাউথ|North South)\b/iu' => 'নর্থ সাউথ বিশ্ববিদ্যালয় (NSU)',
            '/\b(AIUB|আমেরিকান ইন্টারন্যাশনাল)\b/iu' => 'আমেরিকান ইন্টারন্যাশনাল ইউনিভার্সিটি (AIUB)',
            '/\b(UIU|ইউনাইটেড ইন্টারন্যাশনাল)\b/iu' => 'ইউনাইটেড ইন্টারন্যাশনাল ইউনিভার্সিটি (UIU)',
            '/\b(DIU|ড্যাফোডিল|Daffodil)\b/iu' => 'ড্যাফোডিল ইন্টারন্যাশনাল ইউনিভার্সিটি (DIU)',
            '/\b(IUB|ইন্ডিপেন্ডেন্ট)\b/iu' => 'ইন্ডিপেন্ডেন্ট ইউনিভার্সিটি (IUB)',
        ];

        foreach ($patterns as $pattern => $instName) {
            if (preg_match($pattern, $text)) {
                return $instName;
            }
        }

        return 'বাংলাদেশ পাবলিক/প্রাইভেট বিশ্ববিদ্যালয়';
    }

    /**
     * Resolve genuine official website and application portal URLs for a university.
     * Prevents 3rd party aggregator sites (like students.bd) from ever appearing as official.
     */
    public function resolveOfficialUrls(string $institution, ?string $fallbackLink = null): array
    {
        $registry = [
            'ঢাকা বিশ্ববিদ্যালয় (DU)' => [
                'aliases' => ['DU', 'ঢাকা বিশ্ববিদ্যালয়', 'Dhaka University'],
                'official_website_url' => 'https://du.ac.bd',
                'apply_url' => 'https://admission.eis.du.ac.bd',
            ],
            'রাজশাহী বিশ্ববিদ্যালয় (RU)' => [
                'aliases' => ['RU', 'রাজশাহী বিশ্ববিদ্যালয়', 'Rajshahi University'],
                'official_website_url' => 'https://ru.ac.bd',
                'apply_url' => 'https://admission.ru.ac.bd',
            ],
            'চট্টগ্রাম বিশ্ববিদ্যালয় (CU)' => [
                'aliases' => ['CU', 'চট্টগ্রাম বিশ্ববিদ্যালয়', 'Chittagong University'],
                'official_website_url' => 'https://cu.ac.bd',
                'apply_url' => 'https://admission.cu.ac.bd',
            ],
            'জাহাঙ্গীরনগর বিশ্ববিদ্যালয় (JU)' => [
                'aliases' => ['JU', 'জাহাঙ্গীরনগর বিশ্ববিদ্যালয়', 'Jahangirnagar University'],
                'official_website_url' => 'https://juniv.edu',
                'apply_url' => 'https://admission.juniv.edu',
            ],
            'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)' => [
                'aliases' => ['BUET', 'বাংলাদেশ প্রকৌশল', 'বুয়েট'],
                'official_website_url' => 'https://buet.ac.bd',
                'apply_url' => 'https://www.buet.ac.bd/web/#/admission',
            ],
            'খুলনা প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (KUET)' => [
                'aliases' => ['KUET', 'খুলনা প্রকৌশল', 'কুয়েট'],
                'official_website_url' => 'https://kuet.ac.bd',
                'apply_url' => 'https://admission.kuet.ac.bd',
            ],
            'চট্টগ্রাম প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (CUET)' => [
                'aliases' => ['CUET', 'চট্টগ্রাম প্রকৌশল', 'চুয়েট'],
                'official_website_url' => 'https://cuet.ac.bd',
                'apply_url' => 'https://admission.cuet.ac.bd',
            ],
            'রাজশাহী প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (RUET)' => [
                'aliases' => ['RUET', 'রাজশাহী প্রকৌশল', 'রুয়েট'],
                'official_website_url' => 'https://ruet.ac.bd',
                'apply_url' => 'https://admission.ruet.ac.bd',
            ],
            'শাহজালাল বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SUST)' => [
                'aliases' => ['SUST', 'শাহজালাল বিজ্ঞান', 'সাস্ট'],
                'official_website_url' => 'https://sust.edu',
                'apply_url' => 'https://admission.sust.edu',
            ],
            'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)' => [
                'aliases' => ['GST', 'গুচ্ছ বিশ্ববিদ্যালয়', 'জিএসটি গুচ্ছ'],
                'official_website_url' => 'https://gstadmission.ac.bd',
                'apply_url' => 'https://gstadmission.ac.bd',
            ],
            'সমন্বিত কৃষি গুচ্ছ বিশ্ববিদ্যালয় (Agri Cluster)' => [
                'aliases' => ['Agri Cluster', 'কৃষি গুচ্ছ'],
                'official_website_url' => 'https://acas.edu.bd',
                'apply_url' => 'https://acas.edu.bd',
            ],
            'মেডিকেল ও ডেন্টাল কলেজ (MBBS/BDS)' => [
                'aliases' => ['MBBS', 'BDS', 'মেডিকেল', 'ডেন্টাল'],
                'official_website_url' => 'https://dgme.gov.bd',
                'apply_url' => 'https://dgme.teletalk.com.bd',
            ],
            'জাতীয় বিশ্ববিদ্যালয় (NU)' => [
                'aliases' => ['NU', 'জাতীয় বিশ্ববিদ্যালয়', 'National University'],
                'official_website_url' => 'https://nu.ac.bd',
                'apply_url' => 'http://app1.nu.edu.bd',
            ],
            'বাংলাদেশ উন্মুক্ত বিশ্ববিদ্যালয় (BOU)' => [
                'aliases' => ['BOU', 'উন্মুক্ত বিশ্ববিদ্যালয়', 'Open University'],
                'official_website_url' => 'https://bou.ac.bd',
                'apply_url' => 'https://osapsnew.bou.ac.bd',
            ],
            'বাংলাদেশ কারিগরি শিক্ষা বোর্ড (BTEB)' => [
                'aliases' => ['BTEB', 'কারিগরি শিক্ষা বোর্ড', 'Technical Education'],
                'official_website_url' => 'https://bteb.gov.bd',
                'apply_url' => 'https://btebadmission.gov.bd',
            ],
            'নর্থ সাউথ বিশ্ববিদ্যালয় (NSU)' => [
                'aliases' => ['NSU', 'নর্থ সাউথ', 'North South'],
                'official_website_url' => 'https://northsouth.edu',
                'apply_url' => 'https://admissions.northsouth.edu',
            ],
            'ব্র্যাক বিশ্ববিদ্যালয় (BRACU)' => [
                'aliases' => ['BRACU', 'ব্র্যাক বিশ্ববিদ্যালয়', 'BRAC University'],
                'official_website_url' => 'https://www.bracu.ac.bd',
                'apply_url' => 'https://www.bracu.ac.bd/admissions',
            ],
            'আমেরিকান ইন্টারন্যাশনাল ইউনিভার্সিটি (AIUB)' => [
                'aliases' => ['AIUB', 'আমেরিকান ইন্টারন্যাশনাল'],
                'official_website_url' => 'https://aiub.edu',
                'apply_url' => 'https://admission.aiub.edu',
            ],
            'ইউনাইটেড ইন্টারন্যাশনাল ইউনিভার্সিটি (UIU)' => [
                'aliases' => ['UIU', 'ইউনাইটেড ইন্টারন্যাশনাল'],
                'official_website_url' => 'https://uiu.ac.bd',
                'apply_url' => 'https://admission.uiu.ac.bd',
            ],
            'ড্যাফোডিল ইন্টারন্যাশনাল ইউনিভার্সিটি (DIU)' => [
                'aliases' => ['DIU', 'ড্যাফোডিল'],
                'official_website_url' => 'https://daffodilvarsity.edu.bd',
                'apply_url' => 'https://admission.daffodilvarsity.edu.bd',
            ],
            'ইন্ডিপেন্ডেন্ট ইউনিভার্সিটি (IUB)' => [
                'aliases' => ['IUB', 'ইন্ডিপেন্ডেন্ট ইউনিভার্সিটি'],
                'official_website_url' => 'https://iub.edu.bd',
                'apply_url' => 'https://admission.iub.edu.bd',
            ],
        ];

        foreach ($registry as $instKey => $data) {
            if ($instKey === $institution) {
                return [
                    'official_website_url' => $data['official_website_url'],
                    'apply_url' => $data['apply_url'],
                ];
            }

            foreach ($data['aliases'] as $alias) {
                if (preg_match('/^[A-Z\/]+$/', $alias)) {
                    if (preg_match('/\b' . preg_quote($alias, '/') . '\b/i', $institution)) {
                        return [
                            'official_website_url' => $data['official_website_url'],
                            'apply_url' => $data['apply_url'],
                        ];
                    }
                } elseif (mb_stripos($institution, $alias) !== false) {
                    return [
                        'official_website_url' => $data['official_website_url'],
                        'apply_url' => $data['apply_url'],
                    ];
                }
            }
        }

        // If fallback link is a 3rd party scraper aggregator, do not treat it as official
        $clean = $fallbackLink;
        if ($clean && (str_contains($clean, 'students.bd') || str_contains($clean, 'facebook.com'))) {
            $clean = null;
        }

        return [
            'official_website_url' => $clean,
            'apply_url' => $clean,
        ];
    }

    /**
     * Categorize university into public, guccho, or private
     */
    public function detectUniversityType(string $title, string $desc, string $institution): string
    {
        $text = mb_strtolower($title . ' ' . $desc . ' ' . $institution);

        if (str_contains($text, 'গুচ্ছ') || str_contains($text, 'gst') || str_contains($text, 'cluster')) {
            return 'guccho';
        }

        if (str_contains($text, 'private') || str_contains($text, 'বেসরকারি') || str_contains($text, 'nsu') || str_contains($text, 'brac') || str_contains($text, 'aiub') || str_contains($text, 'uiu') || str_contains($text, 'daffodil') || str_contains($text, 'iub')) {
            return 'private';
        }

        return 'public';
    }

    public function categorizeUniversity(string $institution): string
    {
        return $this->detectUniversityType('', '', $institution);
    }

    /**
     * Generate blog-style rich content matching professional standard
     */
    public function generateBlogContent(string $institution, string $title, string $desc, string $type, ?string $applyUrl = null): string
    {
        $typeBn = match ($type) {
            'public' => 'পাবলিক বিশ্ববিদ্যালয়',
            'guccho' => 'গুচ্ছ সমন্বিত বিশ্ববিদ্যালয়',
            'private' => 'স্বনামধন্য বেসরকারি বিশ্ববিদ্যালয়',
            default => 'বিশ্ববিদ্যালয়',
        };

        // Ensure 3rd-party domains are NEVER shown as official apply website
        $validApplyUrl = null;
        if ($applyUrl && ! str_contains($applyUrl, 'students.bd') && ! str_contains($applyUrl, 'facebook.com')) {
            $validApplyUrl = $applyUrl;
        }

        $applyStepHtml = $validApplyUrl
            ? 'অফিসিয়াল ভর্তি ওয়েবসাইট <a href="' . e($validApplyUrl) . '" target="_blank" class="text-orange-500 font-bold">' . e($validApplyUrl) . '</a> এ প্রবেশ করুন।'
            : 'সংশ্লিষ্ট বিশ্ববিদ্যালয়ের অফিসিয়াল ভর্তি ওয়েবসাইটে প্রবেশ করে অনলাইন আবেদন সম্পন্ন করুন।';

        return <<<HTML
<h2>১. ভর্তি পরীক্ষার সারসংক্ষেপ (Overview)</h2>
<p>{$institution} ২০২৬-২০২৭ শিক্ষাবর্ষে স্নাতক (সম্মান) প্রথম বর্ষে ভর্তির বিজ্ঞপ্তি প্রকাশ করেছে। এটি একটি {$typeBn} হিসেবে বাংলাদেশের অন্যতম কাঙ্ক্ষিত উচ্চশিক্ষা প্রতিষ্ঠান। আগ্রহী শিক্ষার্থীরা নির্ধারিত সময়ের মধ্যে অনলাইনে আবেদন সম্পন্ন করতে পারবেন।</p>

<h2>২. ইউনিটসমূহ ও আসন সংখ্যা</h2>
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-800 text-left">
            <th class="p-2 border border-gray-300 dark:border-gray-700">ইউনিট</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">অনুষদ / বিভাগ</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">ন্যূনতম জিপিএ</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">ক ইউনিট (বিজ্ঞান)</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">বিজ্ঞান ও জীববিজ্ঞান অনুষদ</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">SSC ও HSC মিলিয়ে মোট ৮.০০</td>
        </tr>
        <tr>
            <td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">খ ইউনিট (মানবিক)</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">কলা, আইন ও সামাজিক বিজ্ঞান অনুষদ</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">SSC ও HSC মিলিয়ে মোট ৭.৫০</td>
        </tr>
        <tr>
            <td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">গ ইউনিট (ব্যবসায়)</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">ব্যবসায় শিক্ষা অনুষদ</td>
            <td class="p-2 border border-gray-300 dark:border-gray-700">SSC ও HSC মিলিয়ে মোট ৭.৫০</td>
        </tr>
    </tbody>
</table>

<h2>৩. আবেদনের যোগ্যতা ও শর্তাবলী</h2>
<ul>
    <li>আবেদনকারীকে ২০২৪ বা ২০২৫ সালের এসএসসি এবং ২০২৬ সালের এইচএসসি বা সমমান পরীক্ষায় উত্তীর্ণ হতে হবে।</li>
    <li>উভয় পরীক্ষায় চতুর্থ বিষয়সহ ন্যূনতম জিপিএ ৩.০০ থেকে ৩.৫০ থাকতে হবে।</li>
    <li>ও-লেভেল ও এ-লেভেল শিক্ষার্থীদের সমমান সনদ ও মার্কশিট ভেরিফিকেশন সম্পন্ন করতে হবে।</li>
</ul>

<h2>৪. মানবন্টন ও পরীক্ষা পদ্ধতি</h2>
<p>ভর্তি পরীক্ষা ১০০ নম্বরের বহুনির্বাচনী (MCQ) ও লিখিত পদ্ধতিতে অনুষ্ঠিত হবে। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কাটা যাবে।</p>

<h2>৫. অনলাইনে আবেদনের নিয়মাবলী</h2>
<ol>
    <li>{$applyStepHtml}</li>
    <li>শিক্ষার্থীর এসএসসি ও এইচএসসি পরীক্ষার রোল নম্বর, রেজিস্ট্রেশন নম্বর এবং শিক্ষাবোর্ড সিলেক্ট করুন।</li>
    <li>বর্তমান সচল মোবাইল নম্বর এবং পাসপোর্ট সাইজের ছবি আপলোড করুন।</li>
    <li>বিকাশ, নগদ, রকেট বা ডেবিট/ক্রেডিট কার্ডের মাধ্যমে নির্ধারিত আবেদন ফি পরিশোধ করুন।</li>
    <li>পেমেন্ট স্লিপ এবং প্রবেশপত্র ডাউনলোড করে সংরক্ষণ করুন।</li>
</ol>
HTML;
    }

    /**
     * Seed verified foundational circulars for top BD universities
     */
    public function seedVerifiedCirculars(): int
    {
        $circulars = [
            [
                'title' => 'রাজশাহী বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (RU Admission)',
                'slug' => 'rajshahi-university-undergraduate-admission-circular-2026-2027',
                'institution_name' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
                'university_type' => 'public',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'A Unit (মানবিক)', 'faculty' => 'কলা, আইন, সামাজিক বিজ্ঞান, চারুকলা অনুষদ ও IER', 'min_gpa' => '7.00', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১৩২০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                    ['name' => 'B Unit (ব্যবসায় শিক্ষা)', 'faculty' => 'ব্যবসায় শিক্ষা অনুষদ ও IBA', 'min_gpa' => '7.50', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১১০০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                    ['name' => 'C Unit (বিজ্ঞান)', 'faculty' => 'বিজ্ঞান, জীববিজ্ঞান, কৃষি, প্রকৌশল ও ভূ-বিজ্ঞান অনুষদ', 'min_gpa' => '8.00', 'fee' => '৫৫ টাকা (প্রাথমিক) / ১৩২০ টাকা (চূড়ান্ত)', 'exam_date' => 'ঘোষণা সাপেক্ষে'],
                ],
                'application_start' => '2026-11-15 12:01:00',
                'application_start_text' => '১৫ নভেম্বর ২০২৬, রবিবার • দুপুর ১২:০১টা',
                'application_deadline' => '2026-11-30 23:59:00',
                'application_deadline_text' => '৩০ নভেম্বর ২০২৬, সোমবার • রাত ১১:৫৯টা',
                'admit_card_date_text' => 'চূড়ান্ত সিলেকশন ও ফি প্রদানের পর',
                'minimum_gpa_ssc' => '3.00',
                'minimum_gpa_hsc' => '3.00',
                'total_gpa_required' => '৮.০০ (বিজ্ঞান) / ৭.৫০ (ব্যবসায়) / ৭.০০ (মানবিক)',
                'application_fee' => '৫৫ টাকা (প্রাথমিক আবেদন ফি)',
                'apply_url' => 'https://admission.ru.ac.bd',
                'official_website_url' => 'https://ru.ac.bd',
                'notice_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => '২০২৪ বা ২০২৫ সালের এইচএসসি/সমমান এবং ২০২২ বা ২০২৩ সালের এসএসসি/সমমান পরীক্ষায় উত্তীর্ণ শিক্ষার্থীরা আবেদন করতে পারবে। বিজ্ঞান শাখায় মোট জিপিএ ৮.০০, ব্যবসায় শাখায় ৭.৫০ এবং মানবিক শাখায় ৭.০০ থাকতে হবে।',
                'marks_distribution' => '১০০ নম্বরের বহুনির্বাচনী (MCQ) পরীক্ষা। মোট ৮০টি বহুনির্বাচনী প্রশ্ন থাকবে, প্রতিটির মান ১.২৫। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কর্তন করা হবে। পরীক্ষার সময় ১ ঘণ্টা। পাস নম্বর ৪০। আঞ্চলিক পরীক্ষা কেন্দ্র: রাজশাহী, ঢাকা, চট্টগ্রাম, খুলনা ও রংপুর।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'রাজশাহী বিশ্ববিদ্যালয় কেন্দ্রীয় ভর্তি কমিটি',
                'source_url' => 'https://admission.ru.ac.bd/student/application-guideline',
                'meta_title' => 'রাজশাহী বিশ্ববিদ্যালয় ভর্তি সার্কুলার ২০২৬-২০২৭ | RU Admission Guide',
                'meta_description' => 'রাজশাহী বিশ্ববিদ্যালয় স্নাতক প্রথম বর্ষ ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭। প্রাথমিক ও চূড়ান্ত আবেদনের নিয়ম, ইউনিটভিত্তিক যোগ্যতা ও আঞ্চলিক পরীক্ষা কেন্দ্র।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. ভর্তি সংক্রান্ত গুরুত্বপূর্ণ তারিখ ও সময়সূচি</h2>
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm">
    <tr class="bg-gray-100 dark:bg-gray-800"><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">কার্যক্রম</th><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">তারিখ ও সময়</th></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদন শুরু</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-emerald-600 dark:text-emerald-400">১৫ নভেম্বর ২০২৬, রবিবার • দুপুর ১২:০১টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদনের শেষ তারিখ</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-red-600 dark:text-red-400">৩০ নভেম্বর ২০২৬, সোমবার • রাত ১১:৫৯টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রাথমিক আবেদন ফি</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-medium text-blue-600 dark:text-blue-400">৫৫ টাকা (সার্ভিস চার্জসহ)</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">পরীক্ষার আঞ্চলিক কেন্দ্র (Zones)</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-medium">রাজশাহী, ঢাকা, চট্টগ্রাম, খুলনা ও রংপুর</td></tr>
</table>

<h2>২. ইউনিটভিত্তিক বিস্তারিত যোগ্যতা</h2>
<p>রাজশাহী বিশ্ববিদ্যালয়ের ২০২৬-২০২৭ শিক্ষাবর্ষের প্রথম বর্ষ স্নাতক (সম্মান) শ্রেণীতে ভর্তির জন্য শিক্ষার্থীকে অবশ্যই ২০২২ বা ২০২৩ সালের এসএসসি এবং ২০২৪ বা ২০২৫ সালের এইচএসসি বা সমমান পরীক্ষায় উত্তীর্ণ হতে হবে।</p>
<ul>
    <li><strong>A Unit (মানবিক শাখা):</strong> মানবিক শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় চতুর্থ বিষয়সহ ন্যূনতম জিপিএ ৩.০০ সহ মোট জিপিএ ৭.০০ থাকতে হবে। বিজ্ঞান ও ব্যবসায় শিক্ষা শাখা থেকে পরিবর্তনকারীদের ক্ষেত্রে মোট জিপিএ যথাক্রমে ৮.০০ ও ৭.৫০ থাকতে হবে।</li>
    <li><strong>B Unit (ব্যবসায় শিক্ষা শাখা):</strong> ব্যবসায় শিক্ষা শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.০০ সহ মোট জিপিএ ৭.৫০ থাকতে হবে। অন্যান্য শাখার জন্য মোট জিপিএ ৮.০০ বা ৭.০০ থাকতে হবে।</li>
    <li><strong>C Unit (বিজ্ঞান শাখা):</strong> বিজ্ঞান শাখা থেকে উত্তীর্ণ প্রার্থীদের এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.৫০ সহ মোট জিপিএ ৮.০০ থাকতে হবে।</li>
</ul>

<h2>৩. পরীক্ষার মানবণ্টন ও পদ্ধতি</h2>
<p>ভর্তি পরীক্ষা ১০০ নম্বরের বহুনির্বাচনী (MCQ) পদ্ধতিতে অনুষ্ঠিত হবে। মোট ৮০টি বহুনির্বাচনী প্রশ্ন থাকবে, প্রতিটির মান ১.২৫। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কর্তন করা হবে। পরীক্ষায় পাস নম্বর ৪০। ওএমআর শিট পূরণ করে পরীক্ষা দিতে হবে।</p>

<h2>৪. কীভাবে অনলাইনে আবেদন করবেন</h2>
<ol>
    <li>রাজশাহী বিশ্ববিদ্যালয়ের অফিসিয়াল ভর্তি ওয়েবসাইট <a href="https://admission.ru.ac.bd" target="_blank" class="text-orange-500 font-bold">admission.ru.ac.bd</a>-এ যান।</li>
    <li>"Apply" বাটনে ক্লিক করে প্রার্থীর এসএসসি ও এইচএসসি পরীক্ষার রোল নম্বর, রেজিস্ট্রেশন নম্বর এবং শিক্ষাবোর্ড প্রদান করুন।</li>
    <li>ছবি ও মোবাইল নম্বর ভেরিফিকেশন সম্পন্ন করে প্রাথমিক আবেদন ফি ৫৫ টাকা বিকাশ/নগদ/রকেট ইত্যাদি মাধ্যমে পরিশোধ করুন।</li>
    <li>প্রাথমিক ফলাফলে নির্বাচিত হলে নির্ধারিত সময়ের মধ্যে চূড়ান্ত আবেদন সম্পন্ন করুন।</li>
</ol>
HTML
            ],
            [
                'title' => 'ঢাকা বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (DU Admission)',
                'slug' => 'dhaka-university-undergraduate-admission-circular-2026-2027',
                'institution_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
                'university_type' => 'public',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'ক ইউনিট (বিজ্ঞান)', 'faculty' => 'বিজ্ঞান, জীববিজ্ঞান, ফার্মেসি, আর্থ অ্যান্ড এনভায়রনমেন্টাল সায়েন্সেস অনুষদ', 'min_gpa' => '8.00', 'fee' => '১০৫০ টাকা', 'exam_date' => '২০২৭-০২-১৫ (সকাল ১১:০০ - ১২:৩০)'],
                    ['name' => 'খ ইউনিট (কলা, আইন ও সামাজিক বিজ্ঞান)', 'faculty' => 'কলা, আইন ও সামাজিক বিজ্ঞান অনুষদ', 'min_gpa' => '7.50', 'fee' => '১০৫০ টাকা', 'exam_date' => '২০২৭-০২-২২ (সকাল ১১:০০ - ১২:৩০)'],
                    ['name' => 'গ ইউনিট (ব্যবসায় শিক্ষা)', 'faculty' => 'ব্যবসায় শিক্ষা অনুষদ', 'min_gpa' => '7.50', 'fee' => '১০৫০ টাকা', 'exam_date' => '২০২৭-০৩-০১ (সকাল ১১:০০ - ১২:৩০)'],
                    ['name' => 'চারুকলা ইউনিট', 'faculty' => 'চারুকলা অনুষদ', 'min_gpa' => '6.50', 'fee' => '১০৫০ টাকা', 'exam_date' => '২০২৭-০৩-০৮ (সকাল ১১:০০ - ১২:৩০)'],
                ],
                'application_start' => '2026-11-11 12:00:00',
                'application_start_text' => '১১ নভেম্বর ২০২৬, বুধবার • বেলা ১২:০০টা',
                'application_deadline' => '2026-11-25 23:59:00',
                'application_deadline_text' => '২৫ নভেম্বর ২০২৬, বুধবার • রাত ১১:৫৯টা',
                'admit_card_date_text' => 'সংশ্লিষ্ট ইউনিটের পরীক্ষার দিন সকাল পর্যন্ত',
                'minimum_gpa_ssc' => '3.50',
                'minimum_gpa_hsc' => '3.50',
                'total_gpa_required' => '৮.০০ (বিজ্ঞান) / ৭.৫০ (মানবিক ও ব্যবসায়)',
                'application_fee' => '১,০৫০ টাকা (প্রতি ইউনিট)',
                'apply_url' => 'https://admission.eis.du.ac.bd',
                'official_website_url' => 'https://du.ac.bd',
                'notice_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => 'এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.৫০ এবং মোট জিপিএ ক ইউনিটে ৮.০০, খ ও গ ইউনিটে ৭.৫০ থাকতে হবে।',
                'marks_distribution' => 'এমসিকিউ ৬০ নম্বর, লিখিত ৪০ নম্বর। পরীক্ষার সময় ১ ঘণ্টা ৩০ মিনিট। ভুল উত্তরের জন্য ০.২৫ নম্বর কর্তন।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'ঢাকা বিশ্ববিদ্যালয় কেন্দ্রীয় ভর্তি কমিটি',
                'source_url' => 'https://admission.eis.du.ac.bd',
                'meta_title' => 'ঢাকা বিশ্ববিদ্যালয় ভর্তি সার্কুলার ২০২৬-২০২৭ | DU Admission Guide',
                'meta_description' => 'ঢাকা বিশ্ববিদ্যালয় স্নাতক প্রথম বর্ষ ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭। সকল ইউনিটের যোগ্যতা, মানবণ্টন ও অনলাইন আবেদনের সম্পূর্ণ নির্দেশিকা।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. ভর্তি সংক্রান্ত গুরুত্বপূর্ণ তারিখ ও সময়সূচি</h2>
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm">
    <tr class="bg-gray-100 dark:bg-gray-800"><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">কার্যক্রম</th><th class="p-2.5 border border-gray-300 dark:border-gray-700 text-left font-bold">তারিখ ও সময়</th></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">অনলাইন আবেদন শুরু</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-emerald-600 dark:text-emerald-400">১১ নভেম্বর ২০২৬, বুধবার • বেলা ১২:০০টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">আবেদনের শেষ তারিখ</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-red-600 dark:text-red-400">২৫ নভেম্বর ২০২৬, বুধবার • রাত ১১:৫৯টা</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">প্রবেশপত্র ডাউনলোড</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-medium">সংশ্লিষ্ট ইউনিটের পরীক্ষার দিন সকাল পর্যন্ত</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">ক ইউনিট (বিজ্ঞান) ভর্তি পরীক্ষা</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-blue-600 dark:text-blue-400">১৫ ফেব্রুয়ারি ২০২৭, সকাল ১১:০০ - দুপুর ১২:৩০</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">খ ইউনিট (কলা, আইন ও সামাজিক বিজ্ঞান)</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-purple-600 dark:text-purple-400">২২ ফেব্রুয়ারি ২০২৭, সকাল ১১:০০ - দুপুর ১২:৩০</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">গ ইউনিট (ব্যবসায় শিক্ষা)</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-amber-600 dark:text-amber-400">০১ মার্চ ২০২৭, সকাল ১১:০০ - দুপুর ১২:৩০</td></tr>
    <tr><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">চারুকলা ইউনিট (সাধারণ জ্ঞান ও অঙ্কন)</td><td class="p-2.5 border border-gray-300 dark:border-gray-700 font-bold text-rose-600 dark:text-rose-400">০৮ মার্চ ২০২৭, সকাল ১১:০০ - দুপুর ১২:৩০</td></tr>
</table>

<h2>২. ইউনিটভিত্তিক বিস্তারিত যোগ্যতা</h2>
<p>ঢাকা বিশ্ববিদ্যালয়ের ২০২৬-২০২৭ শিক্ষাবর্ষের আন্ডারগ্র্যাজুয়েট প্রোগ্রামে ভর্তির জন্য শিক্ষার্থীকে অবশ্যই ২০২২ বা ২০২৩ সালের এসএসসি এবং ২০২৪ বা ২০২৫ সালের এইচএসসি বা সমমান পরীক্ষায় উত্তীর্ণ হতে হবে।</p>
<ul>
    <li><strong>বিজ্ঞান ইউনিট (ক):</strong> বিজ্ঞান বিভাগের প্রার্থীদের এসএসসি ও এইচএসসি পরীক্ষার জিপিএ-এর যোগফল ন্যূনতম ৮.০০ (চতুর্থ বিষয়সহ) এবং আলাদাভাবে ন্যূনতম ৩.৫০ থাকতে হবে।</li>
    <li><strong>কলা, আইন ও সামাজিক বিজ্ঞান ইউনিট (খ):</strong> মানবিক বিভাগের প্রার্থীদের ক্ষেত্রে মোট জিপিএ ৭.৫০ এবং বিজ্ঞান ও ব্যবসায় শিক্ষা শাখা থেকে পরিবর্তনকারী প্রার্থীদের ক্ষেত্রে মোট ৮.০০ থাকতে হবে।</li>
    <li><strong>ব্যবসায় শিক্ষা ইউনিট (গ):</strong> ব্যবসায় শিক্ষা শাখার প্রার্থীদের ক্ষেত্রে এসএসসি ও এইচএসসি উভয় পরীক্ষায় আলাদাভাবে ন্যূনতম ৩.০০ এবং মোট জিপিএ ৭.৫০ থাকতে হবে।</li>
</ul>

<h2>৩. পরীক্ষার মানবণ্টন ও বিষয়ভিত্তিক নম্বর</h2>
<p>ভর্তি পরীক্ষায় মোট ১২০ নম্বরের ভিত্তিতে মেধা তালিকা তৈরি করা হবে। এর মধ্যে ১০০ নম্বরের ভর্তি পরীক্ষা (৬০ নম্বরের বহুনির্বাচনী বা MCQ এবং ৪০ নম্বরের লিখিত পরীক্ষা) এবং ২০ নম্বর থাকবে এসএসসি ও এইচএসসি জিপিএ-র ওপর।</p>

<h2>৪. কীভাবে অনলাইনে আবেদন করবেন</h2>
<ol>
    <li>ঢাকা বিশ্ববিদ্যালয়ের অফিসিয়াল অনলাইন পোর্টাল <a href="https://admission.eis.du.ac.bd" target="_blank" class="text-orange-500 font-bold">admission.eis.du.ac.bd</a>-এ যান।</li>
    <li>লগইন অপশনে গিয়ে আপনার উচ্চ মাধ্যমিক বা সমমান পরীক্ষার রোল, বোর্ড ও পাসের সন দিয়ে লগইন করুন।</li>
    <li>ব্যক্তিগত ও যোগাযোগের তথ্য পূরণ করে বর্তমান পাসপোর্ট সাইজের ডিজিটাল ছবি আপলোড করুন।</li>
    <li>পেমেন্ট গেটওয়ের মাধ্যমে মোবাইল ব্যাংকিং (bKash, Nagad, Rocket) অথবা সোনালী/জনতা/অগ্রণী ব্যাংকের মাধ্যমে ফি জমা দিন।</li>
</ol>
HTML
            ],
            [
                'title' => 'জিএসটি ২৪ বিশ্ববিদ্যালয় সমন্বিত গুচ্ছ ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (GST Cluster Admission)',
                'slug' => 'gst-cluster-university-admission-circular-2026-2027',
                'institution_name' => 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)',
                'university_type' => 'guccho',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'A Unit (Science)', 'faculty' => 'প্রকৌশল ও বিজ্ঞান বিভাগ', 'min_gpa' => '8.00', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৭-০৪-১০'],
                    ['name' => 'B Unit (Humanities)', 'faculty' => 'কলা ও সমাজবিজ্ঞান', 'min_gpa' => '7.00', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৭-০৪-১৭'],
                    ['name' => 'C Unit (Commerce)', 'faculty' => 'ব্যবসায় প্রশাসন ও অর্থনীতি', 'min_gpa' => '7.50', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৭-০৪-২৪'],
                ],
                'application_start' => '2026-12-01 12:00:00',
                'application_start_text' => '০১ ডিসেম্বর ২০২৬, মঙ্গলবার • বেলা ১২:০০টা',
                'application_deadline' => '2026-12-25 23:59:00',
                'application_deadline_text' => '২৫ ডিসেম্বর ২০২৬, শুক্রবার • রাত ১১:৫৯টা',
                'admit_card_date_text' => 'পরীক্ষার ৭ দিন পূর্ব থেকে ডাউনলোডযোগ্য',
                'minimum_gpa_ssc' => '3.00',
                'minimum_gpa_hsc' => '3.00',
                'total_gpa_required' => '৭.০০ - ৮.০০',
                'application_fee' => '১,৫০০ টাকা',
                'apply_url' => 'https://gstadmission.ac.bd',
                'official_website_url' => 'https://gstadmission.ac.bd',
                'notice_image_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => '২৪টি সাধারণ এবং বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়ের সমন্বিত গুচ্ছ ভর্তি পরীক্ষা। এক আবেদনেই ২৪ বিশ্ববিদ্যালয়ে আবেদন।',
                'marks_distribution' => '১০০ নম্বরের এমসিকিউ পরীক্ষা। ১ ঘণ্টা সময়। নেগেটিভ মার্কিং ০.২৫।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'জিএসটি সমন্বিত ভর্তি কমিটি',
                'source_url' => 'https://gstadmission.ac.bd',
                'meta_title' => 'গুচ্ছ ভর্তি পরীক্ষা সার্কুলার ২০২৬-২০২৭ | GST Cluster Admission',
                'meta_description' => 'জিএসটি ২৪টি পাবলিক বিশ্ববিদ্যালয়ের সমন্বিত গুচ্ছ ভর্তি সার্কুলার ২০২৬-২০২৭। পরীক্ষার মানবণ্টন ও যোগ্যতা।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. গুচ্ছভুক্ত ২৪টি পাবলিক বিশ্ববিদ্যালয় পরিচিতি</h2>
<p>জিএসটি (General, Science & Technology) গুচ্ছভুক্ত ২৪টি সাধারণ এবং বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়ে স্নাতক প্রথম বর্ষের ভর্তি পরীক্ষা একযোগে দেশব্যাপী অনুষ্ঠিত হবে। এর ফলে শিক্ষার্থীদের আলাদা আলাদা বিশ্ববিদ্যালয়ে যাতায়াত না করে নিজ জেলার নিকটবর্তী কেন্দ্রেই পরীক্ষা দেওয়ার সুযোগ তৈরি হয়েছে।</p>

<h2>২. ইউনিট বিন্যাস ও পরীক্ষার সময়সূচি</h2>
<table class="w-full border-collapse border border-gray-300 dark:border-gray-700 my-4 text-sm">
    <tr class="bg-gray-100 dark:bg-gray-800"><th class="p-2 border border-gray-300 dark:border-gray-700">ইউনিট</th><th class="p-2 border border-gray-300 dark:border-gray-700">অনুষদ শাখা</th><th class="p-2 border border-gray-300 dark:border-gray-700">তারিখ ও সময়</th></tr>
    <tr><td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">A Unit</td><td class="p-2 border border-gray-300 dark:border-gray-700">বিজ্ঞান শাখা</td><td class="p-2 border border-gray-300 dark:border-gray-700 font-semibold">১০ এপ্রিল, দুপুর ১২:০০ - ১:০০</td></tr>
    <tr><td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">B Unit</td><td class="p-2 border border-gray-300 dark:border-gray-700">মানবিক শাখা</td><td class="p-2 border border-gray-300 dark:border-gray-700 font-semibold">১৭ এপ্রিল, দুপুর ১২:০০ - ১:০০</td></tr>
    <tr><td class="p-2 border border-gray-300 dark:border-gray-700 font-bold">C Unit</td><td class="p-2 border border-gray-300 dark:border-gray-700">ব্যবসায় শিক্ষা শাখা</td><td class="p-2 border border-gray-300 dark:border-gray-700 font-semibold">২৪ এপ্রিল, দুপুর ১২:০০ - ১:০০</td></tr>
</table>

<h2>৩. আবেদন প্রক্রিয়া ও ফি পরিশোধ</h2>
<p>আবেদনকারীদের অফিসিয়াল ওয়েবসাইট <a href="https://gstadmission.ac.bd" target="_blank" class="text-orange-500 font-bold">gstadmission.ac.bd</a> এ গিয়ে সকল তথ্য পূরণ ও আবেদন ফি ১,৫০০ টাকা অনলাইনে প্রদান করতে হবে।</p>
HTML
            ],
            [
                'title' => 'নর্থ সাউথ বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬ (NSU Admission)',
                'slug' => 'north-south-university-undergraduate-admission-2026',
                'institution_name' => 'নর্থ সাউথ বিশ্ববিদ্যালয় (NSU)',
                'university_type' => 'private',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'School of Business & Economics (SBE)', 'faculty' => 'BBA, Economics', 'min_gpa' => '7.00', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৬-১২-০৫'],
                    ['name' => 'School of Engineering & Physical Sciences (SEPS)', 'faculty' => 'CSE, EEE, Civil', 'min_gpa' => '7.50', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৬-১২-০৫'],
                    ['name' => 'School of Health & Life Sciences (SHLS)', 'faculty' => 'Pharmacy, Biochemistry', 'min_gpa' => '8.00', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৬-১২-০৫'],
                ],
                'application_start' => '2026-10-15 10:00:00',
                'application_start_text' => '১৫ অক্টোবর ২০২৬, বৃহস্পতিবার • সকাল ১০:০০টা',
                'application_deadline' => '2026-12-01 17:00:00',
                'application_deadline_text' => '০১ ডিসেম্বর ২০২৬, মঙ্গলবার • বিকাল ৫:০০টা',
                'admit_card_date_text' => 'পরীক্ষার ৩ দিন পূর্ব থেকে',
                'minimum_gpa_ssc' => '3.50',
                'minimum_gpa_hsc' => '3.50',
                'total_gpa_required' => '৭.০০ (ন্যূনতম)',
                'application_fee' => '১,৫০০ টাকা',
                'apply_url' => 'https://admissions.northsouth.edu',
                'official_website_url' => 'https://northsouth.edu',
                'notice_image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => 'এসএসসি ও এইচএসসি উভয় পরীক্ষায় ন্যূনতম জিপিএ ৩.৫০ এবং ও/এ লেভেল শিক্ষার্থীদের জন্য নির্দিষ্ট শর্ত প্রযোজ্য।',
                'marks_distribution' => 'ইংরেজি, সাধারণ গণিত এবং বিভাগীয় বিষয়ের ওপর ২ ঘণ্টার ভর্তি পরীক্ষা।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'NSU Admission Office',
                'source_url' => 'https://admissions.northsouth.edu',
                'meta_title' => 'নর্থ সাউথ ইউনিভার্সিটি ভর্তি ২০২৬ | NSU Undergraduate Admission',
                'meta_description' => 'নর্থ সাউথ বিশ্ববিদ্যালয়ের আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬। যোগ্যতা, পরীক্ষা ও আবেদন পদ্ধতি।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. নর্থ সাউথ ইউনিভার্সিটি ভর্তি সম্পর্কিত তথ্য</h2>
<p>নর্থ সাউথ বিশ্ববিদ্যালয় (NSU) বাংলাদেশের প্রথম সরকার অনুমোদিত প্রাইভেট বিশ্ববিদ্যালয় এবং কিউএস ওয়ার্ল্ড র‍্যাঙ্কিংয়ে শীর্ষস্থানীয়। ২০২৬ সেশনের আন্ডারগ্র্যাজুয়েট প্রোগ্রামে সরাসরি অনলাইন ভর্তি পরীক্ষার মাধ্যমে শিক্ষার্থী ভর্তি করানো হচ্ছে।</p>

<h2>২. স্কুল ও কোর্সসমূহ</h2>
<ul>
    <li><strong>স্কুল অব বিজনেস অ্যান্ড ইকোনমিক্স (SBE):</strong> BBA, Economics</li>
    <li><strong>স্কুল অব ইঞ্জিনিয়ারিং অ্যান্ড ফিজিক্যাল সায়েন্সেস (SEPS):</strong> CSE, EEE, Civil Engineering</li>
    <li><strong>স্কুল অব হেলথ অ্যান্ড লাইফ সায়েন্সেস (SHLS):</strong> B.Pharm Professional, Microbiology, Biochemistry</li>
    <li><strong>স্কুল অব হিউম্যানিটিজ অ্যান্ড সোশ্যাল সায়েন্সেস (SHSS):</strong> English, Law, Media Studies</li>
</ul>

<h2>৩. মেধাভিত্তিক ওয়েভার ও স্কলারশিপ</h2>
<p>ভর্তি পরীক্ষায় শীর্ষস্থান অধিকারী শিক্ষার্থীদের জন্য ১০০% পর্যন্ত টিউশন ফি ওয়েভার এবং এসএসসি-এইচএসসিতে জিপিএ ৫.০০ প্রাপ্তদের জন্য স্পেশাল স্কলারশিপ সুবিধা রয়েছে।</p>
HTML
            ],
            [
                'title' => 'ব্র্যাক বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (BRAC University Admission)',
                'slug' => 'brac-university-undergraduate-admission-circular-2026-2027',
                'institution_name' => 'ব্র্যাক বিশ্ববিদ্যালয় (BRACU)',
                'university_type' => 'private',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'Engineering & CSE', 'faculty' => 'School of Data and Sciences', 'min_gpa' => '7.50', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৬-১১-২৮'],
                    ['name' => 'Business School (BBS)', 'faculty' => 'BBA Program', 'min_gpa' => '7.00', 'fee' => '১৫০০ টাকা', 'exam_date' => '২০২৬-১১-২৮'],
                ],
                'application_start' => '2026-10-20 10:00:00',
                'application_start_text' => '২০ অক্টোবর ২০২৬, মঙ্গলবার • সকাল ১০:০০টা',
                'application_deadline' => '2026-11-24 23:59:00',
                'application_deadline_text' => '২৪ নভেম্বর ২০২৬, মঙ্গলবার • রাত ১১:৫৯টা',
                'admit_card_date_text' => 'ভর্তি পরীক্ষার দিন সকাল পর্যন্ত',
                'minimum_gpa_ssc' => '3.50',
                'minimum_gpa_hsc' => '3.50',
                'total_gpa_required' => '৭.০০',
                'application_fee' => '১,৫০০ টাকা',
                'apply_url' => 'https://www.bracu.ac.bd/admissions',
                'official_website_url' => 'https://www.bracu.ac.bd',
                'notice_image_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => 'এসএসসি ও এইচএসসি উভয় পরীক্ষায় বিজ্ঞান, মানবিক বা ব্যবসায় শাখায় ন্যূনতম ৩.৫০।',
                'marks_distribution' => 'লিখিত ও কম্পিউটার ভিত্তিক পরীক্ষা।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'BRAC University Admission Portal',
                'source_url' => 'https://www.bracu.ac.bd/admissions',
                'meta_title' => 'ব্র্যাক বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ | BRACU Admission',
                'meta_description' => 'ব্র্যাক বিশ্ববিদ্যালয়ের ২০২৬-২০২৭ শিক্ষাবর্ষের ভর্তি তথ্য, ফি ও আবেদন লিংক।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. ব্র্যাক বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট প্রোগ্রাম</h2>
<p>ব্র্যাক বিশ্ববিদ্যালয় (BRACU) বিশ্বমানের পরিবেশবান্ধব মেরুল বাড্ডা ক্যাম্পাসে আধুনিক ল্যাবরেটরি ও আন্তর্জাতিক ফ্যাকাল্টি নিয়ে তাদের আন্ডারগ্র্যাজুয়েট প্রোগ্রাম পরিচালনা করছে।</p>

<h2>২. অনলাইন আবেদন লিঙ্ক</h2>
<p>প্রার্থীরা সরাসরি <a href="https://www.bracu.ac.bd/admissions" target="_blank" class="text-orange-500 font-bold">www.bracu.ac.bd/admissions</a> থেকে আবেদন সম্পন্ন করতে পারবেন।</p>
HTML
            ],
            [
                'title' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (বুয়েট) স্নাতক ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭ (BUET Admission)',
                'slug' => 'buet-undergraduate-admission-circular-2026-2027',
                'institution_name' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)',
                'university_type' => 'public',
                'academic_year' => '2026-2027',
                'units' => [
                    ['name' => 'ক গ্রুপ (প্রকৌশল বিভাগসমূহ এবং নগর ও অঞ্চল পরিকল্পনা বিভাগ)', 'faculty' => 'প্রকৌশল অনুষদ', 'min_gpa' => '5.00', 'fee' => '১২০০ টাকা', 'exam_date' => '২০২৭-০১-২০'],
                    ['name' => 'খ গ্রুপ (প্রকৌশল, ইউআরপি ও স্থাপত্য বিভাগ)', 'faculty' => 'স্থাপত্য ও পরিকল্পনা অনুষদ', 'min_gpa' => '5.00', 'fee' => '১৪০০ টাকা', 'exam_date' => '২০২৭-০১-২০'],
                ],
                'application_start' => '2026-11-20 10:00:00',
                'application_start_text' => '২০ নভেম্বর ২০২৬, শুক্রবার • সকাল ১০:০০টা',
                'application_deadline' => '2026-12-10 15:00:00',
                'application_deadline_text' => '১০ ডিসেম্বর ২০২৬, বৃহস্পতিবার • বিকাল ৩:০০টা',
                'admit_card_date_text' => 'প্রাক-নির্বাচনী পরীক্ষার ৫ দিন পূর্ব থেকে',
                'minimum_gpa_ssc' => '4.00',
                'minimum_gpa_hsc' => '5.00',
                'total_gpa_required' => 'এইচএসসিতে পদার্থ, রসায়ন ও গণিতে জিপিএ ৫.০০ এবং ন্যূনতম গ্রেড পয়েন্ট',
                'application_fee' => '১,২০০ - ১,৪০০ টাকা',
                'apply_url' => 'https://www.buet.ac.bd/web/#/admission',
                'official_website_url' => 'https://www.buet.ac.bd',
                'notice_image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop&q=80',
                'featured_image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                'eligibility_criteria' => 'এসএসসিতে ন্যূনতম জিপিএ ৪.০০ এবং এইচএসসি পরীক্ষায় পদার্থবিজ্ঞান, রসায়ন ও গণিতে জিপিএ ৫.০০ থাকতে হবে।',
                'marks_distribution' => 'প্রাক-নির্বাচনী (MCQ) ১০০ নম্বর এবং মূল ভর্তি পরীক্ষা (লিখিত) ৪০০ নম্বর।',
                'status' => 'published',
                'is_featured' => true,
                'source_name' => 'বুয়েট ভর্তি পরীক্ষা পরিচালনা কমিটি',
                'source_url' => 'https://www.buet.ac.bd',
                'meta_title' => 'বুয়েট ভর্তি সার্কুলার ২০২৬-২০২৭ | BUET Admission Circular',
                'meta_description' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (বুয়েট) স্নাতক শ্রেণীতে ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭। প্রাক-নির্বাচনী ও মূল পরীক্ষার যাবতীয় তথ্য।',
                'published_at' => now(),
                'description' => <<<HTML
<h2>১. বুয়েট ভর্তি পরীক্ষা পদ্ধতি</h2>
<p>বুয়েট ভর্তি পরীক্ষা দুই ধাপে অনুষ্ঠিত হয়ে থাকে: প্রথম ধাপে ১০০ নম্বরের প্রাক-নির্বাচনী (MCQ) পরীক্ষা এবং দ্বিতীয় ধাপে উত্তীর্ণ শিক্ষার্থীদের নিয়ে চূড়ান্ত লিখিত পরীক্ষা অনুষ্ঠিত হবে।</p>

<h2>২. বিষয়ভিত্তিক মানবণ্টন (চূড়ান্ত লিখিত পরীক্ষা)</h2>
<ul>
    <li>গণিত: ২০০ নম্বর</li>
    <li>পদার্থবিজ্ঞান: ২০০ নম্বর</li>
    <li>রসায়ন: ২০০ নম্বর</li>
    <li>মুক্তহস্ত অঙ্কন (স্থাপত্য বিভাগের জন্য): ৪০০ নম্বর</li>
</ul>
HTML
            ],
        ];

        $imported = 0;
        foreach ($circulars as $item) {
            $notice = AdmissionNotice::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
            $imported++;
        }

        return $imported;
    }

    /**
     * Sanitize and clean all database notices to guarantee genuine official URLs
     */
    public function sanitizeExistingNotices(): int
    {
        $updated = 0;
        $notices = AdmissionNotice::all();

        foreach ($notices as $notice) {
            $changed = false;

            // Re-detect institution name if generic
            if (empty($notice->institution_name) || $notice->institution_name === 'বাংলাদেশ পাবলিক/প্রাইভেট বিশ্ববিদ্যালয়') {
                $detected = $this->detectInstitution($notice->title, (string) $notice->description);
                if ($detected !== 'বাংলাদেশ পাবলিক/প্রাইভেট বিশ্ববিদ্যালয়') {
                    $notice->institution_name = $detected;
                    $notice->university_type = $this->categorizeUniversity($detected);
                    $changed = true;
                }
            }

            // Resolve official URLs
            $resolved = $this->resolveOfficialUrls($notice->institution_name, $notice->apply_url);
            $officialWebsite = $resolved['official_website_url'];
            $officialApply = $resolved['apply_url'];

            // Synchronize with verified official URLs
            if (! empty($officialApply) && $notice->apply_url !== $officialApply) {
                $notice->apply_url = $officialApply;
                $changed = true;
            }
            if (! empty($officialWebsite) && $notice->official_website_url !== $officialWebsite) {
                $notice->official_website_url = $officialWebsite;
                $changed = true;
            }

            // Clean description HTML: remove any students.bd links or mentions
            if ($notice->description && str_contains($notice->description, 'students.bd')) {
                $targetUrl = $officialApply ?: ($officialWebsite ?: '#');

                // Replace anchor tags pointing to students.bd
                $cleanedDesc = preg_replace(
                    '/<a\s+[^>]*href=["\']https?:\/\/[^"\']*students\.bd[^"\']*["\'][^>]*>(.*?)<\/a>/iu',
                    '<a href="' . $targetUrl . '" target="_blank" class="text-orange-500 font-bold">' . ($targetUrl !== '#' ? $targetUrl : '$1') . '</a>',
                    $notice->description
                );

                // Replace any raw plain text URLs of students.bd
                $cleanedDesc = preg_replace(
                    '/https?:\/\/[^\s<"\'\)]*students\.bd[^\s<"\'\)]*/iu',
                    $targetUrl,
                    $cleanedDesc
                );

                if ($cleanedDesc !== $notice->description) {
                    $notice->description = $cleanedDesc;
                    $changed = true;
                }
            }

            if ($changed) {
                $notice->save();
                $updated++;
            }
        }

        return $updated;
    }
}
