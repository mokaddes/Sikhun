<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlogDraftDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array{
     *   total_drafts: int,
     *   new_drafts: int,
     *   duplicates: int,
     *   blocked: int,
     *   failed: int,
     *   recent_drafts: iterable
     * } $summary
     */
    public function __construct(public array $summary) {}

    public function envelope(): Envelope
    {
        $newCount = $this->summary['new_drafts'] ?? 0;
        $totalCount = $this->summary['total_drafts'] ?? 0;

        return new Envelope(
            subject: "Sikhun Blog: {$newCount}টি নতুন ড্রাফট প্রস্তুত (মোট ড্রাফট: {$totalCount}টি)"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.blog-draft-digest',
            with: ['summary' => $this->summary]
        );
    }
}
