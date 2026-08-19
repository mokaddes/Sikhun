<?php

namespace App\Jobs;

use App\Models\ScheduledNotification;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public $timeout = 30;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $topics = ['history', 'science', 'geography', 'culture', 'language', 'technology', 'বাংলাদেশের ইতিহাস', 'বিজ্ঞান'];
        $topic = $topics[array_rand($topics)];

        try {
            $provider = AiProviderFactory::default('notification_gen');

            $content = $provider->chat([
                ['role' => 'system', 'content' => 'You generate short educational notifications in Bengali for HSC/SSC students. Keep it under 150 characters. Format: "বিষয়: তথ্য". Return only the notification text, nothing else.'],
                ['role' => 'user', 'content' => "Generate one fact about {$topic}."],
            ], ['max_tokens' => 150, 'temperature' => 0.8]);

            ScheduledNotification::create([
                'type' => 'general_knowledge',
                'title' => 'আজকের তথ্য',
                'body' => trim($content),
                'target_audience' => 'all',
                'scheduled_for' => now()->addMinutes(5),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AI notification generation failed: '.$e->getMessage());
        }
    }
}
