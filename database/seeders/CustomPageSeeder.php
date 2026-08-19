<?php

namespace Database\Seeders;

use App\Models\CustomPage;
use Illuminate\Database\Seeder;

/**
 * NOTE — Terms of Service and Privacy Policy content below is realistic
 * starter/template legal content written to match how a real EdTech SaaS
 * ToS/Privacy Policy is structured (sections, plain-language explanations).
 * It is NOT legal advice and has NOT been reviewed by a lawyer — have a
 * qualified lawyer familiar with Bangladeshi (and, if relevant, any other
 * jurisdiction's) consumer/data-protection law review and adjust this
 * before relying on it in production. Same goes for the FAQ's pricing/
 * refund answers — update them to match your actual policies once set.
 */
class CustomPageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAbout();
        $this->seedHowItWorks();
        $this->seedFaq();
        $this->seedTerms();
        $this->seedPrivacy();
    }

    private function seedAbout(): void
    {
        $contentBn = <<<HTML
        <h2>আমাদের লক্ষ্য</h2>
        <p>Sikhun.com তৈরি হয়েছে একটি সহজ বিশ্বাস থেকে — মানসম্মত শিক্ষা উপকরণ এবং ব্যক্তিগতকৃত সহায়তা পাওয়ার অধিকার প্রতিটি বাংলাদেশী শিক্ষার্থীর থাকা উচিত, তা তারা ঢাকায় থাকুক বা প্রত্যন্ত কোনো জেলায়, সামর্থ্য যাই হোক না কেন।</p>
        <p>আমরা ডিজিটাল বই, কৃত্রিম বুদ্ধিমত্তা এবং শিক্ষাবিজ্ঞানকে একত্র করেছি এমন একটি প্ল্যাটফর্ম তৈরি করতে, যা সত্যিকার অর্থে শিক্ষার্থীদের সাথে কথা বলে — কেবল কনটেন্ট পরিবেশন করে না।</p>

        <h2>আমরা যা প্রদান করি</h2>
        <ul>
            <li><strong>ডিজিটাল লাইব্রেরি</strong> — HSC, SSC, বিশ্ববিদ্যালয় এবং চাকরির প্রস্তুতির জন্য সহজলভ্য মূল্যে বই।</li>
            <li><strong>AI চ্যাট</strong> — যেকোনো বইয়ের বিষয়বস্তু নিয়ে সরাসরি প্রশ্ন করুন এবং তাৎক্ষণিক, প্রাসঙ্গিক উত্তর পান।</li>
            <li><strong>স্বয়ংক্রিয় পরীক্ষা ও ফ্ল্যাশকার্ড</strong> — যেকোনো টপিক থেকে সেকেন্ডের মধ্যে অনুশীলন তৈরি করুন।</li>
            <li><strong>লিডারবোর্ড ও কমিউনিটি</strong> — সারা দেশের শিক্ষার্থীদের সাথে সুস্থ প্রতিযোগিতা।</li>
        </ul>

        <h2>কেন আমরা আলাদা</h2>
        <p>বেশিরভাগ শিক্ষা প্ল্যাটফর্ম হয় শুধু কনটেন্ট বিক্রি করে, নয়তো শুধু প্রযুক্তি দেখায়। আমরা বিশ্বাস করি প্রকৃত মূল্য আসে যখন দুটোই একসাথে কাজ করে — প্রমাণিত পাঠ্যক্রম-ভিত্তিক কনটেন্ট, AI-চালিত ব্যক্তিগতকরণের সাথে মিশে। এবং যেহেতু বাংলাদেশ দ্বিভাষিক একটি দেশ, আমাদের পুরো প্ল্যাটফর্ম বাংলা ও ইংরেজি উভয় ভাষাতেই সম্পূর্ণরূপে কাজ করে।</p>
        HTML;

        $contentEn = <<<HTML
        <h2>Our Mission</h2>
        <p>Sikhun.com was built on a simple belief — every Bangladeshi student deserves access to quality learning material and personalized help, whether they're in Dhaka or a remote district, whatever their budget.</p>
        <p>We combine digital books, artificial intelligence, and learning science into a platform that actually talks back to students — not just serves them content.</p>

        <h2>What We Offer</h2>
        <ul>
            <li><strong>Digital Library</strong> — affordably priced books for HSC, SSC, university, and job-preparation students.</li>
            <li><strong>AI Chat</strong> — ask questions directly about any book's content and get instant, grounded answers.</li>
            <li><strong>Auto-Generated Exams & Flashcards</strong> — turn any topic into practice material in seconds.</li>
            <li><strong>Leaderboard & Community</strong> — healthy competition with students across the country.</li>
        </ul>

        <h2>Why We're Different</h2>
        <p>Most education platforms either sell content or show off technology — rarely both well. We believe real value comes from combining proven, curriculum-aligned content with genuine AI-powered personalization. And because Bangladesh is a bilingual country, our entire platform works fully in both Bengali and English.</p>
        HTML;

        CustomPage::updateOrCreate(['slug' => 'about'], [
            'title_bn' => 'আমাদের সম্পর্কে', 'title_en' => 'About Us',
            'content_bn' => $contentBn, 'content_en' => $contentEn,
            'meta_title_bn' => 'আমাদের সম্পর্কে | Sikhun.com', 'meta_title_en' => 'About Us | Sikhun.com',
            'meta_description_bn' => 'Sikhun.com বাংলাদেশের শিক্ষার্থীদের জন্য একটি AI-চালিত ডিজিটাল লার্নিং প্ল্যাটফর্ম।',
            'meta_description_en' => 'Sikhun.com is an AI-powered digital learning platform built for Bangladeshi students.',
            'title' => 'আমাদের সম্পর্কে', 'content' => $contentBn,
            'meta_title' => 'আমাদের সম্পর্কে | Sikhun.com', 'meta_description' => 'Sikhun.com বাংলাদেশের শিক্ষার্থীদের জন্য একটি AI-চালিত ডিজিটাল লার্নিং প্ল্যাটফর্ম।',
            'is_published' => true,
        ]);
    }

    private function seedHowItWorks(): void
    {
        $contentBn = <<<HTML
        <h2>শুরু করার চেকলিস্ট</h2>
        <ol>
            <li>একটি ফ্রি অ্যাকাউন্ট তৈরি করুন এবং আপনার লেভেল বেছে নিন (SSC/HSC/বিশ্ববিদ্যালয়/চাকরি)।</li>
            <li>লাইব্রেরি থেকে একটি ফ্রি বই দিয়ে শুরু করুন — কোনো পেমেন্ট ছাড়াই।</li>
            <li>বইটি নিয়ে AI চ্যাটে প্রশ্ন করুন — ১০ মিনিট ফ্রি ট্রায়াল অন্তর্ভুক্ত।</li>
            <li>একটি ছোট অনুশীলন পরীক্ষা তৈরি করে নিজের প্রস্তুতি যাচাই করুন।</li>
        </ol>
        <p>আরও AI ফিচার এবং বেশি ব্যবহারের জন্য, একটি সাবস্ক্রিপশন প্ল্যান বেছে নিতে পারেন — প্রতিটি প্ল্যানে ফ্রি বইও অন্তর্ভুক্ত।</p>
        HTML;

        $contentEn = <<<HTML
        <h2>Getting Started Checklist</h2>
        <ol>
            <li>Create a free account and choose your level (SSC/HSC/University/Job).</li>
            <li>Start with a free book from the library — no payment required.</li>
            <li>Ask the AI chat a question about it — 10 minutes of free trial included.</li>
            <li>Generate a short practice exam to check your understanding.</li>
        </ol>
        <p>For more AI features and higher usage limits, pick a subscription plan — every plan includes free gift books too.</p>
        HTML;

        CustomPage::updateOrCreate(['slug' => 'how-it-works'], [
            'title_bn' => 'কিভাবে কাজ করে', 'title_en' => 'How It Works',
            'content_bn' => $contentBn, 'content_en' => $contentEn,
            'meta_title_bn' => 'কিভাবে কাজ করে | Sikhun.com', 'meta_title_en' => 'How It Works | Sikhun.com',
            'meta_description_bn' => 'Sikhun.com কিভাবে ব্যবহার করবেন তা ধাপে ধাপে জানুন।',
            'meta_description_en' => 'Learn how to use Sikhun.com, step by step.',
            'title' => 'কিভাবে কাজ করে', 'content' => $contentBn,
            'meta_title' => 'কিভাবে কাজ করে | Sikhun.com', 'meta_description' => 'Sikhun.com কিভাবে ব্যবহার করবেন তা ধাপে ধাপে জানুন।',
            'is_published' => true,
        ]);
    }

    private function seedFaq(): void
    {
        $contentBn = <<<HTML
        <details>
            <summary>Sikhun.com ব্যবহার করতে কি টাকা লাগে?</summary>
            <p>না, রেজিস্ট্রেশন এবং অনেক বই সম্পূর্ণ ফ্রি। এছাড়া প্রতিটি নতুন শিক্ষার্থী ১০ মিনিট ফ্রি AI ব্যবহারের সুযোগ পান। এরপর আরও ব্যবহারের জন্য সাশ্রয়ী সাবস্ক্রিপশন প্ল্যান রয়েছে।</p>
        </details>
        <details>
            <summary>আমি কি বই কেনার আগে পড়ে দেখতে পারি?</summary>
            <p>হ্যাঁ, প্রতিটি বইয়ের প্রথম কয়েকটি পৃষ্ঠা কেনার আগেই বিনামূল্যে পড়া যায়।</p>
        </details>
        <details>
            <summary>ওয়ালেট রিচার্জ কিভাবে করব?</summary>
            <p>আপনার ওয়ালেট পেজ থেকে কার্ড/মোবাইল ব্যাংকিং (ZiniPay) অথবা ম্যানুয়াল ব্যাংক ট্রান্সফারের মাধ্যমে রিচার্জ করতে পারবেন। ম্যানুয়াল ট্রান্সফার নিশ্চিত হতে কিছুটা সময় লাগতে পারে।</p>
        </details>
        <details>
            <summary>AI কি সবসময় সঠিক উত্তর দেয়?</summary>
            <p>AI সহায়ক হলেও নিখুঁত নয় — গুরুত্বপূর্ণ তথ্যের ক্ষেত্রে সবসময় আপনার পাঠ্যবই বা শিক্ষকের সাথে যাচাই করে নিন।</p>
        </details>
        <details>
            <summary>রেফারেল প্রোগ্রাম কিভাবে কাজ করে?</summary>
            <p>আপনার প্রোফাইল থেকে একটি রেফারেল লিংক পাবেন। বন্ধু সেই লিংক দিয়ে রেজিস্ট্রেশন করে প্রথম পেমেন্ট করলে আপনারা দুজনেই ওয়ালেটে বোনাস পাবেন।</p>
        </details>
        <details>
            <summary>আমি কি সাবস্ক্রিপশন বাতিল করতে পারি?</summary>
            <p>হ্যাঁ, যেকোনো সময় ভবিষ্যতে নবায়ন বন্ধ করতে পারবেন। বর্তমান মেয়াদ শেষ না হওয়া পর্যন্ত সব সুবিধা চালু থাকবে।</p>
        </details>
        <details>
            <summary>আমার তথ্য কি নিরাপদ?</summary>
            <p>হ্যাঁ। বিস্তারিত জানতে আমাদের <a href="/p/privacy">গোপনীয়তা নীতি</a> পড়ুন।</p>
        </details>
        <details>
            <summary>আরও সাহায্য দরকার?</summary>
            <p>পেজের নিচে-ডানদিকে থাকা চ্যাট বাটনে ক্লিক করুন, অথবা আমাদের <a href="/contact">যোগাযোগ ফর্মে</a> বার্তা পাঠান।</p>
        </details>
        HTML;

        $contentEn = <<<HTML
        <details>
            <summary>Does it cost anything to use Sikhun.com?</summary>
            <p>No — registration and many books are completely free. Every new student also gets 10 minutes of free AI usage. Subscription plans are available for heavier use afterward.</p>
        </details>
        <details>
            <summary>Can I preview a book before buying it?</summary>
            <p>Yes, the first few pages of every book are readable for free before purchase.</p>
        </details>
        <details>
            <summary>How do I recharge my wallet?</summary>
            <p>From your Wallet page, via card/mobile banking (ZiniPay) or manual bank transfer. Manual transfers may take a little time to confirm.</p>
        </details>
        <details>
            <summary>Is the AI always accurate?</summary>
            <p>AI is helpful but not perfect — always double-check important information against your textbook or teacher.</p>
        </details>
        <details>
            <summary>How does the referral program work?</summary>
            <p>You'll find a referral link on your profile. When a friend registers with it and makes their first purchase, you both get a wallet bonus.</p>
        </details>
        <details>
            <summary>Can I cancel my subscription?</summary>
            <p>Yes, you can stop future renewals at any time. All benefits stay active until your current period ends.</p>
        </details>
        <details>
            <summary>Is my data safe?</summary>
            <p>Yes. Read our <a href="/p/privacy">Privacy Policy</a> for details.</p>
        </details>
        <details>
            <summary>Need more help?</summary>
            <p>Click the chat button in the bottom-right of any page, or send a message via our <a href="/contact">contact form</a>.</p>
        </details>
        HTML;

        CustomPage::updateOrCreate(['slug' => 'faq'], [
            'title_bn' => 'সাধারণ প্রশ্নোত্তর', 'title_en' => 'Frequently Asked Questions',
            'content_bn' => $contentBn, 'content_en' => $contentEn,
            'meta_title_bn' => 'সাধারণ প্রশ্নোত্তর | Sikhun.com', 'meta_title_en' => 'FAQ | Sikhun.com',
            'meta_description_bn' => 'Sikhun.com সম্পর্কিত সচরাচর জিজ্ঞাসিত প্রশ্নের উত্তর।',
            'meta_description_en' => 'Answers to frequently asked questions about Sikhun.com.',
            'title' => 'সাধারণ প্রশ্নোত্তর', 'content' => $contentBn,
            'meta_title' => 'সাধারণ প্রশ্নোত্তর | Sikhun.com', 'meta_description' => 'Sikhun.com সম্পর্কিত সচরাচর জিজ্ঞাসিত প্রশ্নের উত্তর।',
            'is_published' => true,
        ]);
    }

    private function seedTerms(): void
    {
        $contentBn = <<<HTML
        <h3>১. শর্তাবলী গ্রহণ</h3>
        <p>Sikhun.com ব্যবহার করার মাধ্যমে আপনি এই শর্তাবলীতে সম্মত হচ্ছেন। আপনি সম্মত না হলে অনুগ্রহ করে প্ল্যাটফর্মটি ব্যবহার করবেন না।</p>
        <h3>২. অ্যাকাউন্ট রেজিস্ট্রেশন</h3>
        <p>নির্ভুল তথ্য দিয়ে অ্যাকাউন্ট তৈরি করতে হবে এবং আপনার পাসওয়ার্ডের নিরাপত্তার দায়িত্ব আপনার।</p>
        <h3>৩. সাবস্ক্রিপশন ও পেমেন্ট</h3>
        <p>সাবস্ক্রিপশন প্ল্যান স্বয়ংক্রিয়ভাবে নবায়ন হয় না — প্রতিবার আপনাকে সক্রিয়ভাবে নবায়ন বা পুনরায় সাবস্ক্রাইব করতে হবে। ওয়ালেট ব্যালেন্স ফেরতযোগ্য নয়, তবে গ্রাহক সেবার মাধ্যমে ব্যতিক্রমী পরিস্থিতিতে বিবেচনা করা হতে পারে।</p>
        <h3>৪. কনটেন্ট ও মেধাস্বত্ব</h3>
        <p>প্ল্যাটফর্মের সব বই, কোর্স এবং কনটেন্ট মেধাস্বত্ব আইনে সুরক্ষিত। ডাউনলোড, স্ক্রিনশট বা পুনর্বিতরণ কঠোরভাবে নিষিদ্ধ।</p>
        <h3>৫. গ্রহণযোগ্য ব্যবহার</h3>
        <p>প্ল্যাটফর্ম অপব্যবহার (স্ক্র্যাপিং, একাধিক ভুয়া অ্যাকাউন্ট, পরীক্ষায় অসততা ইত্যাদি) সনাক্ত হলে অ্যাকাউন্ট স্থগিত করা হতে পারে।</p>
        <h3>৬. AI-জেনারেটেড কনটেন্ট সম্পর্কে</h3>
        <p>AI চ্যাট, পরীক্ষা, ফ্ল্যাশকার্ড ও প্রবন্ধ মূল্যায়ন কৃত্রিম বুদ্ধিমত্তা দ্বারা তৈরি এবং এতে ত্রুটি থাকতে পারে। এগুলো শিক্ষকের বিকল্প নয়।</p>
        <h3>৭. অ্যাকাউন্ট বাতিলকরণ</h3>
        <p>শর্তাবলী লঙ্ঘনের ক্ষেত্রে আমরা যেকোনো সময় অ্যাকাউন্ট স্থগিত বা বাতিল করার অধিকার রাখি।</p>
        <h3>৮. দায়বদ্ধতার সীমাবদ্ধতা</h3>
        <p>আইন অনুমোদিত সর্বোচ্চ পরিমাণে, Sikhun.com পরোক্ষ বা পরিণামগত ক্ষতির জন্য দায়বদ্ধ থাকবে না।</p>
        <h3>৯. শর্তাবলীর পরিবর্তন</h3>
        <p>আমরা সময়ে সময়ে এই শর্তাবলী পরিবর্তন করতে পারি। উল্লেখযোগ্য পরিবর্তনের ক্ষেত্রে আপনাকে অবহিত করা হবে।</p>
        <h3>১০. যোগাযোগ</h3>
        <p>প্রশ্ন থাকলে আমাদের <a href="/contact">যোগাযোগ ফর্মে</a> বার্তা পাঠান।</p>
        HTML;

        $contentEn = <<<HTML
        <h3>1. Acceptance of Terms</h3>
        <p>By using Sikhun.com, you agree to these terms. If you do not agree, please do not use the platform.</p>
        <h3>2. Account Registration</h3>
        <p>You must register with accurate information and are responsible for keeping your password secure.</p>
        <h3>3. Subscriptions & Payments</h3>
        <p>Subscription plans do not auto-renew — you must actively renew or resubscribe each time. Wallet balances are non-refundable, though exceptional circumstances may be considered via customer support.</p>
        <h3>4. Content & Intellectual Property</h3>
        <p>All books, courses, and content on the platform are protected by copyright. Downloading, screenshotting, or redistributing content is strictly prohibited.</p>
        <h3>5. Acceptable Use</h3>
        <p>Accounts found abusing the platform (scraping, multiple fake accounts, exam dishonesty, etc) may be suspended.</p>
        <h3>6. About AI-Generated Content</h3>
        <p>AI chat, exams, flashcards, and essay grading are generated by artificial intelligence and may contain errors. They are not a substitute for a teacher.</p>
        <h3>7. Account Termination</h3>
        <p>We reserve the right to suspend or terminate accounts at any time for violations of these terms.</p>
        <h3>8. Limitation of Liability</h3>
        <p>To the maximum extent permitted by law, Sikhun.com is not liable for indirect or consequential damages.</p>
        <h3>9. Changes to These Terms</h3>
        <p>We may update these terms from time to time. You will be notified of material changes.</p>
        <h3>10. Contact</h3>
        <p>Questions? Reach out via our <a href="/contact">contact form</a>.</p>
        HTML;

        CustomPage::updateOrCreate(['slug' => 'terms'], [
            'title_bn' => 'ব্যবহারের শর্তাবলী', 'title_en' => 'Terms of Service',
            'content_bn' => $contentBn, 'content_en' => $contentEn,
            'meta_title_bn' => 'ব্যবহারের শর্তাবলী | Sikhun.com', 'meta_title_en' => 'Terms of Service | Sikhun.com',
            'meta_description_bn' => 'Sikhun.com ব্যবহারের শর্তাবলী।', 'meta_description_en' => 'Terms of Service for using Sikhun.com.',
            'title' => 'ব্যবহারের শর্তাবলী', 'content' => $contentBn,
            'meta_title' => 'ব্যবহারের শর্তাবলী | Sikhun.com', 'meta_description' => 'Sikhun.com ব্যবহারের শর্তাবলী।',
            'is_published' => true,
        ]);
    }

    private function seedPrivacy(): void
    {
        $contentBn = <<<HTML
        <h3>১. আমরা যে তথ্য সংগ্রহ করি</h3>
        <p>নাম, ইমেইল, শিক্ষার্থীর ধরন, পেমেন্ট লেনদেনের তথ্য, এবং প্ল্যাটফর্ম ব্যবহারের তথ্য (পঠিত বই, পরীক্ষার ফলাফল ইত্যাদি)।</p>
        <h3>২. তথ্যের ব্যবহার</h3>
        <p>সেবা প্রদান, ব্যক্তিগতকৃত সুপারিশ, লেনদেন প্রক্রিয়াকরণ এবং প্ল্যাটফর্ম উন্নয়নে আমরা এই তথ্য ব্যবহার করি।</p>
        <h3>৩. তথ্য শেয়ারিং</h3>
        <p>আমরা আপনার তথ্য বিক্রি করি না। পেমেন্ট প্রসেসর (ZiniPay) এবং AI প্রোভাইডারদের (যেমন OpenAI) সাথে শুধুমাত্র সেবা প্রদানের জন্য প্রয়োজনীয় তথ্য শেয়ার করা হয়।</p>
        <h3>৪. কুকিজ</h3>
        <p>সেশন ও ভাষা পছন্দ মনে রাখতে আমরা প্রয়োজনীয় কুকিজ ব্যবহার করি — কোনো তৃতীয় পক্ষের বিজ্ঞাপন ট্র্যাকিং কুকিজ নেই।</p>
        <h3>৫. তথ্য সংরক্ষণ</h3>
        <p>আপনার অ্যাকাউন্ট সক্রিয় থাকাকালীন আমরা তথ্য সংরক্ষণ করি। অ্যাকাউন্ট মুছে ফেলার অনুরোধ করলে যুক্তিসঙ্গত সময়ের মধ্যে তা কার্যকর করা হবে।</p>
        <h3>৬. আপনার অধিকার</h3>
        <p>আপনি আপনার তথ্য দেখা, সংশোধন বা মুছে ফেলার অনুরোধ করতে পারেন — আমাদের <a href="/contact">যোগাযোগ ফর্মে</a> যোগাযোগ করুন।</p>
        <h3>৭. শিশুদের গোপনীয়তা</h3>
        <p>আমাদের সেবা শিক্ষার্থীদের জন্য, তবে ১৩ বছরের কম বয়সীদের ক্ষেত্রে অভিভাবকের সম্মতি প্রয়োজন।</p>
        <h3>৮. নিরাপত্তা</h3>
        <p>পাসওয়ার্ড এনক্রিপ্ট করা থাকে এবং সংবেদনশীল তথ্য (যেমন AI API কী) নিরাপদভাবে সংরক্ষিত হয়। তবে কোনো সিস্টেমই ১০০% নিরাপদ নয়।</p>
        <h3>৯. নীতিমালার পরিবর্তন</h3>
        <p>এই নীতিমালা সময়ে সময়ে হালনাগাদ হতে পারে। উল্লেখযোগ্য পরিবর্তনে আপনাকে অবহিত করা হবে।</p>
        <h3>১০. যোগাযোগ</h3>
        <p>গোপনীয়তা সম্পর্কিত যেকোনো প্রশ্নে আমাদের <a href="/contact">যোগাযোগ ফর্মে</a> বার্তা পাঠান।</p>
        HTML;

        $contentEn = <<<HTML
        <h3>1. Information We Collect</h3>
        <p>Name, email, student type, payment transaction details, and platform usage data (books read, exam results, etc).</p>
        <h3>2. How We Use Information</h3>
        <p>We use this data to provide the service, personalize recommendations, process transactions, and improve the platform.</p>
        <h3>3. Data Sharing</h3>
        <p>We do not sell your data. Information is shared with payment processors (ZiniPay) and AI providers (e.g. OpenAI) only as needed to deliver the service.</p>
        <h3>4. Cookies</h3>
        <p>We use essential cookies to remember your session and language preference — no third-party advertising trackers.</p>
        <h3>5. Data Retention</h3>
        <p>We retain data while your account is active. Account deletion requests are honored within a reasonable timeframe.</p>
        <h3>6. Your Rights</h3>
        <p>You may request to view, correct, or delete your data — reach out via our <a href="/contact">contact form</a>.</p>
        <h3>7. Children's Privacy</h3>
        <p>Our service is for students; users under 13 require parental consent.</p>
        <h3>8. Security</h3>
        <p>Passwords are encrypted and sensitive data (like AI API keys) is stored securely. No system is 100% secure, however.</p>
        <h3>9. Changes to This Policy</h3>
        <p>This policy may be updated from time to time. You will be notified of material changes.</p>
        <h3>10. Contact</h3>
        <p>For any privacy-related questions, reach out via our <a href="/contact">contact form</a>.</p>
        HTML;

        CustomPage::updateOrCreate(['slug' => 'privacy'], [
            'title_bn' => 'গোপনীয়তা নীতি', 'title_en' => 'Privacy Policy',
            'content_bn' => $contentBn, 'content_en' => $contentEn,
            'meta_title_bn' => 'গোপনীয়তা নীতি | Sikhun.com', 'meta_title_en' => 'Privacy Policy | Sikhun.com',
            'meta_description_bn' => 'Sikhun.com এর গোপনীয়তা নীতি।', 'meta_description_en' => 'Privacy Policy for Sikhun.com.',
            'title' => 'গোপনীয়তা নীতি', 'content' => $contentBn,
            'meta_title' => 'গোপনীয়তা নীতি | Sikhun.com', 'meta_description' => 'Sikhun.com এর গোপনীয়তা নীতি।',
            'is_published' => true,
        ]);
    }
}
