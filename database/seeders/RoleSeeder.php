<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'cliente', 'descripcion' => 'Paciente o usuario final del sistema'],
            ['nombre' => 'despachador', 'descripcion' => 'Personal del dispensario medico'],
            ['nombre' => 'administrativo', 'descripcion' => 'Administrador funcional del sistema'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['nombre' => $role['nombre']],
                ['descripcion' => $role['descripcion']]
            );
        }
    }
}
