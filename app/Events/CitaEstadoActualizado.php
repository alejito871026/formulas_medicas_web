<?php

namespace App\Events;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CitaEstadoActualizado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Cita $cita,
        public string $estadoAnterior,
        public string $estadoNuevo,
        public ?User $actor = null,
    ) {
    }
}
