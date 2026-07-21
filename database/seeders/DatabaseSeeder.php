<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AiProviderSeeder::class,
            PlanSeeder::class,
            CategorySeeder::class,
            AuthorSeeder::class,
            PublicationSeeder::class,
            BookSeeder::class,
            MentorSeeder::class,
            CourseSeeder::class,
            StudentSeeder::class,
            SiteSettingSeeder::class,
            CustomPageSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
