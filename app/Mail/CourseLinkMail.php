<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Delivers the access link for link-based courses (enrollment invite or file
 * download). Sent on every fulfillment path — paid or free — so the student
 * always ends up with the link in their inbox.
 */
class CourseLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Course $course,
        public Student $student,
        public ?Order $order = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your course link — {$this->course->title}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.course-link', with: [
            'course' => $this->course,
            'student' => $this->student,
            'order' => $this->order,
        ]);
    }
}
