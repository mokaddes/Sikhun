<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
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

        SiteSetting::firstOrCreate(['key' => 'blog_digest_email'], ['value' => 'admin@sikhun.com']);
        SiteSetting::firstOrCreate(['key' => 'blog_credit_enabled'], ['value' => '1']);
        SiteSetting::firstOrCreate(['key' => 'blog_daily_ai_limit'], ['value' => '25']);
    }
}
