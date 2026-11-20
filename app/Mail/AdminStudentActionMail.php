<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminStudentActionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string>  $details
     */
    public function __construct(
        public Student $student,
        public string $emailSubject,
        public string $heading,
        public string $summary,
        public array $details = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-student-action',
            with: [
                'student' => $this->student,
                'heading' => $this->heading,
                'summary' => $this->summary,
                'details' => $this->details,
            ],
        );
    }
}
