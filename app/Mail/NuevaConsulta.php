<?php

namespace App\Mail;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaConsulta extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Consulta $consulta) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->consulta->asunto ?? 'Nueva consulta recibida',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.nueva-consulta',
        );
    }
}
