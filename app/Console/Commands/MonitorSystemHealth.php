<?php

namespace App\Console\Commands;

use App\Services\ObservabilityService;
use Illuminate\Console\Command;

class MonitorSystemHealth extends Command
{
    protected $signature = 'monitor:health {--failed-threshold=10} {--backlog-threshold=200}';

    protected $description = 'Check database, storage, and queue health and emit observability alerts if thresholds are exceeded';

    public function handle(ObservabilityService $observability): int
    {
        $report = $observability->checkAndAlert(
            (int) $this->option('failed-threshold'),
            (int) $this->option('backlog-threshold')
        );

        $this->info(json_encode($report, JSON_PRETTY_PRINT));

        return $report['healthy'] ? self::SUCCESS : self::FAILURE;
    }
}
