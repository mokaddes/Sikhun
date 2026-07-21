<?php

namespace Database\Seeders;

use App\Models\Publication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $publications = ['NCTB', 'Panjeree Publications', 'Hasan Book House', 'Lecture Publications', 'Adarsha Prokashani'];

        foreach ($publications as $name) {
            Publication::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
