<?php

namespace App\Console\Commands;

use App\Services\Admissions\AdmissionScraperService;
use Illuminate\Console\Command;

class MergeAdmissionDuplicates extends Command
{
    protected $signature = 'admissions:merge-duplicates';

    protected $description = 'Merge duplicate university admission notices by normalized institution and session, keeping merged slug history.';

    public function handle(AdmissionScraperService $scraper): int
    {
        $result = $scraper->mergeDuplicates();

        $this->info(sprintf(
            'Merged %d groups, removed %d duplicates. %d notices remaining.',
            $result['groups_merged'],
            $result['deleted_count'],
            $result['remaining_count']
        ));

        return self::SUCCESS;
    }
}
