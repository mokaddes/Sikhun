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
        $science = Category::where('slug', 'science')->first();
        $author = Author::first();
        $publication = Publication::first();

        $books = [
            ['title' => 'পদার্থবিজ্ঞান প্রথম পত্র', 'subject' => 'physics', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'পদার্থবিজ্ঞান দ্বিতীয় পত্র', 'subject' => 'physics', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'রসায়ন প্রথম পত্র', 'subject' => 'chemistry', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'রসায়ন দ্বিতীয় পত্র', 'subject' => 'chemistry', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'জীববিজ্ঞান প্রথম পত্র', 'subject' => 'biology', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'উচ্চতর গণিত প্রথম পত্র', 'subject' => 'math', 'level' => 'hsc', 'price' => 49, 'is_free' => false],
            ['title' => 'বাংলা প্রথম পত্র (HSC)', 'subject' => 'bangla', 'level' => 'hsc', 'price' => 0, 'is_free' => true],
            ['title' => 'ইংরেজি প্রথম পত্র (HSC)', 'subject' => 'english', 'level' => 'hsc', 'price' => 0, 'is_free' => true],
            ['title' => 'সাধারণ বিজ্ঞান (SSC)', 'subject' => 'science', 'level' => 'ssc', 'price' => 39, 'is_free' => false],
            ['title' => 'গণিত (SSC)', 'subject' => 'math', 'level' => 'ssc', 'price' => 39, 'is_free' => false],
            ['title' => 'BCS প্রস্তুতি গাইড', 'subject' => null, 'level' => 'job', 'price' => 99, 'is_free' => false],
            ['title' => 'ব্যাংক জব প্রস্তুতি', 'subject' => null, 'level' => 'job', 'price' => 79, 'is_free' => false],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                ['slug' => Str::slug($book['title'])],
                array_merge($book, [
                    'description' => "{$book['title']} — সম্পূর্ণ পাঠ্যবই ব্যাখ্যাসহ, প্রতিটি অধ্যায়ে অনুশীলনী ও উদাহরণ।",
                    'author_id' => $author?->id,
                    'publication_id' => $publication?->id,
                    'category_id' => $science?->id,
                    'total_pages' => rand(120, 350),
                    'is_published' => true,
                ])
            );
        }
    }
}
