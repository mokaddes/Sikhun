<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public int $studentId,
        public string $title,
        public string $body,
        public array $data = []
    ) {
        $this->onQueue('notifications');
    }

    public function handle(PushNotificationService $push): void
    {
        $student = Student::find($this->studentId);
        if (! $student) {
            return;
        }

        $push->sendToStudent($student, $this->title, $this->body, $this->data);
    }
}
