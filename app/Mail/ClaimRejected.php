<?php

namespace App\Mail;

use App\Models\ClaimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ClaimRequest $claim) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de reclamo — ' . $this->claim->lugar->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.claim-rejected',
        );
    }
}
