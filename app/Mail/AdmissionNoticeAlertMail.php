<?php

namespace App\Mail;

use App\Models\AdmissionNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionNoticeAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdmissionNotice $notice,
        public int $totalPendingDrafts = 1
    ) {}

    public function envelope(): Envelope
    {
        $typeBn = match ($this->notice->university_type) {
            'public' => 'পাবলিক বিশ্ববিদ্যালয়',
            'guccho' => 'গুচ্ছ বিশ্ববিদ্যালয়',
            'private' => 'বেসরকারি বিশ্ববিদ্যালয়',
            default => 'বিশ্ববিদ্যালয়',
        };

        return new Envelope(
            subject: "নতুন ভর্তি বিজ্ঞপ্তি ড্রাফট প্রস্তুত: {$this->notice->institution_name} ({$typeBn})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-notice-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
