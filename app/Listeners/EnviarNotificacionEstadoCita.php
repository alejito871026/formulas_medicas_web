<?php

namespace App\Listeners;

use App\Events\CitaEstadoActualizado;
use App\Services\ResendEmailService;
use Illuminate\Support\Facades\View;

class EnviarNotificacionEstadoCita
{
    public function __construct(private readonly ResendEmailService $resendEmailService)
    {
    }

    public function handle(CitaEstadoActualizado $event): void
    {
        $cita = $event->cita->loadMissing('paciente.user');

        $destinatarios = collect([
            $cita->paciente?->email,
            $cita->paciente?->user?->email,
        ])
            ->filter(fn ($email) => is_string($email) && trim($email) !== '')
            ->map(fn (string $email) => strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        if (empty($destinatarios)) {
            return;
        }

        $this->resendEmailService->send(
            $destinatarios,
            'Actualizacion de estado de cita',
            View::make('emails.cita-estado-actualizado', [
                'cita' => $cita,
                'estadoAnterior' => $event->estadoAnterior,
                'estadoNuevo' => $event->estadoNuevo,
                'actor' => $event->actor,
            ])->render(),
        );
    }
}
