<?php

namespace App\Console\Commands;

use App\Models\ScheduledNotification;
use App\Models\Student;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';

    protected $description = 'Deliver any due ScheduledNotification rows to their target audience';

    public function handle(NotificationService $notifications): int
    {
        $due = ScheduledNotification::whereNull('sent_at')
            ->where('scheduled_for', '<=', now())
            ->get();

        foreach ($due as $scheduled) {
            $students = $scheduled->target_audience === 'all'
                ? Student::where('status', 'active')->get()
                : Student::where('status', 'active')->where('type', $scheduled->target_audience)->get();

            $sent = 0;
            foreach ($students as $student) {
                if ($notifications->send($student, $scheduled->type, $scheduled->title, $scheduled->body)) {
                    $sent++;
                }
            }

            $scheduled->update(['sent_at' => now()]);
            $this->info("Sent scheduled notification #{$scheduled->id} to {$sent}/{$students->count()} students.");
        }

        if ($due->isEmpty()) {
            $this->info('No scheduled notifications due.');
        }

        return self::SUCCESS;
    }
}
