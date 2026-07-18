<?php

namespace App\Listeners;

use App\Events\EntregaEstadoActualizado;
use App\Services\ResendEmailService;
use Illuminate\Support\Facades\View;

class EnviarNotificacionEstadoEntrega
{
    public function __construct(private readonly ResendEmailService $resendEmailService)
    {
    }

    public function handle(EntregaEstadoActualizado $event): void
    {
        $entrega = $event->entrega->loadMissing('formulaItem.formulaMedica.paciente.user');

        $paciente = $entrega->formulaItem?->formulaMedica?->paciente;

        $destinatarios = collect([
            $paciente?->email,
            $paciente?->user?->email,
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
            'Actualizacion de estado de entrega',
            View::make('emails.entrega-estado-actualizado', [
                'entrega' => $entrega,
                'estadoAnterior' => $event->estadoAnterior,
                'estadoNuevo' => $event->estadoNuevo,
                'actor' => $event->actor,
            ])->render(),
        );
    }
}
