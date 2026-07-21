<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAiNotification;
use Illuminate\Console\Command;

class GenerateAiNotifications extends Command
{
    protected $signature = 'notifications:generate-ai';

    protected $description = 'Dispatch a job to generate today\'s AI-powered educational notification';

    public function handle(): int
    {
        GenerateAiNotification::dispatch();
        $this->info('AI notification generation dispatched.');

        return self::SUCCESS;
    }
}
