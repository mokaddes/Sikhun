<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        // One set of credentials, assigned to every use case and marked
        // default for all of them — an admin can still add more providers
        // later and split use cases across them differently if they want,
        // but this is the sane out-of-the-box default most people want.
        $provider = AiProvider::updateOrCreate(
            ['name' => 'OpenAI GPT-4o (default)'],
            [
                'type' => 'openai',
                'model_name' => 'gpt-4o',
                'api_key' => env('OPENAI_API_KEY'),
                'is_active' => true,
                'max_tokens' => 2000,
                'temperature' => 0.70,
            ]
        );

        $useCases = ['book_chat', 'exam_gen', 'flashcard_gen', 'essay_grade', 'schedule_gen', 'notification_gen', 'support_bot'];

        foreach ($useCases as $useCase) {
            $provider->useCases()->updateOrCreate(
                ['use_case' => $useCase],
                ['is_default' => true]
            );
        }
    }
}
