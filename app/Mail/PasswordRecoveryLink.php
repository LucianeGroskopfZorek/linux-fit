<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordRecoveryLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $resetUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redefinição de senha | Linux Fit',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-recovery',
            text: 'emails.password-recovery-text',
        );
    }
}
