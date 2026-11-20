<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ObservabilityService
{
    public function __construct(protected SiteSettingService $settings) {}

    public function healthReport(): array
    {
        $dbOk = true;
        $pendingJobs = 0;
        $failedJobs = 0;

        try {
            DB::select('SELECT 1');
            if (Schema::hasTable('jobs')) {
                $pendingJobs = (int) DB::table('jobs')->count();
            }
            if (Schema::hasTable('failed_jobs')) {
                $failedJobs = (int) DB::table('failed_jobs')->count();
            }
        } catch (\Throwable $e) {
            $dbOk = false;
            Log::error('Health check DB failure', ['error' => $e->getMessage()]);
        }

        $storageWritable = is_writable(storage_path('logs'));

        return [
            'healthy' => $dbOk && $storageWritable && $failedJobs < 25,
            'database' => $dbOk,
            'storage_writable' => $storageWritable,
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    public function checkAndAlert(int $failedThreshold = 10, int $backlogThreshold = 200): array
    {
        $report = $this->healthReport();
        $alerts = [];

        if (! $report['database']) {
            $alerts[] = 'Database connectivity failed.';
        }
        if ($report['failed_jobs'] >= $failedThreshold) {
            $alerts[] = "High failed_jobs count: {$report['failed_jobs']}.";
        }
        if ($report['pending_jobs'] >= $backlogThreshold) {
            $alerts[] = "High queue backlog: {$report['pending_jobs']} pending jobs.";
        }

        if (! empty($alerts)) {
            $message = '[Sikhun Health Alert] '.implode(' ', $alerts);
            Log::warning($message, $report);

            $webhook = trim((string) $this->settings->get('observability_webhook_url', ''));
            if ($webhook !== '') {
                try {
                    Http::timeout(5)->post($webhook, [
                        'text' => $message,
                        'report' => $report,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Failed to send observability alert webhook', ['error' => $e->getMessage()]);
                }
            }
        }

        return $report;
    }
}
