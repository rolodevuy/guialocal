<?php

namespace App\Mail;

use App\Models\Suscriptor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Suscriptor $suscriptor,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Te suscribiste a Guía Local! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter-bienvenida',
            with: [
                'urlBaja' => route('newsletter.baja', $this->suscriptor->token_baja),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
