<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice, public int $daysLeft) {}

    public function envelope(): Envelope
    {
        $bn = app()->getLocale() === 'bn';

        return new Envelope(subject: $bn
            ? "ইনভয়েস {$this->invoice->invoice_number} — {$this->daysLeft} দিনে পরিশোধ"
            : "Invoice {$this->invoice->invoice_number} due in {$this->daysLeft} day(s)");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invoice-reminder', with: [
            'invoice' => $this->invoice,
            'daysLeft' => $this->daysLeft,
        ]);
    }
}
