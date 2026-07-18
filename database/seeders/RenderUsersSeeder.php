<?php

namespace Database\Seeders;

use App\Models\Paciente;
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

        if (isset($roles['cliente'])) {
            $pacienteEmail = env('DEPLOY_PACIENTE_EMAIL', 'paciente.demo@formulas.test');

            $pacienteUser = User::query()->updateOrCreate(
                ['email' => $pacienteEmail],
                [
                    'name' => trim(env('DEPLOY_PACIENTE_NOMBRES', 'Paciente') . ' ' . env('DEPLOY_PACIENTE_APELLIDOS', 'Demo')),
                    'password' => env('DEPLOY_PACIENTE_PASSWORD', 'Paciente123!'),
                    'role_id' => $roles['cliente'],
                    'activo' => true,
                    'telefono' => env('DEPLOY_PACIENTE_TELEFONO', '3000000001'),
                    'direccion' => env('DEPLOY_PACIENTE_DIRECCION', 'Cartago'),
                ]
            );

            Paciente::query()->updateOrCreate(
                ['numero_documento' => env('DEPLOY_PACIENTE_DOCUMENTO', '1000000999')],
                [
                    'user_id' => $pacienteUser->id,
                    'tipo_documento' => env('DEPLOY_PACIENTE_TIPO_DOCUMENTO', 'CC'),
                    'nombres' => env('DEPLOY_PACIENTE_NOMBRES', 'Paciente'),
                    'apellidos' => env('DEPLOY_PACIENTE_APELLIDOS', 'Demo'),
                    'telefono' => env('DEPLOY_PACIENTE_TELEFONO', '3000000001'),
                    'email' => $pacienteEmail,
                    'direccion' => env('DEPLOY_PACIENTE_DIRECCION', 'Cartago'),
                    'eps' => env('DEPLOY_PACIENTE_EPS', null),
                    'departamento' => env('DEPLOY_PACIENTE_DEPARTAMENTO', 'Valle del Cauca'),
                    'municipio' => env('DEPLOY_PACIENTE_MUNICIPIO', 'Cartago'),
                ]
            );
        }
    }
}