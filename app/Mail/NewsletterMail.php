<?php

namespace App\Mail;

use App\Models\Suscriptor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Suscriptor $suscriptor,
        public readonly Collection $nuevosNegocios,
        public readonly Collection $promociones,
        public readonly ?object    $ultimoArticulo,
    ) {}

    public function envelope(): Envelope
    {
        $zonaNombre = $this->suscriptor->zona?->nombre ?? 'tu zona';

        return new Envelope(
            subject: "Novedades de {$zonaNombre} — Guía Local",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter',
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
