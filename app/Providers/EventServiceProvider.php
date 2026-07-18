<?php

namespace App\Providers;

use App\Events\CitaEstadoActualizado;
use App\Events\EntregaEstadoActualizado;
use App\Listeners\EnviarNotificacionEstadoCita;
use App\Listeners\EnviarNotificacionEstadoEntrega;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CitaEstadoActualizado::class => [
            EnviarNotificacionEstadoCita::class,
        ],
        EntregaEstadoActualizado::class => [
            EnviarNotificacionEstadoEntrega::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
