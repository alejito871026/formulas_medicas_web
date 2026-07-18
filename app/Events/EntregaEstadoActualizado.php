<?php

namespace App\Events;

use App\Models\Entrega;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntregaEstadoActualizado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Entrega $entrega,
        public string $estadoAnterior,
        public string $estadoNuevo,
        public ?User $actor = null,
    ) {
    }
}
