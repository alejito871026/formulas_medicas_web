<?php

namespace App\Policies;

use App\Models\FormulaMedica;
use App\Models\Paciente;
use App\Models\User;

class FormulaMedicaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('cliente', 'despachador', 'administrativo');
    }

    public function view(User $user, FormulaMedica $formulaMedica): bool
    {
        return $user->hasRole('administrativo', 'despachador')
            || $formulaMedica->paciente?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('cliente', 'administrativo');
    }

    public function createForPaciente(User $user, Paciente $paciente): bool
    {
        return $user->hasRole('administrativo') || $paciente->user_id === $user->id;
    }

    public function update(User $user, FormulaMedica $formulaMedica): bool
    {
        return $user->hasRole('administrativo', 'despachador')
            || $formulaMedica->paciente?->user_id === $user->id;
    }

    public function delete(User $user, FormulaMedica $formulaMedica): bool
    {
        return $user->hasRole('administrativo', 'despachador')
            || $formulaMedica->paciente?->user_id === $user->id;
    }
}