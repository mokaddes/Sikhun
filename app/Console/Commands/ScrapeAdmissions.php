<?php

namespace App\Console\Commands;

use App\Services\Admissions\AdmissionScraperService;
use Illuminate\Console\Command;

class ScrapeAdmissions extends Command
{
    protected $signature = 'admissions:scrape';

    protected $description = 'Scrape university admission notices, parse details, generate draft posts, and send email alerts';

    public function handle(AdmissionScraperService $scraper): int
    {
        $this->info('Starting university admission circular scraping...');

        $count = $scraper->scrapeAll();

        $this->info("Completed. Imported/updated {$count} admission notices.");

        return self::SUCCESS;
    }
}
