<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'রাহেলা বেগম', 'করিম হোসেন', 'সুমাইয়া আক্তার', 'তানভীর আহমেদ', 'নুসরাত জাহান',
            'ইমরান হোসেন', 'ফাতেমা বিনতে আলী', 'রাকিব হাসান', 'তাসনিম আরা', 'সাকিব আল হাসান',
            'মিম আক্তার', 'জাহিদ হাসান', 'সাদিয়া ইসলাম', 'আরিফ হোসেন', 'নাফিসা রহমান',
            'ফাহিম মুনতাসির', 'তানজিলা খানম', 'শাহরিয়ার কবির', 'মৌসুমী আক্তার', 'রেজাউল করিম',
        ];

        $types = ['hsc', 'ssc', 'university', 'job'];

        foreach ($names as $i => $name) {
            $n = $i + 1;
            Student::updateOrCreate(
                ['email' => "student{$n}@sikhun.com"],
                [
                    'name' => $name,
                    'password' => bcrypt('student123'),
                    'type' => $types[$i % count($types)],
                    'status' => 'active',
                    'wallet_balance' => rand(0, 500),
                    'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
