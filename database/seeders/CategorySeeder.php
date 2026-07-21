<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $academic = Category::updateOrCreate(['slug' => 'academic'], ['name' => 'একাডেমিক', 'type' => 'academic']);
        $nonAcademic = Category::updateOrCreate(['slug' => 'non-academic'], ['name' => 'নন-একাডেমিক', 'type' => 'non_academic']);

        $academicChildren = [
            ['name' => 'বিজ্ঞান', 'slug' => 'science'],
            ['name' => 'মানবিক', 'slug' => 'humanities'],
            ['name' => 'ব্যবসায় শিক্ষা', 'slug' => 'business'],
        ];

        foreach ($academicChildren as $child) {
            Category::updateOrCreate(
                ['slug' => $child['slug']],
                array_merge($child, ['type' => 'academic', 'parent_id' => $academic->id])
            );
        }

        $nonAcademicChildren = [
            ['name' => 'উপন্যাস', 'slug' => 'novel'],
            ['name' => 'প্রবন্ধ', 'slug' => 'essay'],
        ];

        foreach ($nonAcademicChildren as $child) {
            Category::updateOrCreate(
                ['slug' => $child['slug']],
                array_merge($child, ['type' => 'non_academic', 'parent_id' => $nonAcademic->id])
            );
        }
    }
}
