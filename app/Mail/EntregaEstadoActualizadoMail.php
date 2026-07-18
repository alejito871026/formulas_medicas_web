<?php

namespace App\Mail;

use App\Models\Entrega;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntregaEstadoActualizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Entrega $entrega,
        public string $estadoAnterior,
        public string $estadoNuevo,
        public ?User $actor = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualizacion de estado de entrega',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-estado-actualizado',
        );
    }
}
