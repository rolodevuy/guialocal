<?php

namespace App\Mail;

use App\Models\ClaimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClaimRequest $claim,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recibimos tu solicitud — ' . $this->claim->lugar->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.claim-received',
        );
    }
}
