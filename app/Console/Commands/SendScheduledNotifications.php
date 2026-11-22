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
            $data = $scheduled->action_url ? ['url' => $scheduled->action_url] : [];

            if ($scheduled->target_student_id) {
                $student = Student::where('status', 'active')->find($scheduled->target_student_id);
                $sent = $student && $notifications->send($student, $scheduled->type, $scheduled->title, $scheduled->body, $data, true) ? 1 : 0;
            } else {
                $sent = $notifications->sendToAudience(
                    audience: $scheduled->target_audience,
                    type: $scheduled->type,
                    title: $scheduled->title,
                    body: $scheduled->body,
                    data: $data,
                    mandatory: true,
                    sendEmail: (bool) $scheduled->send_email
                );
            }

            $scheduled->update(['sent_at' => now()]);
            $this->info("Sent scheduled notification #{$scheduled->id} to {$sent} student(s).");
        }

        if ($due->isEmpty()) {
            $this->info('No scheduled notifications due.');
        }

        return self::SUCCESS;
    }
}
