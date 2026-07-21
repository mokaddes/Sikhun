<?php

namespace App\Mail;

use App\Models\StudentSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public StudentSubscription $subscription, public int $daysRemaining) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your Sikhun.com subscription expires in {$this->daysRemaining} day(s)");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-expiry', with: [
            'subscription' => $this->subscription,
            'daysRemaining' => $this->daysRemaining,
        ]);
    }
}
