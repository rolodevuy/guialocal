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
        $subject = 'Nueva consulta de ' . $this->consulta->nombre;
        if ($this->consulta->asunto) {
            $subject .= ' — ' . $this->consulta->asunto;
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.nueva-consulta',
        );
    }
}
