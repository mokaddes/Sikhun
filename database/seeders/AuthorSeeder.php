<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = ['ড. মোহাম্মদ কিবরিয়া', 'প্রফেসর আনোয়ার হোসেন', 'ড. ফারজানা আক্তার', 'মোঃ রফিকুল ইসলাম', 'ড. সুমাইয়া রহমান'];

        foreach ($authors as $name) {
            Author::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
