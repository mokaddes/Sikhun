<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogSource;
use App\Models\BlogTag;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'science-space', 'name_bn' => 'বিজ্ঞান ও মহাকাশ', 'name_en' => 'Science & Space', 'sort_order' => 1],
            ['slug' => 'artificial-intelligence', 'name_bn' => 'কৃত্রিম বুদ্ধিমত্তা (AI)', 'name_en' => 'Artificial Intelligence', 'sort_order' => 2],
            ['slug' => 'technology-programming', 'name_bn' => 'প্রযুক্তি ও প্রোগ্রামিং', 'name_en' => 'Technology & Programming', 'sort_order' => 3],
            ['slug' => 'academic-career', 'name_bn' => 'একাডেমিক ও পরীক্ষা প্রস্তুতি', 'name_en' => 'Academic & Exam Prep', 'sort_order' => 4],
            ['slug' => 'research-innovation', 'name_bn' => 'গবেষণা ও উদ্ভাবন', 'name_en' => 'Research & Innovation', 'sort_order' => 5],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $model = BlogCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name_bn' => $cat['name_bn'],
                    'name_en' => $cat['name_en'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                ]
            );
            $catMap[$cat['slug']] = $model->id;
        }

        $tags = [
            ['slug' => 'hsc', 'name_bn' => 'এইচএসসি', 'name_en' => 'HSC'],
            ['slug' => 'ssc', 'name_bn' => 'এসএসসি', 'name_en' => 'SSC'],
            ['slug' => 'admission', 'name_bn' => 'ভর্তি প্রস্তুতি', 'name_en' => 'Admission Prep'],
            ['slug' => 'career', 'name_bn' => 'ক্যারিয়ার', 'name_en' => 'Career'],
            ['slug' => 'study-tips', 'name_bn' => 'পড়াশোনার কৌশল', 'name_en' => 'Study Tips'],
            ['slug' => 'exam-preparation', 'name_bn' => 'পরীক্ষার প্রস্তুতি', 'name_en' => 'Exam Preparation'],
            ['slug' => 'mathematics', 'name_bn' => 'গণিত', 'name_en' => 'Mathematics'],
            ['slug' => 'physics', 'name_bn' => 'পদার্থবিজ্ঞান', 'name_en' => 'Physics'],
            ['slug' => 'chemistry', 'name_bn' => 'রসায়ন', 'name_en' => 'Chemistry'],
            ['slug' => 'biology', 'name_bn' => 'জীববিজ্ঞান', 'name_en' => 'Biology'],
            ['slug' => 'english', 'name_bn' => 'ইংরেজি', 'name_en' => 'English'],
            ['slug' => 'bangladesh', 'name_bn' => 'বাংলাদেশ', 'name_en' => 'Bangladesh'],
            ['slug' => 'artificial-intelligence', 'name_bn' => 'কৃত্রিম বুদ্ধিমত্তা', 'name_en' => 'Artificial Intelligence'],
            ['slug' => 'technology', 'name_bn' => 'প্রযুক্তি', 'name_en' => 'Technology'],
            ['slug' => 'programming', 'name_bn' => 'প্রোগ্রামিং', 'name_en' => 'Programming'],
            ['slug' => 'science', 'name_bn' => 'বিজ্ঞান', 'name_en' => 'Science'],
            ['slug' => 'space', 'name_bn' => 'মহাকাশ', 'name_en' => 'Space'],
            ['slug' => 'research', 'name_bn' => 'গবেষণা', 'name_en' => 'Research'],
            ['slug' => 'innovation', 'name_bn' => 'উদ্ভাবন', 'name_en' => 'Innovation'],
            ['slug' => 'digital-learning', 'name_bn' => 'ডিজিটাল শিক্ষা', 'name_en' => 'Digital Learning'],
            ['slug' => 'job-search', 'name_bn' => 'চাকরি খোঁজা', 'name_en' => 'Job Search'],
            ['slug' => 'automation', 'name_bn' => 'অটোমেশন', 'name_en' => 'Automation'],
            ['slug' => 'manus-ai', 'name_bn' => 'Manus AI', 'name_en' => 'Manus AI'],
        ];

        foreach ($tags as $tag) {
            BlogTag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }

        $sources = [
            [
                'name' => 'NASA Breaking News & Science',
                'url' => 'https://www.nasa.gov/feed/',
                'type' => 'rss',
                'default_category_id' => $catMap['science-space'],
                'max_per_run' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Hugging Face AI Blog',
                'url' => 'https://huggingface.co/blog/feed.xml',
                'type' => 'rss',
                'default_category_id' => $catMap['artificial-intelligence'],
                'max_per_run' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Google AI Blog',
                'url' => 'https://blog.google/technology/ai/rss/',
                'type' => 'rss',
                'default_category_id' => $catMap['artificial-intelligence'],
                'max_per_run' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'arXiv Artificial Intelligence (cs.AI)',
                'url' => 'https://rss.arxiv.org/rss/cs.AI',
                'type' => 'rss',
                'default_category_id' => $catMap['research-innovation'],
                'max_per_run' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'freeCodeCamp Programming & Tech',
                'url' => 'https://www.freecodecamp.org/news/rss/',
                'type' => 'rss',
                'default_category_id' => $catMap['technology-programming'],
                'max_per_run' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sources as $src) {
            BlogSource::updateOrCreate(
                ['url' => $src['url']],
                $src
            );
        }

        $tagMap = BlogTag::whereIn('slug', array_column($tags, 'slug'))->pluck('id', 'slug');
        $posts = [
            [
                'slug' => 'hsc-study-plan-last-90-days',
                'title' => 'এইচএসসি পরীক্ষার শেষ ৯০ দিনের বাস্তবসম্মত স্টাডি প্ল্যান',
                'title_en' => 'A Practical 90-Day Study Plan for HSC Candidates',
                'excerpt' => 'সিলেবাস শেষ করা, রিভিশন এবং মডেল টেস্ট—শেষ তিন মাসে কীভাবে অগ্রাধিকার ঠিক করবেন।',
                'content' => '<h2>শেষ ৯০ দিনের লক্ষ্য</h2><p>শেষ সময়ে নতুন বইয়ের সংখ্যা বাড়ানোর বদলে বোর্ড বই, ক্লাস নোট এবং ভুলের খাতা—এই তিনটি উৎসকে কেন্দ্র করে পড়ুন। প্রতিটি অধ্যায়ের জন্য ধারণা, অনুশীলন এবং পুনরাবৃত্তির আলাদা সময় রাখুন।</p><h3>সাপ্তাহিক ছক</h3><ul><li>চার দিন অধ্যায়ভিত্তিক পড়া ও অনুশীলন</li><li>এক দিন দুর্বল অধ্যায় পুনরাবৃত্তি</li><li>এক দিন পূর্ণাঙ্গ মডেল টেস্ট</li><li>এক দিন ভুল বিশ্লেষণ ও বিশ্রাম</li></ul><p>প্রতিটি মডেল টেস্টের পরে ভুলের কারণ লিখুন—ধারণাগত ভুল, অসাবধানতা নাকি সময় ব্যবস্থাপনা। এই তালিকাই শেষ মাসের রিভিশন গাইড হবে।</p>',
                'category' => 'academic-career',
                'tags' => ['hsc', 'study-tips', 'exam-preparation'],
            ],
            [
                'slug' => 'ssc-mathematics-common-mistakes',
                'title' => 'এসএসসি গণিতে নম্বর কমে যাওয়ার ৭টি সাধারণ কারণ',
                'title_en' => '7 Common Reasons SSC Students Lose Marks in Mathematics',
                'excerpt' => 'সূত্র জানা থাকা সত্ত্বেও কোথায় নম্বর কাটা যায় এবং কীভাবে ধাপে ধাপে সমাধান লিখবেন।',
                'content' => '<h2>শুধু উত্তর নয়, সমাধানের ধাপও গুরুত্বপূর্ণ</h2><p>এসএসসি গণিতে অনেক নম্বর কমে যায় প্রশ্ন না বুঝে সূত্র প্রয়োগ, একক না লেখা এবং শেষ লাইনে উত্তর স্পষ্ট না করার কারণে। প্রতিটি সমাধানে প্রদত্ত তথ্য, সূত্র, বসানো এবং চূড়ান্ত উত্তর আলাদা করে লিখুন।</p><h3>ভুল কমানোর কৌশল</h3><ol><li>প্রশ্নে কী চাওয়া হয়েছে তা দাগ দিন।</li><li>চিত্র বা ডেটা থাকলে আলাদা করে লিখুন।</li><li>শেষ পাঁচ মিনিট শুধু হিসাব ও একক যাচাই করুন।</li></ol><p>প্রতিদিন অল্প সংখ্যক প্রশ্ন নিয়মিত সমাধান করলে গতি ও নির্ভুলতা দুটোই বাড়ে।</p>',
                'category' => 'academic-career',
                'tags' => ['ssc', 'mathematics', 'exam-preparation'],
            ],
            [
                'slug' => 'university-admission-revision-strategy',
                'title' => 'বিশ্ববিদ্যালয় ভর্তি পরীক্ষার জন্য স্মার্ট রিভিশন কৌশল',
                'title_en' => 'Smart Revision Strategies for University Admission Tests',
                'excerpt' => 'বিষয়ভিত্তিক ভুল বিশ্লেষণ, দ্রুত MCQ অনুশীলন এবং সময় ব্যবস্থাপনার সমন্বিত পদ্ধতি।',
                'content' => '<h2>ভর্তি প্রস্তুতিতে ভুলের ডেটা ব্যবহার করুন</h2><p>ভর্তি পরীক্ষার প্রস্তুতিতে শুধু বেশি প্রশ্ন সমাধান করলেই হবে না। প্রতিটি মডেল টেস্টের পরে ভুল প্রশ্নগুলো বিষয়, অধ্যায় এবং ভুলের ধরন অনুযায়ী ভাগ করুন।</p><h3>তিন ধাপের রিভিশন</h3><ul><li>প্রথম ধাপে সূত্র ও সংজ্ঞার দ্রুত পুনরাবৃত্তি</li><li>দ্বিতীয় ধাপে সময় বেঁধে MCQ অনুশীলন</li><li>তৃতীয় ধাপে ভুল প্রশ্ন আবার সমাধান</li></ul><p>একই ধরনের ভুল দ্বিতীয়বার না করার জন্য সপ্তাহে অন্তত একবার error log পর্যালোচনা করুন।</p>',
                'category' => 'academic-career',
                'tags' => ['admission', 'study-tips', 'exam-preparation'],
            ],
            [
                'slug' => 'manus-ai-job-search-automation',
                'title' => 'Manus AI দিয়ে অটোমেটিক চাকরি খোঁজা: CV match, job analysis ও safe apply গাইড',
                'title_en' => 'Automate Your Job Search with Manus AI: CV Matching, Job Analysis and Safe Apply Guide',
                'excerpt' => 'ফ্রি Manus AI ব্যবহার করে CV থেকে job title বের করা, Bdjobs ও অন্যান্য platform-এর job description বিশ্লেষণ, transparent matching score এবং ৫০%+ match হলে review-first application workflow—ধাপে ধাপে।',
                'content' => '<figure><img src="/images/blog/blog-manus-job-automation-hero.png" alt="Manus AI দিয়ে CV থেকে চাকরি খোঁজার workflow" loading="lazy"><figcaption>CV → job title → match score → human-approved application—একটি review-first workflow।</figcaption></figure><h2>চাকরি খোঁজার কাজটাই এবার অটোমেট করুন</h2><p>প্রতিদিন একই CV দিয়ে অনেক job পোস্ট দেখা, requirement কপি করা এবং কোনটিতে apply করবেন তা ঠিক করা সময়সাপেক্ষ। Manus AI-কে একটি সহকারী হিসেবে ব্যবহার করলে এই repetitive কাজগুলোকে ধাপে ভাগ করা যায়। তবে লক্ষ্য হওয়া উচিত <strong>blind auto-apply নয়</strong>; বরং CV বুঝে relevant job shortlist, evidence-based match score এবং আপনার অনুমতির পরে application draft তৈরি করা।</p><p>বর্তমান Manus Help Center অনুযায়ী নতুন free user registration-এ ১,০০০ credit দেওয়া হয় এবং প্রতিদিন ৩০০ daily credit refresh হয়। Daily credit সেদিনের মধ্যে ব্যবহার করতে হয়, Free user-এর daily-refresh ব্যবহারে মাসে ১,৫০০ credit limit আছে, এবং এই daily credit Manus 2.0 Lite-এ ব্যবহারযোগ্য। নিয়ম ও সুবিধা বদলাতে পারে, তাই signup-এর সময় account-এর credit panel-ও দেখে নিন। <a href="https://help.manus.im/en/articles/11813668-are-there-any-discounts-or-special-offers" rel="nofollow noopener" target="_blank">[অফিসিয়াল signup credit তথ্য]</a> <a href="https://help.manus.im/en/articles/11985189-what-should-i-do-if-my-daily-refresh-credits-have-not-refreshed" rel="nofollow noopener" target="_blank">[daily credit নিয়ম]</a></p><h2>এক নজরে পুরো workflow</h2><ul><li><strong>Step 1:</strong> CV upload করে ১০–২০টি সম্ভাব্য job title বের করুন।</li><li><strong>Step 2:</strong> Bdjobs, LinkedIn বা অন্য platform-এর job description পড়ে requirement ও match score হিসাব করুন।</li><li><strong>Step 3:</strong> ৫০% বা তার বেশি match হলে job-specific CV/cover letter draft করুন এবং final submit-এর আগে নিজে review করুন।</li></ul><figure><img src="/images/blog/blog-manus-job-automation-step1.png" alt="Manus AI-তে CV upload করে job title shortlist করার ধাপ" loading="lazy"><figcaption>Step 1: CV থেকে বাস্তব skill ও experience দেখে job title shortlist।</figcaption></figure><h2>Step 1: CV upload করে ১০–২০টি job title বের করুন</h2><p>প্রথমে CV-এর PDF বা DOCX upload করুন। CV-তে phone number, NID, password, bank তথ্য বা অপ্রয়োজনীয় ব্যক্তিগত তথ্য থাকলে আগে বাদ দিন। Manus-কে বলুন—আপনার CV-তে যা নেই তা যেন অনুমান না করে এবং title-এর সঙ্গে seniority, location ও industry-ও দেখায়।</p><div class="callout"><strong>Demo prompt:</strong><br>“এই CV বিশ্লেষণ করে বাংলাদেশে আমার জন্য সবচেয়ে উপযুক্ত ১০–২০টি job title দাও। প্রতিটির জন্য seniority, required skills, সম্ভাব্য industry, location এবং কেন match করে—সংক্ষেপে লিখো। CV-তে নেই এমন skill বানিয়ে লিখবে না। শেষে must-have skill এবং আমার gap আলাদা করে দাও।”</div><p>Output-টি একটি priority list হিসেবে নিন। উদাহরণ: Junior Data Analyst, MIS Executive, Business Analyst Intern, Digital Marketing Executive। একই title-এর ভিন্ন seniority হলে আলাদা করে দেখান, যাতে ভুল level-এর job-এ সময় নষ্ট না হয়।</p><figure><img src="/images/blog/blog-manus-job-automation-step2.png" alt="Job description থেকে transparent match score হিসাবের উদাহরণ" loading="lazy"><figcaption>Step 2: score-এর সঙ্গে gap report রাখুন—শুধু একটি percentage যথেষ্ট নয়।</figcaption></figure><h2>Step 2: job description পড়ে matching calculation করুন</h2><p>প্রতিটি job-এর title, responsibilities, must-have skills, experience, education, location, deadline এবং official application link সংগ্রহ করুন। Manus-কে পুরো description দিন বা আপনি যে অংশ ব্যবহার করার অনুমতি রাখেন সেটিই দিন। তারপর একটি transparent rubric চাইুন। একটি উদাহরণ হতে পারে: skill ৪০%, experience ৩০%, education ১৫%, location ও notice period ১৫%। এটি industry-standard কোনো fixed formula নয়; আপনার career লক্ষ্য অনুযায়ী rubric বদলাতে পারবেন।</p><p>ধরা যাক score ৭৮% এসেছে। এর মানে আপনি job-এর জন্য উপযুক্ত হতে পারেন, কিন্তু Power BI project evidence বা নির্দিষ্ট domain experience কম হতে পারে। তাই score-এর পাশে <strong>কেন match</strong>, <strong>কোন requirement missing</strong> এবং <strong>কীভাবে gap পূরণ করবেন</strong>—এই তিনটি অংশ চাইুন।</p><div class="callout"><strong>Demo prompt:</strong><br>“এই job description ও আমার CV তুলনা করে ০–১০০ match score দাও। Skill ৪০%, experience ৩০%, education ১৫%, location/notice period ১৫% rubric ব্যবহার করো। প্রতিটি অংশের score, evidence, missing requirement এবং CV-তে কী যোগ করা যায় লিখো। অনুমান করে কোনো তথ্য যোগ করবে না।”</div><h2>কোন platform-এ কীভাবে সাবধান থাকবেন</h2><p><strong>Bdjobs:</strong> official job page, employer, deadline ও application instruction যাচাই করুন। Bdjobs নিজেও প্রকাশিত job information-এর দায়িত্ব নিয়োগকারী প্রতিষ্ঠানের বলে এবং চাকরিপ্রার্থীদের সতর্ক থাকতে বলে। অগ্রিম টাকা, OTP, password বা অপ্রয়োজনীয় financial information চাইলে থামুন। <a href="https://bdjobs.com/tos.asp" rel="nofollow noopener" target="_blank">Bdjobs Terms & Conditions</a> এবং job page-এর instruction অনুসরণ করুন।</p><p><strong>LinkedIn:</strong> LinkedIn User Agreement automated scraping, bots, unauthorized automated access এবং inauthentic engagement নিষিদ্ধ করে। তাই LinkedIn-এ bot দিয়ে profile scrape, mass-message বা blind auto-apply করবেন না। Search result দেখে official LinkedIn workflow-এ নিজে খুলে job details পড়ুন, অথবা employer-এর official career page-এ যান। <a href="https://www.linkedin.com/legal/user-agreement" rel="nofollow noopener" target="_blank">LinkedIn User Agreement, section 8.2</a></p><figure><img src="/images/blog/blog-manus-job-automation-step3.png" alt="৫০ শতাংশের বেশি match হলে human-approved safe apply workflow" loading="lazy"><figcaption>Step 3: ৫০%+ match হলে draft তৈরি করুন; final submit মানুষের অনুমোদনে।</figcaption></figure><h2>Step 3: ৫০%+ match হলে safe apply workflow</h2><p>৫০% match একটি screening threshold—চাকরি পাওয়ার নিশ্চয়তা নয়। Score threshold পার হলে Manus-কে job-specific CV version, cover letter এবং form-এর answer draft করতে বলুন। Salary expectation, notice period, work authorization, portfolio link বা years of experience-এর মতো field কখনো অনুমান করতে দেবেন না।</p><div class="callout"><strong>Safe apply prompt:</strong><br>“শুধু ৫০% বা তার বেশি match হওয়া job-এর জন্য application draft তৈরি করো। কোনো তথ্য অনুমান করবে না। Required field, salary expectation, notice period, attachment ও employer প্রশ্নের উত্তর আমাকে দেখিয়ে final submit-এর আগে আমার অনুমতি নাও। LinkedIn-এ bot বা scraping করো না; platform-এর official workflow অনুসরণ করো।”</div><ol><li>প্রথমে job link, employer ও deadline আবার যাচাই করুন।</li><li>CV version-এ শুধু সত্য তথ্য রাখুন; keyword stuffing করবেন না।</li><li>Cover letter-এ job description-এর সঙ্গে মেলে এমন বাস্তব evidence দিন।</li><li>প্রতিটি required field নিজে review করে final submit করুন।</li><li>Applied date, job link, version এবং follow-up date একটি tracker-এ রাখুন।</li></ol><h2>১০ মিনিটের daily routine</h2><ul><li><strong>২ মিনিট:</strong> Manus-এ credit ও task budget দেখে নিন। বড় browser task একসঙ্গে চালাবেন না।</li><li><strong>৩ মিনিট:</strong> CV থেকে title shortlist refresh করুন।</li><li><strong>৩ মিনিট:</strong> ২–৩টি নতুন job-এর description এনে score ও gap report করুন।</li><li><strong>২ মিনিট:</strong> ৫০%+ match-এর draft review করে official page-এ application complete করুন।</li></ul><p>এই routine-এ quantity নয়, relevance গুরুত্বপূর্ণ। প্রতিদিন ২০টি random application পাঠানোর চেয়ে ৩টি ভালোভাবে যাচাই করা application বেশি কার্যকর হতে পারে।</p><h2>শেষ কথা</h2><p>Manus AI আপনার job search-এর research, comparison ও drafting সময় কমাতে পারে। কিন্তু CV-এর সত্যতা, employer-এর শর্ত, personal data, platform rules এবং final application-এর দায় আপনার। তাই সবচেয়ে নিরাপদ মডেল হলো <strong>AI-assisted, human-approved job search</strong>—AI shortlist করবে, আপনি সিদ্ধান্ত নেবেন।</p><p class="text-muted"><strong>তথ্য-সতর্কতা:</strong> Manus credit ও platform policy সময়ের সঙ্গে বদলাতে পারে। এই গাইডের credit তথ্য ২ নভেম্বর ২০২৬-এ দেখা official Manus Help Center-এর ভিত্তিতে লেখা। কোনো platform-এ automation চালানোর আগে সেই platform-এর সর্বশেষ terms অবশ্যই পড়ুন।</p>',
                'category' => 'academic-career',
                'tags' => ['career', 'job-search', 'automation', 'manus-ai', 'artificial-intelligence', 'technology'],
            ],
            [
                'slug' => 'hsc-result-2026-summary-analysis-board-challenge',
                'title' => 'এইচএসসি ও সমমান ফল ২০২৬: সারাংশ, বোর্ডভিত্তিক বিশ্লেষণ ও বোর্ড চ্যালেঞ্জের নিয়ম',
                'title_en' => 'HSC and Equivalent Result 2026: Summary, Board-wise Analysis and Re-scrutiny Guide',
                'excerpt' => '৩১ অক্টোবর ২০২৬ প্রকাশিত এইচএসসি ও সমমান ফলাফলের গুরুত্বপূর্ণ সংখ্যা, বোর্ডভিত্তিক পাসের হার এবং পুনঃনিরীক্ষণের সরকারি নির্দেশনা একসঙ্গে।',
                'content' => '<figure><img src="/images/blog/hsc-result-2026-analysis.png" alt="এইচএসসি ও সমমান পরীক্ষা ২০২৬-এর ফলাফলের বোর্ডভিত্তিক বিশ্লেষণ" loading="lazy"><figcaption>চিত্র: Sikhun-এর ডেটা ভিজ্যুয়ালাইজেশন; উৎস—সরকারি ফলাফল পোর্টাল ও শিক্ষা বোর্ডের প্রকাশিত তথ্য।</figcaption></figure><h2>ফল প্রকাশের সারাংশ</h2><p>এইচএসসি ও সমমান পরীক্ষা ২০২৬-এর ফল ৩১ অক্টোবর ২০২৬ প্রকাশিত হয়েছে। ফল দেখতে <a href="https://www.educationboardresults.gov.bd/v2/home" rel="nofollow noopener" target="_blank">সরকারি শিক্ষা বোর্ড ফলাফল পোর্টাল</a> ব্যবহার করা যায়। পোর্টালে বোর্ড, পরীক্ষা, বছর এবং Individual/Detailed Result নির্বাচন করে ফল দেখা যায়।</p><p>এবার ১১টি শিক্ষা বোর্ডে মোট ১২,৭০,৫৮৩ জন পরীক্ষার্থী নিবন্ধিত ছিলেন; পরীক্ষায় অংশ নেন ১২,৪০,৭৯৮ জন। মোট ৬,৪৬,৬৮৫ জন উত্তীর্ণ হয়েছেন। সম্মিলিত পাসের হার ৫২.১২ শতাংশ, যা ২০২৫ সালের ৫৮.৮৩ শতাংশের তুলনায় ৬.৭১ শতাংশীয় পয়েন্ট কম। GPA-5 পেয়েছেন ৪০,০৮৪ জন; গত বছর এই সংখ্যা ছিল ৬৯,০৯৭। এই জাতীয় সংখ্যাগুলো ফল প্রকাশ-সংক্রান্ত সরকারি নোটিশ ও ফলাফল ডেটার সঙ্গে মিলিয়ে প্রকাশ করা হয়েছে।</p><p><a href="https://www.dhakaeducationboard.gov.bd/index.php/site/product/hsccorner" rel="nofollow noopener" target="_blank">ঢাকা শিক্ষা বোর্ডের HSC Corner</a>-এ ফল প্রকাশের প্রেস বিজ্ঞপ্তি, পুনঃনিরীক্ষণ নির্দেশনা এবং সংশ্লিষ্ট ডাউনলোড লিংক রাখা হয়েছে।</p><figure><img src="/images/blog/hsc-result-2026-portal.jpg" alt="সরকারি শিক্ষা বোর্ড ফলাফল পোর্টালে HSC 2026 ফল দেখার ফর্ম" loading="lazy"><figcaption>সরকারি ফলাফল পোর্টালে বোর্ড, পরীক্ষা, বছর ও রোল/রেজিস্ট্রেশন তথ্য দিয়ে ফল দেখা যায়।</figcaption></figure><h2>বোর্ডভিত্তিক পাসের হার</h2><p>সাধারণ শিক্ষা বোর্ডগুলোর মধ্যে ঢাকা বোর্ডে পাসের হার সবচেয়ে বেশি—৬১.৮০ শতাংশ। বরিশাল বোর্ডে ৪৪.৮৪ শতাংশ নিয়ে সাধারণ বোর্ডগুলোর মধ্যে হার সবচেয়ে কম। মাদ্রাসা বোর্ডে ৬১.০৭ শতাংশ এবং কারিগরি বোর্ডে ৪৯.৪৩ শতাংশ পরীক্ষার্থী পাস করেছেন। বোর্ডভিত্তিক হার ও GPA-5 সংখ্যার সারাংশ:</p><ul><li>ঢাকা: ৬১.৮০% পাস, GPA-5 ১৬,৭১৪</li><li>চট্টগ্রাম: ৫২.০৭% পাস, GPA-5 ২,৯২৯</li><li>ময়মনসিংহ: ৫১.৪৪% পাস, GPA-5 ২,১৩৭</li><li>সিলেট: ৫০.৯০% পাস, GPA-5 ১,২০৪</li><li>কুমিল্লা: ৪৮.০৪% পাস, GPA-5 ১,৩১৩</li><li>যশোর: ৪৪.৯২% পাস, GPA-5 ৩,২২৮</li><li>বরিশাল: ৪৪.৮৪% পাস, GPA-5 ৯৫০</li><li>দিনাজপুর: ৪৫.৯৮% পাস, GPA-5 ২,৯৫৩</li><li>রাজশাহী: ৪৫.০৬% পাস, GPA-5 ৪,৫২০</li><li>মাদ্রাসা: ৬১.০৭% পাস, GPA-5 ৩,২৫০</li><li>কারিগরি: ৪৯.৪৩% পাস, GPA-5 ৮৮৬</li></ul><p>এই ১১টি বোর্ডের GPA-5 সংখ্যা যোগ করলে ৪০,০৮৪ হয়। তাই শুধু পাসের হার নয়, বোর্ডের পরীক্ষার্থী সংখ্যা, বিষয়ভিত্তিক ফল, প্রতিষ্ঠানভেদে পার্থক্য এবং GPA-5-এর বণ্টনও বিশ্লেষণে দেখা জরুরি। জাতীয় গড় কমেছে বলে প্রত্যেক শিক্ষার্থীর সম্ভাবনা কমে যায় না; ফলকে নিজের লক্ষ্য, বিষয়ভিত্তিক নম্বর এবং পরবর্তী ভর্তি পরিকল্পনার সঙ্গে মিলিয়ে দেখুন।</p><h2>বোর্ড চ্যালেঞ্জ বা পুনঃনিরীক্ষণ</h2><p>কোনো বিষয়ে নম্বর গণনা, যোগফল বা ফল প্রকাশে অসঙ্গতি মনে হলে ফলাফল পুনঃনিরীক্ষণ/উত্তরপত্র পুনর্মূল্যায়নের সরকারি প্রক্রিয়ায় আবেদন করা যায়। <a href="https://dhakaeducationboard.gov.bd/data/20261030135522418317.pdf" rel="nofollow noopener" target="_blank">ঢাকা শিক্ষা বোর্ডের ৩০ অক্টোবর ২০২৬-এর নির্দেশনা</a> অনুযায়ী আবেদন অনলাইনে <a href="https://rescrutiny.eduboardresults.gov.bd" rel="nofollow noopener" target="_blank">rescrutiny.eduboardresults.gov.bd</a> পোর্টালে করতে হবে। আবেদন গ্রহণের সময় ১ নভেম্বর থেকে ৭ নভেম্বর ২০২৬; প্রতি পত্রের জন্য নির্ধারিত ফি ১২৫ টাকা। ফি ও সময়সীমা জমা দেওয়ার আগে সরকারি নোটিশে আবার যাচাই করুন, কারণ বোর্ড কর্তৃপক্ষ প্রয়োজনে নির্দেশনা হালনাগাদ করতে পারে।</p><h3>আবেদনের আগে যা প্রস্তুত রাখবেন</h3><ol><li>নিজের বোর্ড, রোল নম্বর, রেজিস্ট্রেশন নম্বর ও মোবাইল নম্বর প্রস্তুত রাখুন।</li><li>যে বিষয় বা পত্রে পুনঃনিরীক্ষণ চান, সেটি ঠিকভাবে নির্বাচন করুন।</li><li>আবেদন সাবমিট করার পর পেমেন্ট সম্পন্ন করে ট্রানজেকশন/আবেদন তথ্য সংরক্ষণ করুন।</li><li>শুধু সামাজিক যোগাযোগমাধ্যমের পোস্ট নয়, নিজের বোর্ডের সরকারি নোটিশ ও পোর্টাল থেকে সময়সীমা যাচাই করুন।</li></ol><h2>ফল প্রকাশের পর শিক্ষার্থীর পরবর্তী পদক্ষেপ</h2><p>ফল প্রত্যাশার চেয়ে কম হলে আগে মার্কশিট দেখে কোন বিষয়ে ঘাটতি হয়েছে তা লিখে ফেলুন। পুনঃনিরীক্ষণের প্রয়োজন থাকলে সময়সীমার মধ্যে আবেদন করুন। এরপর বিশ্ববিদ্যালয় ভর্তি, জাতীয় বিশ্ববিদ্যালয়, মেডিকেল, প্রকৌশল বা চাকরি প্রস্তুতির যে পথ আপনার লক্ষ্য, সেটির যোগ্যতা ও সময়সূচি দেখে বাস্তবসম্মত পরিকল্পনা করুন। একটি ফলাফল আপনার পরবর্তী পথের একটি ধাপ—চূড়ান্ত পরিচয় নয়।</p><p class="text-muted"><strong>তথ্য-সতর্কতা:</strong> এই পোস্টটি ৩১ অক্টোবর ২০২৬ পর্যন্ত প্রকাশিত সরকারি ফলাফল পোর্টাল, ঢাকা শিক্ষা বোর্ডের HSC Corner ও পুনঃনিরীক্ষণ নির্দেশনার ভিত্তিতে প্রস্তুত। ব্যক্তিগত ফল বা আবেদন-সংক্রান্ত সিদ্ধান্তের আগে সংশ্লিষ্ট সরকারি পোর্টালে সর্বশেষ তথ্য যাচাই করুন।</p>',
                'category' => 'academic-career',
                'tags' => ['hsc', 'bangladesh', 'exam-preparation', 'admission'],
            ],
        ]; 

        foreach ($posts as $post) {
            $model = BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'blog_category_id' => $catMap[$post['category']],
                    'meta_title' => $post['title'],
                    'meta_description' => $post['excerpt'],
                    'status' => 'published',
                    'published_at' => now(),
                    'origin' => 'manual',
                    'original_language' => 'bn',
                    'ai_meta' => ['title_en' => $post['title_en'], 'seeded' => true],
                ]
            );

            $model->tags()->sync($tagMap->only($post['tags'])->values()->all());
        }

        SiteSetting::firstOrCreate(['key' => 'blog_digest_email'], ['value' => 'admin@sikhun.com']);
        SiteSetting::firstOrCreate(['key' => 'blog_credit_enabled'], ['value' => '1']);
        SiteSetting::firstOrCreate(['key' => 'blog_daily_ai_limit'], ['value' => '25']);
    }
}
