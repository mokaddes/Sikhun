<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookChunk;
use App\Models\BookElement;
use App\Models\BookPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixSscBookChapters extends Command
{
    protected $signature = 'books:fix-ssc-chapters';
    protected $description = 'Fix Biology and Chemistry books to have only genuine major chapters (no micro-sections/onucched)';

    public function handle(): int
    {
        $this->info("Fixing SSC Biology (#20) and Chemistry (#21) chapters...");

        // 1. Biology (জীববিজ্ঞান) - 14 Official Chapters from Table of Contents
        $biologyChapters = [
            ['num' => '১', 'title' => 'জীবনপাঠ', 'start' => 6, 'end' => 21],
            ['num' => '২', 'title' => 'জীবকোষ ও টিস্যু', 'start' => 22, 'end' => 54],
            ['num' => '৩', 'title' => 'কোষ বিভাজন', 'start' => 55, 'end' => 68],
            ['num' => '৪', 'title' => 'জীবনীশক্তি', 'start' => 69, 'end' => 88],
            ['num' => '৫', 'title' => 'খাদ্য, পুষ্টি এবং পরিপাক', 'start' => 89, 'end' => 129],
            ['num' => '৬', 'title' => 'জীবে পরিবহন', 'start' => 130, 'end' => 164],
            ['num' => '৭', 'title' => 'গ্যাসীয় বিনিময়', 'start' => 165, 'end' => 182],
            ['num' => '৮', 'title' => 'রেচন প্রক্রিয়া', 'start' => 183, 'end' => 194],
            ['num' => '৯', 'title' => 'দৃঢ়তা প্রদান ও চলন', 'start' => 195, 'end' => 208],
            ['num' => '১০', 'title' => 'সমন্বয়', 'start' => 209, 'end' => 235],
            ['num' => '১১', 'title' => 'জীবের প্রজনন', 'start' => 236, 'end' => 258],
            ['num' => '১২', 'title' => 'জীবের বংশগতি ও জৈব অভিব্যক্তি', 'start' => 259, 'end' => 286],
            ['num' => '১৩', 'title' => 'জীবের পরিবেশ', 'start' => 287, 'end' => 309],
            ['num' => '১৪', 'title' => 'জীবপ্রযুক্তি', 'start' => 310, 'end' => 326],
        ];

        // 2. Chemistry (রসায়ন) - 12 Official Chapters from Table of Contents
        $chemistryChapters = [
            ['num' => '১', 'title' => 'রসায়নের ধারণা', 'start' => 6, 'end' => 21],
            ['num' => '২', 'title' => 'পদার্থের অবস্থা', 'start' => 22, 'end' => 39],
            ['num' => '৩', 'title' => 'পদার্থের গঠন', 'start' => 40, 'end' => 63],
            ['num' => '৪', 'title' => 'পর্যায় সারণি', 'start' => 64, 'end' => 86],
            ['num' => '৫', 'title' => 'রাসায়নিক বন্ধন', 'start' => 87, 'end' => 113],
            ['num' => '৬', 'title' => 'মোলের ধারণা ও রাসায়নিক গণনা', 'start' => 114, 'end' => 146],
            ['num' => '৭', 'title' => 'রাসায়নিক বিক্রিয়া', 'start' => 147, 'end' => 172],
            ['num' => '৮', 'title' => 'রসায়ন ও শক্তি', 'start' => 173, 'end' => 210],
            ['num' => '৯', 'title' => 'এসিড-ক্ষারক সমতা', 'start' => 211, 'end' => 237],
            ['num' => '১০', 'title' => 'খনিজ সম্পদ: ধাতু-অধাতু', 'start' => 238, 'end' => 265],
            ['num' => '১১', 'title' => 'খনিজ সম্পদ: জীবাশ্ম', 'start' => 266, 'end' => 291],
            ['num' => '১২', 'title' => 'আমাদের জীবনে রসায়ন', 'start' => 292, 'end' => 310],
        ];

        // 3. Agriculture (কৃষিশিক্ষা) - 7 Official Chapters from Table of Contents
        $agricultureChapters = [
            ['num' => '১', 'title' => 'কৃষি প্রযুক্তি', 'start' => 6, 'end' => 40],
            ['num' => '২', 'title' => 'কৃষি উপকরণ', 'start' => 41, 'end' => 74],
            ['num' => '৩', 'title' => 'কৃষি ও জলবায়ু', 'start' => 75, 'end' => 98],
            ['num' => '৪', 'title' => 'কৃষিজ উৎপাদন', 'start' => 99, 'end' => 179],
            ['num' => '৫', 'title' => 'বনায়ন', 'start' => 180, 'end' => 206],
            ['num' => '৬', 'title' => 'কৃষি সমবায়', 'start' => 207, 'end' => 214],
            ['num' => '৭', 'title' => 'পারিবারিক খামার', 'start' => 215, 'end' => 234],
        ];

        // 4. Bangla Grammar (বাংলা ব্যাকরণ ও নির্মিতি) - 6 Major Units
        $banglaGrammarChapters = [
            ['num' => '১', 'title' => 'ভাষা ও ব্যাকরণ', 'start' => 6, 'end' => 16],
            ['num' => '২', 'title' => 'ধ্বনিতত্ত্ব ও বর্ণ', 'start' => 17, 'end' => 31],
            ['num' => '৩', 'title' => 'রূপতত্ত্ব ও শব্দ গঠন', 'start' => 32, 'end' => 87],
            ['num' => '৪', 'title' => 'বাক্যতত্ত্ব', 'start' => 88, 'end' => 110],
            ['num' => '৫', 'title' => 'বাগর্থ', 'start' => 111, 'end' => 130],
            ['num' => '৬', 'title' => 'নির্মিতি অংশ', 'start' => 131, 'end' => 218],
        ];

        // 5. Bangla Literature (বাংলা সাহিত্য) - 2 Parts
        $banglaLiteratureChapters = [
            ['num' => '১', 'title' => 'গদ্য অংশ', 'start' => 6, 'end' => 159],
            ['num' => '২', 'title' => 'কবিতা অংশ', 'start' => 160, 'end' => 262],
        ];

        // 6. Bangla Co-reading (বাংলা সহপাঠ) - 2 Parts
        $banglaSohopathChapters = [
            ['num' => '১', 'title' => 'উপন্যাস: কাকতাড়ুয়া', 'start' => 6, 'end' => 65],
            ['num' => '২', 'title' => 'নাটক: বহিপীর', 'start' => 66, 'end' => 102],
        ];

        // 7. Bangladesh & Global Studies (বাংলাদেশ ও বিশ্বপরিচয়) - 15 Chapters
        $bgsChapters = [
            ['num' => '১', 'title' => 'পূর্ব বাংলার আন্দোলন ও জাতীয়তাবাদের উত্থান (১৯৪৭-১৯৭০)', 'start' => 6, 'end' => 18],
            ['num' => '২', 'title' => 'বাংলাদেশের স্বাধীনতা', 'start' => 19, 'end' => 30],
            ['num' => '৩', 'title' => 'সৌরজগৎ ও ভূমণ্ডল', 'start' => 31, 'end' => 49],
            ['num' => '৪', 'title' => 'বাংলাদেশের ভূপ্রকৃতি ও জলবায়ু', 'start' => 50, 'end' => 65],
            ['num' => '৫', 'title' => 'বাংলাদেশের নদ-নদী ও প্রাকৃতিক সম্পদ', 'start' => 66, 'end' => 78],
            ['num' => '৬', 'title' => 'রাষ্ট্র, নাগরিকতা ও আইন', 'start' => 79, 'end' => 89],
            ['num' => '৭', 'title' => 'বাংলাদেশ সরকারের বিভিন্ন অঙ্গ ও প্রশাসন ব্যবস্থা', 'start' => 90, 'end' => 102],
            ['num' => '৮', 'title' => 'বাংলাদেশের গণতন্ত্র ও নির্বাচন ব্যবস্থা', 'start' => 103, 'end' => 111],
            ['num' => '৯', 'title' => 'জাতিসংঘ ও বাংলাদেশ', 'start' => 112, 'end' => 121],
            ['num' => '১০', 'title' => 'জাতীয় সম্পদ ও অর্থনৈতিক ব্যবস্থা', 'start' => 122, 'end' => 133],
            ['num' => '১১', 'title' => 'অর্থনৈতিক নির্দেশকসমূহ ও বাংলাদেশের অর্থনীতির প্রকৃতি', 'start' => 134, 'end' => 157],
            ['num' => '১২', 'title' => 'বাংলাদেশ সরকারের অর্থ ও ব্যাংক ব্যবস্থা', 'start' => 158, 'end' => 168],
            ['num' => '১৩', 'title' => 'বাংলাদেশের পরিবার কাঠামো ও সামাজিকীকরণ', 'start' => 169, 'end' => 182],
            ['num' => '১৪', 'title' => 'বাংলাদেশের সামাজিক পরিবর্তন', 'start' => 183, 'end' => 189],
            ['num' => '১৫', 'title' => 'বাংলাদেশের সামাজিক সমস্যা ও প্রতিকার', 'start' => 190, 'end' => 206],
        ];

        // 8. Geography & Environment (ভূগোল ও পরিবেশ) - 15 Chapters
        $bhugolChapters = [
            ['num' => '১', 'title' => 'ভূগোল ও পরিবেশ', 'start' => 6, 'end' => 12],
            ['num' => '২', 'title' => 'মহাবিশ্ব ও আমাদের পৃথিবী', 'start' => 13, 'end' => 35],
            ['num' => '৩', 'title' => 'মানচিত্র পঠন ও ব্যবহার', 'start' => 36, 'end' => 51],
            ['num' => '৪', 'title' => 'পৃথিবীর অভ্যন্তরীণ ও বাহ্যিক গঠন', 'start' => 52, 'end' => 73],
            ['num' => '৫', 'title' => 'বায়ুমণ্ডল', 'start' => 74, 'end' => 95],
            ['num' => '৬', 'title' => 'বারিমণ্ডল', 'start' => 96, 'end' => 109],
            ['num' => '৭', 'title' => 'জনসংখ্যা', 'start' => 110, 'end' => 130],
            ['num' => '৮', 'title' => 'মানববসতি', 'start' => 131, 'end' => 142],
            ['num' => '৯', 'title' => 'সম্পদ ও অর্থনৈতিক কার্যাবলি', 'start' => 143, 'end' => 151],
            ['num' => '১০', 'title' => 'বাংলাদেশের ভৌগোলিক বিবরণ', 'start' => 152, 'end' => 168],
            ['num' => '১১', 'title' => 'বাংলাদেশের সম্পদ ও শিল্প', 'start' => 169, 'end' => 189],
            ['num' => '১২', 'title' => 'বাংলাদেশের যোগাযোগ ব্যবস্থা ও বাণিজ্য', 'start' => 190, 'end' => 202],
            ['num' => '১৩', 'title' => 'বাংলাদেশের উন্নয়ন কর্মকাণ্ড ও পরিবেশের ভারসাম্য', 'start' => 203, 'end' => 212],
            ['num' => '১৪', 'title' => 'বাংলাদেশের প্রাকৃতিক দুর্যোগ', 'start' => 213, 'end' => 229],
            ['num' => '১৫', 'title' => 'টেকসই উন্নয়ন-অভীষ্ট (এসডিজি)', 'start' => 230, 'end' => 238],
        ];

        $this->rebuildChapters(20, 'জীববিজ্ঞান', $biologyChapters);
        $this->rebuildChapters(21, 'রসায়ন', $chemistryChapters);
        $this->rebuildChapters(14, 'কৃষিশিক্ষা', $agricultureChapters);
        $this->rebuildChapters(15, 'বাংলা ব্যাকরণ ও নির্মিতি', $banglaGrammarChapters);
        $this->rebuildChapters(16, 'বাংলা সাহিত্য', $banglaLiteratureChapters);
        $this->rebuildChapters(17, 'বাংলা সহপাঠ', $banglaSohopathChapters);
        $this->rebuildChapters(18, 'বাংলাদেশ ও বিশ্বপরিচয়', $bgsChapters);
        $this->rebuildChapters(19, 'ভূগোল ও পরিবেশ', $bhugolChapters);

        $this->info("Successfully rebuilt clean chapters for all SSC books!");
        return 0;
    }

    private function rebuildChapters(int $bookId, string $label, array $definitions): void
    {
        $book = Book::find($bookId);
        if (!$book) {
            $this->warn("Book #{$bookId} ({$label}) not found. Skipping.");
            return;
        }

        if ($book->processing_status !== 'completed') {
            $this->warn("Book #{$bookId} ({$label}) is still {$book->processing_status}. Skipping chapter rebuild for now.");
            return;
        }

        $this->line("Processing #{$bookId}: {$book->title} (currently has " . $book->chapters()->count() . " chapters)...");

        DB::transaction(function () use ($book, $definitions) {
            // 1. Detach references to prevent FK locks/blocks
            BookPage::where('book_id', $book->id)->update(['chapter_id' => null]);
            BookElement::where('book_id', $book->id)->update(['chapter_id' => null]);
            BookChunk::where('book_id', $book->id)->update(['chapter_id' => null]);
            BookChapter::where('book_id', $book->id)->update(['parent_id' => null]);

            // 2. Delete old micro-section chapters
            BookChapter::where('book_id', $book->id)->delete();

            $createdChapters = [];
            foreach ($definitions as $i => $def) {
                $created = $book->chapters()->create([
                    'parent_id' => null,
                    'chapter_number' => $def['num'],
                    'title' => $def['title'],
                    'slug' => Str::slug($def['title']) ?: "chapter-{$i}",
                    'level' => 1,
                    'start_page' => $def['start'],
                    'end_page' => $def['end'],
                    'sort_order' => $i,
                    'metadata' => [
                        'source' => 'official_table_of_contents',
                        'normalized_at' => now()->toIso8601String(),
                    ],
                ]);
                $createdChapters[] = $created;
            }

            // Map book_pages to new clean chapters
            foreach ($createdChapters as $ch) {
                BookPage::where('book_id', $book->id)
                    ->whereBetween('page_number', [$ch->start_page, $ch->end_page])
                    ->update(['chapter_id' => $ch->id]);

                BookElement::where('book_id', $book->id)
                    ->whereBetween('page_number', [$ch->start_page, $ch->end_page])
                    ->update(['chapter_id' => $ch->id]);
            }

            // Map book_chunks to new clean chapters based on page_number
            foreach ($createdChapters as $ch) {
                BookChunk::where('book_id', $book->id)
                    ->whereBetween('page_number', [$ch->start_page, $ch->end_page])
                    ->update(['chapter_id' => $ch->id]);
            }
        });

        $this->info("  Done! #{$bookId} now has " . $book->chapters()->count() . " clean chapters.");
    }
}
