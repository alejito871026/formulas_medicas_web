<?php

namespace Database\Seeders;

use App\Models\Eps;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PacienteSeeder extends Seeder
{
    private const GEOGRAFIA = [
        'Antioquia' => ['Medellin', 'Envigado', 'Bello', 'Itagui', 'Rionegro'],
        'Cundinamarca' => ['Bogota', 'Soacha', 'Chia', 'Zipaquira', 'Facatativa'],
        'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura', 'Tulua', 'Cartago'],
        'Atlantico' => ['Barranquilla', 'Soledad', 'Malambo', 'Puerto Colombia', 'Sabanalarga'],
        'Santander' => ['Bucaramanga', 'Floridablanca', 'Giron', 'Piedecuesta', 'Barrancabermeja'],
    ];

    private const TIPOS_DOCUMENTO = ['CC', 'TI', 'CE', 'PASAPORTE', 'PEP', 'PPT'];

    public function run(): void
    {
        $rolCliente = Role::query()->firstWhere('nombre', 'paciente');
        $epsDisponibles = Eps::query()->pluck('nombre')->values()->all();

        if (! $rolCliente || empty($epsDisponibles)) {
            return;
        }

        $nombres = [
            'Alejandro', 'Camila', 'Sofia', 'Mateo', 'Valentina', 'Santiago', 'Isabella', 'Daniel', 'Luciana', 'Nicolas',
            'Mariana', 'Gabriel', 'Laura', 'Sebastian', 'Paula', 'Andres', 'Juliana', 'David', 'Manuela', 'Juan',
            'Catalina', 'Felipe', 'Daniela', 'Miguel', 'Maria', 'Samuel', 'Sara', 'Jeronimo', 'Carolina', 'Emilio',
        ];

        $apellidos = [
            'Gomez', 'Rodriguez', 'Lopez', 'Martinez', 'Garcia', 'Hernandez', 'Ramirez', 'Torres', 'Diaz', 'Vargas',
            'Castro', 'Rojas', 'Morales', 'Mendoza', 'Ruiz', 'Sanchez', 'Velasco', 'Cifuentes', 'Reyes', 'Quintero',
        ];

        $totalPacientes = max(8, (int) env('DEMO_PACIENTES_TOTAL', 12));
        $passwordHash = Hash::make('password123');

        for ($i = 1; $i <= $totalPacientes; $i++) {
            $nombre = $nombres[($i - 1) % count($nombres)];
            $apellido = $apellidos[($i * 3) % count($apellidos)];
            $departamento = array_keys(self::GEOGRAFIA)[($i * 5) % count(self::GEOGRAFIA)];
            $municipios = self::GEOGRAFIA[$departamento];
            $municipio = $municipios[$i % count($municipios)];
            $eps = $epsDisponibles[$i % count($epsDisponibles)];

            $email = sprintf('cliente%03d@formulas.test', $i);
            $documento = (string) (100000000 + $i);
            $fechaRegistro = now()->subDays(random_int(0, 180));

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre . ' ' . $apellido,
                    'password' => $passwordHash,
                    'role_id' => $rolCliente->id,
                    'activo' => true,
                ]
            );

            Paciente::query()->updateOrCreate(
                ['numero_documento' => $documento],
                [
                    'user_id' => $user->id,
                    'tipo_documento' => self::TIPOS_DOCUMENTO[$i % count(self::TIPOS_DOCUMENTO)],
                    'nombres' => $nombre,
                    'apellidos' => $apellido,
                    'fecha_nacimiento' => now()->subYears(random_int(18, 85))->subDays(random_int(0, 365))->toDateString(),
                    'telefono' => '3' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT),
                    'email' => $email,
                    'direccion' => sprintf('Calle %d # %d-%d', random_int(1, 120), random_int(1, 99), random_int(1, 99)),
                    'eps' => $eps,
                    'departamento' => $departamento,
                    'municipio' => $municipio,
                    'created_at' => $fechaRegistro,
                    'updated_at' => (clone $fechaRegistro)->addDays(random_int(0, 30))->min(now()),
                ]
            );
        }

        $userDemo = User::query()->updateOrCreate(
            ['email' => 'cliente@formulas.test'],
            [
                'name' => 'Cliente Paciente',
                'password' => $passwordHash,
                'role_id' => $rolCliente->id,
                'activo' => true,
            ]
        );

        Paciente::query()->updateOrCreate(
            ['numero_documento' => '100000001'],
            [
                'user_id' => $userDemo->id,
                'tipo_documento' => 'CC',
                'nombres' => 'Cliente',
                'apellidos' => 'Paciente',
                'telefono' => '3000000000',
                'email' => 'cliente@formulas.test',
                'direccion' => 'Cartago',
                'eps' => $epsDisponibles[0],
                'departamento' => 'Valle del Cauca',
                'municipio' => 'Cartago',
                'created_at' => now()->subDays(random_int(0, 180)),
                'updated_at' => now()->subDays(random_int(0, 90)),
            ]
        );
    }
}
