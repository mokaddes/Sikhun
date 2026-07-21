<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Mentor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = Mentor::all();

        $courses = [
            ['title' => 'HSC পদার্থবিজ্ঞান সম্পূর্ণ কোর্স', 'level' => 'hsc', 'price' => 999],
            ['title' => 'BCS প্রিলিমিনারি ক্র্যাশ কোর্স', 'level' => 'job', 'price' => 1499],
        ];

        foreach ($courses as $i => $courseData) {
            $course = Course::updateOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                array_merge($courseData, [
                    'description' => "{$courseData['title']} — লাইভ ক্লাস, রেকর্ডেড ভিডিও এবং প্র্যাকটিস সিট সহ সম্পূর্ণ প্যাকেজ।",
                    'mentor_id' => $mentors[$i % max($mentors->count(), 1)]?->id,
                    'is_active' => true,
                ])
            );

            for ($s = 1; $s <= 3; $s++) {
                $section = CourseSection::updateOrCreate(
                    ['course_id' => $course->id, 'title' => "সেকশন {$s}: মৌলিক ধারণা"],
                    ['sort_order' => $s]
                );

                for ($l = 1; $l <= 5; $l++) {
                    CourseLesson::updateOrCreate(
                        ['course_section_id' => $section->id, 'title' => "লেসন {$l}"],
                        [
                            'type' => 'video',
                            'video_url' => 'https://example.com/video-placeholder.mp4',
                            'is_free_preview' => $s === 1 && $l === 1,
                            'sort_order' => $l,
                            'duration_minutes' => rand(10, 25),
                        ]
                    );
                }
            }
        }
    }
}
