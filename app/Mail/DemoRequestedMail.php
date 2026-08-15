<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Solicitud de Demo Personalizada - ' . ($this->data['empresa'] ?? 'Inshidento'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>Nueva Solicitud de Demo recibida</h2>
                <p><strong>Nombre:</strong> {$this->data['nombre']}</p>
                <p><strong>Empresa:</strong> {$this->data['empresa']}</p>
                <p><strong>Correo Electrónico:</strong> {$this->data['email']}</p>
                <p><strong>Número de Sucursales / Edificios:</strong> {$this->data['sucursales']}</p>
            ",
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
