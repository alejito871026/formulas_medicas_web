<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RenderUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()->pluck('id', 'nombre');

        User::query()->updateOrCreate(
            ['email' => 'jessica-serna@hotmail.com'],
            [
                'name' => 'Administrador Sistema',
                'password' => env('DEPLOY_ADMIN_PASSWORD', 'Admin12345!'),
                'role_id' => $roles['administrativo'] ?? null,
                'activo' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'wspbots.wsp@gmail.com'],
            [
                'name' => 'Despachador Principal',
                'password' => env('DEPLOY_DESPACHADOR_PASSWORD', 'Despachador123!'),
                'role_id' => $roles['despachador'] ?? null,
                'activo' => true,
            ]
        );
    }
}