<?php

namespace App\Console\Commands;

use App\Services\Jobs\JobScraperService;
use Illuminate\Console\Command;

class ScrapeJobsCommand extends Command
{
    protected $signature = 'jobs:scrape';
    protected $description = 'Scrape latest unique BD job circulars from trusted portals';

    public function handle(JobScraperService $scraper): int
    {
        $this->info('Starting BD jobs scraper...');
        $count = $scraper->scrapeAll();
        $this->info("Completed. Synced/Imported {$count} job circulars.");

        return Command::SUCCESS;
    }
}
