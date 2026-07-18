<?php

namespace App\Mail;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaEstadoActualizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cita $cita,
        public string $estadoAnterior,
        public string $estadoNuevo,
        public ?User $actor = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualizacion de estado de cita',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cita-estado-actualizado',
        );
    }
}
