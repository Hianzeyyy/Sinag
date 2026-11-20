<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class AccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $status,
        public readonly ?string $remarks = null,
    ) {
        if (! in_array($status, ['approved', 'rejected'], true)) {
            throw new InvalidArgumentException('Account status email must be approved or rejected.');
        }
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved'
            ? 'SINAG Account Approved – Pangasinan State University'
            : 'SINAG Account Registration Update – Pangasinan State University';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-status',
            with: [
                'loginUrl' => route('login'),
            ],
        );
    }
}
