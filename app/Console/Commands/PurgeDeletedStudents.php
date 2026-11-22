<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\AccountDataService;
use Illuminate\Console\Command;

/**
 * Permanently anonymizes soft-deleted student accounts whose 30-day recovery
 * window has elapsed.
 */
class PurgeDeletedStudents extends Command
{
    protected $signature = 'students:purge-deleted';

    protected $description = 'Permanently purge soft-deleted students past their 30-day recovery window';

    public function handle(AccountDataService $accountData): int
    {
        $expired = Student::onlyTrashed()
            ->whereNotNull('email')
            ->where(function ($q) {
                $q->where('scheduled_purge_at', '<=', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('scheduled_purge_at')
                            ->where('deleted_at', '<=', now()->subDays(30));
                    });
            })
            ->get();

        foreach ($expired as $student) {
            $accountData->permanentlyPurgeStudent($student);
        }

        $this->info("Permanently purged {$expired->count()} expired deleted student account(s).");

        return self::SUCCESS;
    }
}
