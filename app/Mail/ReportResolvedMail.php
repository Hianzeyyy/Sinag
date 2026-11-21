<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $incidentId)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your SINAG report has been resolved');
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report-resolved',
            with: ['loginUrl' => route('login')],
        );
    }
}
