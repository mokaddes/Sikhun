<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'science')->first();
        $nctbAuthor = Author::where('slug', 'nctb')->first();
        $nctbPublication = Publication::where('slug', 'nctb')->first();
        $author = Author::first();
        $publication = Publication::first();

        // Drop the placeholder rows written by the previous version of this
        // seeder (slug was title-only) so re-running on an existing database
        // does not leave duplicated SSC/HSC books behind.
        Book::whereIn('slug', [
            'pdarthbijngan-prthm-ptr', 'pdarthbijngan-dwiteez-ptr',
            'rsazn-prthm-ptr', 'rsazn-dwiteez-ptr',
            'jeebbijngan-prthm-ptr', 'ucctr-gnit-prthm-ptr',
            'bangla-prthm-ptr-hsc', 'ingreji-prthm-ptr-hsc',
            'sadharn-bijngan-ssc', 'gnit-ssc',
            'tthz-oo-zogazog-przukti-hsc',
        ])->delete();

        // NCTB textbooks — titles only, no PDF attached yet.
        // SSC titles come from the Bangla-version (Class 9-10) set,
        // HSC titles from the NCTB (Class 11-12) set.
        $books = [
            // SSC — Class 9-10
            ['title' => 'কৃষিশিক্ষা', 'subject' => 'agriculture', 'level' => 'ssc'],
            ['title' => 'বাংলা ব্যাকরণ ও নির্মিতি', 'subject' => 'bangla', 'level' => 'ssc'],
            ['title' => 'বাংলা সাহিত্য', 'subject' => 'bangla', 'level' => 'ssc'],
            ['title' => 'বাংলা সহপাঠ', 'subject' => 'bangla', 'level' => 'ssc'],
            ['title' => 'বাংলাদেশ ও বিশ্বপরিচয়', 'subject' => 'bgs', 'level' => 'ssc'],
            ['title' => 'ভূগোল ও পরিবেশ', 'subject' => 'geography', 'level' => 'ssc'],
            ['title' => 'জীববিজ্ঞান', 'subject' => 'biology', 'level' => 'ssc'],
            ['title' => 'রসায়ন', 'subject' => 'chemistry', 'level' => 'ssc'],
            ['title' => 'অর্থনীতি', 'subject' => 'economics', 'level' => 'ssc'],
            ['title' => 'ইংরেজি', 'subject' => 'english', 'level' => 'ssc'],
            ['title' => 'উচ্চতর গণিত', 'subject' => 'higher_math', 'level' => 'ssc'],
            ['title' => 'তথ্য ও যোগাযোগ প্রযুক্তি', 'subject' => 'ict', 'level' => 'ssc'],
            ['title' => 'ইসলাম শিক্ষা ও নৈতিক শিক্ষা', 'subject' => 'islam', 'level' => 'ssc'],
            ['title' => 'গণিত', 'subject' => 'math', 'level' => 'ssc'],
            ['title' => 'পদার্থবিজ্ঞান', 'subject' => 'physics', 'level' => 'ssc'],
            ['title' => 'বিজ্ঞান', 'subject' => 'science', 'level' => 'ssc'],

            // HSC — Class 11-12
            ['title' => 'সাহিত্য পাঠ', 'subject' => 'bangla', 'level' => 'hsc'],
            ['title' => 'সহপাঠ', 'subject' => 'bangla', 'level' => 'hsc'],
            ['title' => 'ইংরেজি', 'subject' => 'english', 'level' => 'hsc'],
            ['title' => 'রসায়ন প্রথম পত্র', 'subject' => 'chemistry', 'level' => 'hsc'],
            ['title' => 'রসায়ন দ্বিতীয় পত্র', 'subject' => 'chemistry', 'level' => 'hsc'],
            ['title' => 'পদার্থবিজ্ঞান প্রথম পত্র', 'subject' => 'physics', 'level' => 'hsc'],
            ['title' => 'পদার্থবিজ্ঞান দ্বিতীয় পত্র', 'subject' => 'physics', 'level' => 'hsc'],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                // The level is part of the slug so shared titles (e.g. "ইংরেজি")
                // stay unique across SSC and HSC.
                ['slug' => Str::slug($book['title'].' '.$book['level'])],
                array_merge($book, [
                    'description' => "{$book['title']} — এনসিটিবি (NCTB) প্রণীত ও প্রকাশিত পাঠ্যবই।",
                    'author_id' => $nctbAuthor?->id,
                    'publication_id' => $nctbPublication?->id,
                    'category_id' => $category?->id,
                    'total_pages' => rand(120, 350),
                    'price' => 0,
                    'is_free' => true,
                    'is_published' => true,
                ])
            );
        }

        // $jobBooks = [
        //     ['title' => 'BCS প্রস্তুতি গাইড', 'subject' => null, 'level' => 'job', 'price' => 99, 'is_free' => false],
        //     ['title' => 'ব্যাংক জব প্রস্তুতি', 'subject' => null, 'level' => 'job', 'price' => 79, 'is_free' => false],
        // ];

        // foreach ($jobBooks as $book) {
        //     Book::updateOrCreate(
        //         ['slug' => Str::slug($book['title'])],
        //         array_merge($book, [
        //             'description' => "{$book['title']} — প্রতিযোগিতামূলক পরীক্ষার পূর্ণাঙ্গ প্রস্তুতি।",
        //             'author_id' => $author?->id,
        //             'publication_id' => $publication?->id,
        //             'category_id' => $category?->id,
        //             'total_pages' => rand(120, 350),
        //             'is_published' => true,
        //         ])
        //     );
        // }
    }
}
