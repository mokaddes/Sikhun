<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = [
            ['name' => 'ড. তানভীর আহমেদ', 'designation' => 'পদার্থবিজ্ঞান বিশেষজ্ঞ', 'expertise' => ['Physics', 'Higher Math']],
            ['name' => 'সাদিয়া ইসলাম', 'designation' => 'ইংরেজি প্রভাষক', 'expertise' => ['English', 'IELTS']],
            ['name' => 'মোঃ কামরুল হাসান', 'designation' => 'বিসিএস প্রশিক্ষক', 'expertise' => ['BCS Preli', 'Bank Job']],
        ];

        foreach ($mentors as $mentor) {
            Mentor::updateOrCreate(
                ['slug' => Str::slug($mentor['name'])],
                array_merge($mentor, ['bio' => "{$mentor['name']} — {$mentor['designation']} নিয়ে ১০+ বছরের অভিজ্ঞতা।"])
            );
        }
    }
}
