<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Mentor;
use Illuminate\Database\Seeder;

class UdemyCoursesSeeder extends Seeder
{
    public function run(): void
    {
        $mentor = Mentor::first();
        $category = Category::where('slug', 'non-academic')->first() ?? Category::first();

        // ----------------------------------------------------
        // COURSE 1: Learn Redis Fast - The Complete Crash Course
        // ----------------------------------------------------
        $redisDescription = <<<'MD'
আধুনিক সফটওয়্যার ডেভেলপমেন্ট ও হাই-স্কেল ব্যাকএন্ড সিস্টেমে রেডিস (Redis - Remote Dictionary Server) একটি অপরিহার্য প্রযুক্তি। আপনি যদি আপনার ওয়েব অ্যাপ্লিকেশন, মোবাইল অ্যাপ বা এপিআই (API)-এর গতি শতগুণ বাড়িয়ে ডেটাবেসের ওপর লোড কমাতে চান, তবে রেডিস শেখা আপনার জন্য আবশ্যক। 

এই কোর্সটি সম্পূর্ণ প্র্যাকটিক্যাল ও টু-দ্য-পয়েন্ট নির্দেশিকা, যা খুব দ্রুত সময়ে আপনাকে রেডিসের ফান্ডামেন্টাল থেকে শুরু করে প্রোডাকশন লেভেলের ক্যাশিং ও মেমোরি ম্যানেজমেন্টের দক্ষতায় প্রস্তুত করবে।

---

### 🚀 আপনি এই কোর্সে যা যা শিখবেন:
1. **রেডিস পরিচিতি ও আর্কিটেকচার:** ইন-মেমোরি ডেটাবেস কীভাবে কাজ করে এবং রিলেশনাল ডেটাবেসের তুলনায় এটি কেন শতগুণ দ্রুত।
2. **লোকাল ও ডকার সেটআপ:** উইন্ডোজ, লিনাক্স ও ডকার কন্টেইনারে খুব সহজে রেডিস সার্ভার ও `redis-cli` সেটআপ।
3. **কোর ডেটা স্ট্রাকচার্স:**
   - **Strings:** কি-ভ্যালু পেয়ার, এক্সপিরেশন টাইম (TTL) এবং ইনক্রিমেন্ট অপারেশন।
   - **Hashes:** অবজেক্ট ডেটা সংরক্ষণ ও দ্রুত ফিল্ড-লেভেল অ্যাক্সেস।
   - **Lists:** কিউ (Queue) এবং স্ট্যাক (Stack) হিসেবে রিয়েল-টাইম টাস্ক প্রসেসিং (LPUSH, RPUSH, LPOP)।
   - **Sets & Sorted Sets:** ইউনিক ডেটা ম্যানেজমেন্ট এবং লিডারবোর্ড (Leaderboard) তৈরি।
4. **হাই-পারফরম্যান্স ক্যাশিং স্ট্র্যাটেজি:** ডাটাবেস কুয়েরি ক্যাশিং, সেশন স্টোরেজ এবং ক্যাশ ইনভ্যালিডেশন টেকনিক।
5. **পাব/সাব (Pub/Sub) আর্কিটেকচার:** রিয়েল-টাইম চ্যাট ও নোটিফিকেশন সিস্টেম তৈরিতে মেসেজ ব্রোকার হিসেবে রেডিসের ব্যবহার।
6. **ডেটা পারসিস্টেন্স ও সিকিউরিটি:** RDB স্ন্যাপশট ও AOF (Append Only File) এর মাধ্যমে মেমোরির ডেটা ডিস্কে সুরক্ষিত রাখা।
7. **ব্যাকএন্ড ইন্টিগ্রেশন:** নোড জেএস (Node.js), পাইথন বা পিএইচপি/লারাভেলের সাথে রেডিস কানেক্ট করে লাইভ প্রজেক্ট তৈরি।

---

### 🎯 এই কোর্সটি কাদের জন্য:
- ব্যাকএন্ড ওয়েব ডেভেলপার (Node.js, Python, PHP, Java, Golang)
- ফুল-স্ট্যাক ডেভেলপার যারা হাই-ট্রাফিক অ্যাপ্লিকেশন তৈরি করতে চান
- সফটওয়্যার ইঞ্জিনিয়ারিং শিক্ষার্থী ও জব প্রত্যাশীরা
- যে কেউ যিনি ডাটাবেসের রেসপন্স টাইম ০.১ সেকেন্ডের নিচে নামিয়ে আনতে চান

---

### 📦 কোর্সের সাথে যা যা পাচ্ছেন:
- সম্পূর্ণ এইচডি ভিডিও লেকচার
- প্রজেক্ট সোর্স কোড ও হ্যান্ডস-অন কনফিগারেশন ফাইল
- গুগল ড্রাইভে আজীবন আনলিমিটেড অ্যাক্সেস ও ডাউনলোড সুবিধা
MD;

        $redisCourse = Course::updateOrCreate(
            ['slug' => 'learn-redis-fast-crash-course'],
            [
                'title' => 'রেডিস ক্র্যাশ কোর্স – হাই-পারফরম্যান্স ক্যাশিং ও ইন-মেমোরি ডাটাবেস (Learn Redis Fast)',
                'description' => $redisDescription,
                'delivery_type' => 'file_download',
                'external_link' => 'https://drive.google.com/drive/folders/17THjrrcd387oZ4EPCvfkmsFeQRU_Dqp1?usp=drive_link',
                'link_note' => 'কোর্সটির সকল লেকচার ভিডিও এবং প্রয়োজনীয় রিসোর্স গুগল ড্রাইভ ফোল্ডারে দেওয়া আছে। উপরের বাটনে ক্লিক করে গুগল ড্রাইভ থেকে ফুল কোর্সটি ডাউনলোড করে আজীবন অফলাইনে স্টাডি করতে পারবেন। কোনো টেকনিক্যাল সমস্যা হলে শিখুন সাপোর্ট সেন্টারে যোগাযোগ করুন।',
                'cover_image' => 'courses/covers/redis-crash-course.jpg',
                'mentor_id' => $mentor?->id,
                'category_id' => $category?->id,
                'level' => 'job',
                'price' => 99.00,
                'is_active' => true,
            ]
        );

        $redisCurriculum = [
            'মডিউল ১: পরিচিতি ও রেডিস আর্কিটেকচার' => [
                ['title' => 'কোর্সে স্বাগতম ও রেডিস পরিচিতি (Course Welcome & What is Redis)', 'duration' => 8, 'free' => true],
                ['title' => 'ইন-মেমোরি বনাম ডিস্ক স্টোরেজ আর্কিটেকচার (In-Memory vs Disk Storage Architecture)', 'duration' => 10, 'free' => false],
                ['title' => 'ডকার ও সিএলআই সেটআপ (Redis Installation with Docker & redis-cli)', 'duration' => 12, 'free' => false],
            ],
            'মডিউল ২: কোর ডেটা স্ট্রাকচার্স ও এসেনশিয়াল কমান্ডস' => [
                ['title' => 'স্ট্রিংস ও এক্সপিরেশন টাইম (Working with Strings & TTL Expirations)', 'duration' => 14, 'free' => false],
                ['title' => 'লিস্ট দিয়ে টাস্ক কিউ তৈরি (Using Lists as High-Speed Task Queues)', 'duration' => 12, 'free' => false],
                ['title' => 'হ্যাশ দিয়ে অবজেক্ট ডেটা সংরক্ষণ (Storing Objects with Hashes)', 'duration' => 15, 'free' => false],
                ['title' => 'সেটস ও রিয়েল-টাইম লিডারবোর্ড (Sets & Sorted Sets for Realtime Leaderboards)', 'duration' => 15, 'free' => false],
            ],
            'মডিউল ৩: রিয়েল-টাইম মেসেজিং ও পাব/সাব' => [
                ['title' => 'পাব/সাব প্যাটার্ন পরিচিতি (Understanding Pub/Sub Architecture)', 'duration' => 10, 'free' => false],
                ['title' => 'মেসেজ ব্রোকার ও ইভেন্ট ডিস্ট্রিবিউশন (Building a Real-time Message Broker)', 'duration' => 14, 'free' => false],
            ],
            'মডিউল ৪: পারসিস্টেন্স, ক্যাশিং ও প্রোডাকশন প্রজেক্ট' => [
                ['title' => 'ডেটা পারসিস্টেন্স: RDB স্ন্যাপশট ও AOF লগস (Persistence with RDB & AOF)', 'duration' => 12, 'free' => false],
                ['title' => 'স্লো ডাটাবেস কুয়েরির জন্য হাই-স্পিড ক্যাশ লেয়ার (Building a Fast Cache Layer)', 'duration' => 18, 'free' => false],
                ['title' => 'প্রোডাকশন বেস্ট প্র্যাকটিস ও কোর্স সমাপনী (Production Best Practices & Wrap-up)', 'duration' => 8, 'free' => false],
            ],
        ];

        $secOrder = 1;
        foreach ($redisCurriculum as $secTitle => $lessons) {
            $section = CourseSection::updateOrCreate(
                ['course_id' => $redisCourse->id, 'title' => $secTitle],
                ['sort_order' => $secOrder++]
            );

            $lesOrder = 1;
            foreach ($lessons as $lessonData) {
                CourseLesson::updateOrCreate(
                    ['course_section_id' => $section->id, 'title' => $lessonData['title']],
                    [
                        'type' => 'video',
                        'duration_minutes' => $lessonData['duration'],
                        'is_free_preview' => $lessonData['free'],
                        'sort_order' => $lesOrder++,
                    ]
                );
            }
        }

        // ----------------------------------------------------
        // COURSE 2: Python for Data Analysts - The Complete Flagship
        // ----------------------------------------------------
        $pythonDescription = <<<'MD'
ডেটা অ্যানালিটিক্স বর্তমান বিশ্বের সবচেয়ে চাহিদাসম্পন্ন ও উচ্চ বেতনের ক্যারিয়ারগুলোর মধ্যে অন্যতম। আন্তর্জাতিক বাজারে একজন প্রফেশনাল ডেটা অ্যানালিস্টের মতো কাজ করার জন্য পাইথন (Python) হলো সবচেয়ে শক্তিশালী হাতিয়ার।

বিশ্বখ্যাত অনলাইন লার্নিং প্ল্যাটফর্ম Udemy-এর জনপ্রিয় ফ্ল্যাগশিপ কোর্স **"Python for Data Analysts: The Complete Flagship"**-এর এই সম্পূর্ণ প্রোগ্রামে একদম শূন্য (Beginner) থেকে শুরু করে রিয়েল-ওয়ার্ল্ড ডেটা হ্যান্ডলিং, ভিজ্যুয়ালাইজেশন এবং এআই-পাওয়ার্ড অ্যানালিটিক্স শেখানো হয়েছে। কোনো পূর্ব কোডিং অভিজ্ঞতার প্রয়োজন নেই!

---

### 🌟 কোর্সের মূল আকর্ষণ ও আপনি যা যা শিখবেন:
1. **পাইথন প্রোগ্রামিং ফান্ডামেন্টালস (Python Zero to Hero):** 
   - ভ্যারিয়েবল, ডেটা টাইপস, অপারেটরস এবং কন্ডিশনাল লজিক।
   - লুপস, লিস্ট, ডিকশনারি, টাপল এবং ফাংশনালিটি।
   - ক্লিন কোড লেখা এবং ডিবাগিং কৌশল।
2. **পান্ডাস ও নামপাই দিয়ে প্রফেশনাল ডেটা প্রসেসিং (Pandas & NumPy Mastery):**
   - বাস্তব জীবনের অপরিষ্কার (Messy/Dirty) ডেটাসেট ক্লিন ও প্রসেস করা।
   - ডেটা ফিল্টারিং, সর্টিং, গ্রুপিং এবং পাইভট টেবিল (Pivot Tables) তৈরি।
   - মিসিং ভ্যালু ফিক্স এবং ডেটা ট্রান্সফর্মেশন।
3. **এসকিউএল ও ডেটাবেস ইন্টিগ্রেশন (SQL with Python):**
   - রিলেশনাল ডেটাবেস থেকে ডেটা ফেচ ও ফিল্টার করা।
   - পাইথনের ভেতর এসকিউএল কুয়েরি রান করে ডেটাফ্রেম তৈরি করা।
4. **বিজনেস স্ট্যাটিস্টিকস ও এক্সপ্লোরেটরি ডেটা অ্যানালাইসিস (EDA):**
   - ডেটার ভেতরের হিডেন প্যাটার্ন ও ট্রেন্ড খুঁজে বের করার কৌশল।
   - ডিসক্রিপটিভ স্ট্যাটিস্টিকস ও হাইপোথিসিস টেস্টিং।
5. **ডেটা ভিজ্যুয়ালাইজেশন (Data Storytelling):**
   - Matplotlib ও Seaborn দিয়ে দৃষ্টিনন্দন চার্ট ও গ্রাফ তৈরি।
   - বিজনেস ড্যাশবোর্ড ও রিপোর্ট প্রেজেন্টেশন।
6. **এআই-চালিত ওয়ার্কফ্লো ও মেশিন লার্নিং (AI-Augmented Workflows):**
   - চ্যাটজিপিটি ও আধুনিক এআই টুলস ব্যবহার করে দ্রুত কোড অপ্টিমাইজেশন।
   - প্রেডিক্টিভ মডেলিং ও মেশিন লার্নিংয়ের ফান্ডামেন্টালস।
7. **হ্যান্ডস-অন ক্যাপস্টোন প্রজেক্ট (Portfolio Capstone Project):**
   - শুরু থেকে শেষ পর্যন্ত একটি পূর্ণাঙ্গ ডেটাসেট অ্যানালাইসিস করে পোর্টফোলিও বা সিভিতে যুক্ত করার মতো প্রজেক্ট কমপ্লিট করা।

---

### 👨‍💼 কাদের জন্য এই কোর্সটি:
- যারা একদম শুরু থেকে ডেটা অ্যানালিটিক্সে ক্যারিয়ার শুরু করতে চান
- এক্সেল (Excel) বা বিজনেস ইউজার যারা পাইথনে শিফট করতে চান
- বিশ্ববিদ্যালয়ের শিক্ষার্থী ও ফ্রেশ গ্র্যাজুয়েট যারা আইটি/ডেটা সাইন্সে জব পেতে চান
- ফ্রিল্যান্সার যারা আপওয়ার্ক ও ফাইবারে ডেটা অ্যানালাইসিস সার্ভিস দিতে চান

---

### 📁 কোর্সের সাথে যা যা পাচ্ছেন:
- ১০+ ঘণ্টার ফুল এইচডি ভিডিও লেকচার
- প্রতিটি অধ্যায়ের জুপিটার নোটবুক (.ipynb) ও প্র্যাকটিস ফাইল
- রিয়েল-লাইফ বিজনেস ডেটাসেট
- গুগল ড্রাইভ থেকে ফুল কোর্স আজীবন অ্যাক্সেস ও আনলিমিটেড ডাউনলোড
MD;

        $pythonCourse = Course::updateOrCreate(
            ['slug' => 'python-for-data-analysts-complete-course'],
            [
                'title' => 'পাইথন দিয়ে ডেটা অ্যানালাইসিস সম্পূর্ণ কোর্স (Python for Data Analysts: The Complete Flagship)',
                'description' => $pythonDescription,
                'delivery_type' => 'file_download',
                'external_link' => 'https://drive.google.com/drive/folders/1KnLr25izddbF1-od4l19TEuLSpDY7cF0?usp=drive_link',
                'link_note' => 'কোর্সটির সকল ফুল এইচডি প্রিমিয়াম ভিডিও লেকচার, জুপিটার নোটবুক (.ipynb), ডেটাসেট ও সোর্স কোড গুগল ড্রাইভ ফোল্ডারে শেয়ার করা আছে। লিংকে ক্লিক করে সম্পূর্ণ কোর্স এক ক্লিকে ডাউনলোড করে আজীবন অফলাইনে স্টাডি করতে পারবেন।',
                'cover_image' => 'courses/covers/python-data-analysts.jpg',
                'mentor_id' => $mentor?->id,
                'category_id' => $category?->id,
                'level' => 'job',
                'price' => 99.00,
                'is_active' => true,
            ]
        );

        $pythonCurriculum = [
            'মডিউল ১: পাইথন প্রোগ্রামিং ফান্ডামেন্টালস (Python Zero to Hero)' => [
                ['title' => 'স্বাগতম ও ডেটা অ্যানালিটিক্স ক্যারিয়ার পরিচিতি (Welcome & Course Roadmap)', 'duration' => 12, 'free' => true],
                ['title' => 'জুপিটার নোটবুক ও অ্যানাকোন্ডা এনভায়রনমেন্ট সেটআপ (Jupyter & Environment Setup)', 'duration' => 15, 'free' => false],
                ['title' => 'ভ্যারিয়েবল, নাম্বার, স্ট্রিং ও বেসিক ডেটা টাইপস (Variables & Primitive Types)', 'duration' => 20, 'free' => false],
                ['title' => 'লিস্ট, ডিকশনারি, সেট ও টাপল (Python Data Structures in Depth)', 'duration' => 25, 'free' => false],
                ['title' => 'কন্ডিশনাল লজিক ও লুপ কন্ট্রোল (Conditionals & Loops)', 'duration' => 22, 'free' => false],
                ['title' => 'রিইউজেবল কাস্টম ফাংশন ও এক্সেপশন হ্যান্ডলিং (Custom Functions & Error Handling)', 'duration' => 18, 'free' => false],
            ],
            'মডিউল ২: নামপাই দিয়ে হাই-পারফরম্যান্স কম্পিউটিং (NumPy Foundations)' => [
                ['title' => 'নামপাই অ্যারে বনাম সাধারণ পাইথন লিস্ট (NumPy Arrays vs Python Lists)', 'duration' => 15, 'free' => false],
                ['title' => 'অ্যারে অপারেশন, স্লাইসিং ও ব্রডকাস্টিং (Array Operations & Broadcasting)', 'duration' => 20, 'free' => false],
            ],
            'মডিউল ৩: পান্ডাস দিয়ে প্রফেশনাল ডেটা ম্যানিপুলেশন (Pandas Data Wrangling)' => [
                ['title' => 'পান্ডাস সিরিজ ও ডেটাফ্রেম পরিচিতি (Series & DataFrames Deep Dive)', 'duration' => 22, 'free' => false],
                ['title' => 'সিএসভি, এক্সেল ও জেসন ডেটাসেট লোড করা (Loading Real Datasets)', 'duration' => 18, 'free' => false],
                ['title' => 'অপরিষ্কার ডেটা ক্লিনিং ও মিসিং ভ্যালু ফিক্স (Data Cleaning & Imputation)', 'duration' => 25, 'free' => false],
                ['title' => 'ফিল্টারিং, কন্ডিশনাল সিলেকশন ও সর্টিং (Filtering & Conditional Indexing)', 'duration' => 20, 'free' => false],
                ['title' => 'গ্রুপবাই, এগ্রিগেশন ও পাইভট টেবিল তৈরি (GroupBy & Pivot Tables)', 'duration' => 25, 'free' => false],
                ['title' => 'মাল্টিপল ডেটাফ্রেম মার্জ ও জয়েন করা (Merging & Joining DataFrames)', 'duration' => 20, 'free' => false],
            ],
            'মডিউল ৪: এসকিউএল ও ডেটাবেস ইন্টিগ্রেশন (SQL with Python)' => [
                ['title' => 'পাইথনের সাথে রিলেশনাল এসকিউএল ডেটাবেস কানেকশন (Database Connections)', 'duration' => 18, 'free' => false],
                ['title' => 'এসকিউএল কুয়েরি এক্সিকিউট করে পান্ডাসে ফেচ করা (Executing SQL Queries)', 'duration' => 20, 'free' => false],
            ],
            'মডিউল ৫: ডেটা ভিজ্যুয়ালাইজেশন ও এক্সপ্লোরেটরি অ্যানালাইসিস (EDA)' => [
                ['title' => 'এক্সপ্লোরেটরি ডেটা অ্যানালাইসিস (EDA) মেথডোলজি (EDA Framework)', 'duration' => 22, 'free' => false],
                ['title' => 'Matplotlib ও Seaborn দিয়ে আকর্ষণীয় চার্ট তৈরি (Plotting with Matplotlib & Seaborn)', 'duration' => 28, 'free' => false],
                ['title' => 'বিজনেস ইনসাইটস এক্সট্র্যাকশন ও ডেটা স্টোরিটেলিং (Data Storytelling for Business)', 'duration' => 18, 'free' => false],
            ],
            'মডিউল ৬: মেশিন লার্নিং ও এআই-পাওয়ার্ড অ্যানালিটিক্স' => [
                ['title' => 'প্রেডিক্টিভ মডেলিং ও Scikit-Learn পরিচিতি (Intro to Machine Learning)', 'duration' => 22, 'free' => false],
                ['title' => 'চ্যাটজিপিটি ও এআই দিয়ে দ্রুত কোড অপ্টিমাইজেশন (AI-Augmented Workflows)', 'duration' => 15, 'free' => false],
            ],
            'মডিউল ৭: পূর্ণাঙ্গ ক্যাপস্টোন পোর্টফোলিও প্রজেক্ট' => [
                ['title' => 'রিয়েল-ওয়ার্ল্ড কেস স্টাডি প্রজেক্ট ডেসক্রিপশন (Capstone Project Overview)', 'duration' => 15, 'free' => false],
                ['title' => 'এন্ড-টু-এন্ড ডেটা ক্লিনিং, অ্যানালাইসিস ও প্রেজেন্টেশন (Full Project Implementation)', 'duration' => 30, 'free' => false],
            ],
        ];

        // ----------------------------------------------------
        // COURSE 3: PHP Laravel Build Amazing Streaming Service
        // ----------------------------------------------------
        $laravelDescription = <<<'MD'
জনপ্রিয় ভিডিও স্ট্রিমিং প্ল্যাটফর্ম যেমন Netflix, YouTube বা Hoichoi কীভাবে ভেতর থেকে কাজ করে—তা নিজের হাতে তৈরি করে শেখার চেয়ে কার্যকর কোনো উপায় হতে পারে না। এই কোর্সে জনপ্রিয় পিএইচপি ফ্রেমওয়ার্ক **Laravel** ব্যবহার করে একদম শূন্য থেকে একটি পূর্ণাঙ্গ, রিয়েল-ওয়ার্ল্ড ভিডিও স্ট্রিমিং ওয়েব অ্যাপ্লিকেশন এবং অ্যাডমিন প্যানেল তৈরি করা শেখানো হয়েছে।

আপনি যদি পিএইচপির বেসিক জানেন এবং নিজেকে একজন হাই-ডিমান্ড লারাভেল ব্যাকএন্ড ও ফুল-স্ট্যাক ইঞ্জিনিয়ার হিসেবে প্রতিষ্ঠিত করতে চান, তবে এই প্রজেক্ট-ভিত্তিক কোর্সটি আপনার পোর্টফোলিওকে অনন্য উচ্চতায় নিয়ে যাবে।

---

### 🚀 আপনি এই কোর্সে যা যা শিখবেন ও তৈরি করবেন:
1. **এনভায়রনমেন্ট ও লারাভেল সেটআপ:**
   - লারাভেলের লেটেস্ট ভার্সন ইন্সটলেশন ও কনফিগারেশন।
   - মাইএসকিউএল (MySQL) ডেটাবেস ডিজাইন ও মাইগ্রেশন।
2. **অথেনটিকেশন ও সিকিউরিটি আর্কিটেকচার:**
   - ইউজার রেজিস্ট্রেশন, লগইন এবং পাসওয়ার্ড রিসেট ফ্লো।
   - রোল-বেসড অ্যাক্সেস কন্ট্রোল (RBAC): সাধারণ ইউজার ও সুপার অ্যাডমিন প্রিভিলেজ।
   - কাস্টম মিডলওয়্যার দিয়ে রাউট সিকিউরিটি নিশ্চিত করা।
3. **ভিডিও ও মিডিয়া ম্যানেজমেন্ট সিস্টেম (CMS):**
   - ভিডিও আপলোড, থাম্বনেইল হ্যান্ডলিং এবং স্টোরেজ অপ্টিমাইজেশন।
   - ক্যাটেগরি, ট্যাগ ও জনরা (Genres) ভিত্তিক ভিডিও অর্গানাইজেশন।
   - রিলেশনাল ডেটাবেস আর্কিটেকচার (One-to-Many, Many-to-Many রিলেশনশিপ)।
4. **ভিডিও প্লেয়ার ও স্ট্রিমিং এক্সপেরিয়েন্স:**
   - কাস্টম এইচটিএমএল৫ ভিডিও প্লেয়ার ইন্টিগ্রেশন।
   - নিরবচ্ছিন্ন প্লেব্যাক, ফুলস্ক্রিন ও ভলিউম কন্ট্রোল।
   - রিলেটেড ভিডিও রিকমেন্ডেশন ও ওয়াচলিস্ট ফিচার।
5. **কমপ্লিট অ্যাডমিন ড্যাশবোর্ড ডেভেলপমেন্ট:**
   - ভিডিও, ইউজার এবং সাবস্ক্রিপশন ম্যানেজ করার জন্য শক্তিশালী অ্যাডমিন প্যানেল।
   - রিয়েল-টাইম এনালিটিক্স ও প্ল্যাটফর্ম স্ট্যাটস ভিউ।
   - বুটস্ট্র্যাপ দিয়ে তৈরি আকর্ষণীয় রেসপনসিভ UI।
6. **ইউজার ইন্টারঅ্যাকশন ও এনগেজমেন্ট:**
   - ভিডিও লাইক, কমেন্ট ও রেটিং সিস্টেম।
   - লাইভ সার্চ ও মাল্টিপল ফিল্টারিং অপশন।
7. **প্রোডাকশন ডিপ্লয়মেন্ট গাইড:**
   - লাইভ ক্লাউড সার্ভার/ভিপিএস-এ অ্যাপ্লিকেশন হোস্ট করার প্র্যাকটিক্যাল গাইড।

---

### 🎯 এই কোর্সটি কাদের জন্য:
- যারা পিএইচপি জানেন এবং রিয়েল-লাইফ লারাভেল প্রজেক্ট শিখতে চান
- ওয়েব ডেভেলপার যারা নিজেদের সিভি বা পোর্টফোলিওতে একটি হেভিওয়েট প্রজেক্ট যুক্ত করতে চান
- ফ্রিল্যান্সার যারা ক্লায়েন্টের জন্য ভিডিও পোর্টাল বা কন্টেন্ট প্ল্যাটফর্ম তৈরি করতে চান
- কম্পিউটার সায়েন্স ও সফটওয়্যার ইঞ্জিনিয়ারিংয়ের শিক্ষার্থী

---

### 📦 কোর্সের সাথে যা যা পাচ্ছেন:
- সম্পূর্ণ এইচডি ভিডিও লেকচার
- কমপ্লিট লারাভেল প্রজেক্ট সোর্স কোড ও মাইগ্রেশন ফাইল
- ডাটাবেস ডাম্প ও প্র্যাকটিস অ্যাসেটস
- গুগল ড্রাইভ থেকে লাইফটাইম আনলিমিটেড অ্যাক্সেস ও ডাউনলোড সুবিধা
MD;

        $laravelCourse = Course::updateOrCreate(
            ['slug' => 'php-laravel-build-amazing-streaming-service'],
            [
                'title' => 'পিএইচপি লারাভেল দিয়ে ফুল-স্ট্যাক ভিডিও স্ট্রিমিং সার্ভিস ডেভেলপমেন্ট (PHP Laravel: Build Amazing Streaming Service)',
                'description' => $laravelDescription,
                'delivery_type' => 'file_download',
                'external_link' => 'https://drive.google.com/drive/folders/1KnLr25izddbF1-od4l19TEuLSpDY7cF0?usp=drive_link',
                'link_note' => 'কোর্সটির সকল ফুল এইচডি প্রিমিয়াম ভিডিও লেকচার, কমপ্লিট সোর্স কোড ও প্রজেক্ট ডেটাবেস গুগল ড্রাইভ ফোল্ডারে শেয়ার করা আছে। লিংকে ক্লিক করে সম্পূর্ণ কোর্স এক ক্লিকে ডাউনলোড করে আজীবন অফলাইনে স্টাডি করতে পারবেন।',
                'cover_image' => 'courses/covers/laravel-streaming-service.jpg',
                'mentor_id' => $mentor?->id,
                'category_id' => $category?->id,
                'level' => 'job',
                'price' => 99.00,
                'is_active' => true,
            ]
        );

        $laravelCurriculum = [
            'মডিউল ১: এনভায়রনমেন্ট সেটআপ ও প্রজেক্ট আর্কিটেকচার' => [
                ['title' => 'স্বাগতম ও স্ট্রিমিং প্ল্যাটফর্ম পরিচিতি (Course Welcome & Project Overview)', 'duration' => 10, 'free' => true],
                ['title' => 'লারাভেল ইন্সটলেশন ও ডেটাবেস স্কিমা ডিজাইন (Laravel Installation & Database Schema)', 'duration' => 15, 'free' => false],
                ['title' => 'ফ্রন্টএন্ড এসেটস ও বুটস্ট্র্যাপ ৫ ইন্টিগ্রেশন (Front-end Assets & Bootstrap)', 'duration' => 14, 'free' => false],
            ],
            'মডিউল ২: অথেনটিকেশন, রোলস ও সিকিউরিটি আর্কিটেকচার' => [
                ['title' => 'মাল্টি-গার্ড অথেনটিকেশন সেটআপ (Multi-Auth System: User & Admin)', 'duration' => 18, 'free' => false],
                ['title' => 'রোল-বেসড অ্যাক্সেস কন্ট্রোল ও মিডলওয়্যার (Role-Based Access & Middlewares)', 'duration' => 16, 'free' => false],
            ],
            'মডিউল ৩: অ্যাডমিন ড্যাশবোর্ড ও মিডিয়া ম্যানেজমেন্ট (CMS)' => [
                ['title' => 'রেসপনসিভ অ্যাডমিন প্যানেল ডেভেলপমেন্ট (Building the Admin Dashboard)', 'duration' => 22, 'free' => false],
                ['title' => 'ক্যাটেগরি ও জনরা ম্যানেজমেন্ট ক্রুড (Category & Genre Management CRUD)', 'duration' => 20, 'free' => false],
                ['title' => 'ভিডিও আপলোড, থাম্বনেইল প্রসেসিং ও স্টোরেজ (Video Upload & Storage Handling)', 'duration' => 25, 'free' => false],
                ['title' => 'মুভি ও ওয়েব সিরিজ ম্যানেজমেন্ট সিস্টেম (Movies & Episodes Management)', 'duration' => 22, 'free' => false],
            ],
            'মডিউল ৪: ফ্রন্টএন্ড ভিডিও স্ট্রিমিং ও প্লেয়ার এক্সপেরিয়েন্স' => [
                ['title' => 'নেটফ্লিক্স-স্টাইল হোমপেজ ও হিরো ব্যানার ডিজাইন (Home Page & Hero Banner)', 'duration' => 20, 'free' => false],
                ['title' => 'কাস্টম এইচটিএমএল৫ ভিডিও প্লেয়ার ইন্টিগ্রেশন (HTML5 Custom Video Player)', 'duration' => 24, 'free' => false],
                ['title' => 'ওয়াচলিস্ট ও হিস্ট্রি ট্র্যাকিং ফাংশনালিটি (Watchlist & Viewing History)', 'duration' => 18, 'free' => false],
                ['title' => 'রিলেটেড কন্টেন্ট রিকমেন্ডেশন সিস্টেম (Related Content Recommendations)', 'duration' => 16, 'free' => false],
            ],
            'মডিউল ৫: লাইভ সার্চ ও ইউজার এনগেজমেন্ট' => [
                ['title' => 'লাইভ সার্চ ও ডায়নামিক ফিল্টারিং অপশন (Real-Time Search & Filters)', 'duration' => 20, 'free' => false],
                ['title' => 'ভিডিও রেটিং, লাইক ও কমেন্টস সিস্টেম (User Ratings & Comments)', 'duration' => 18, 'free' => false],
            ],
            'মডিউল ৬: প্রোডাকশন অপ্টিমাইজেশন ও সার্ভার ডিপ্লয়মেন্ট' => [
                ['title' => 'ডেটাবেস কুয়েরি অপ্টিমাইজেশন ও ক্যাশিং (Database Query Optimization)', 'duration' => 18, 'free' => false],
                ['title' => 'ক্লাউড ভিপিএস-এ অ্যাপ্লিকেশন ডিপ্লয়মেন্ট (Production Deployment & Review)', 'duration' => 25, 'free' => false],
            ],
        ];

        $secOrder = 1;
        foreach ($laravelCurriculum as $secTitle => $lessons) {
            $section = CourseSection::updateOrCreate(
                ['course_id' => $laravelCourse->id, 'title' => $secTitle],
                ['sort_order' => $secOrder++]
            );

            $lesOrder = 1;
            foreach ($lessons as $lessonData) {
                CourseLesson::updateOrCreate(
                    ['course_section_id' => $section->id, 'title' => $lessonData['title']],
                    [
                        'type' => 'video',
                        'duration_minutes' => $lessonData['duration'],
                        'is_free_preview' => $lessonData['free'],
                        'sort_order' => $lesOrder++,
                    ]
                );
            }
        }
    }
}
