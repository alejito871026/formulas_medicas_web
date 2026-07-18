<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedInventario = filter_var(env('DEMO_SEED_INVENTARIO', true), FILTER_VALIDATE_BOOL);

        $this->call(RoleSeeder::class);
        $this->call(EpsSeeder::class);
        $this->call(MedicamentoSeeder::class);
        if ($seedInventario) {
            $this->call(InventarioSeeder::class);
        }
        $this->call(PacienteSeeder::class);
        $this->call(FormulaMedicaSeeder::class);
        $this->call(FormulaMedicaItemSeeder::class);
        $this->call(EntregaSeeder::class);
        $this->call(CitaSeeder::class);
        $this->call(BackfillLastSixMonthsSeeder::class);

        $roles = Role::query()->pluck('id', 'nombre');

        User::query()->updateOrCreate(
            ['email' => 'admin@formulas.test'],
            [
                'name' => 'Administrador Sistema',
                'password' => 'password123',
                'role_id' => $roles['administrativo'] ?? null,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'despachador@formulas.test'],
            [
                'name' => 'Despachador Principal',
                'password' => 'password123',
                'role_id' => $roles['despachador'] ?? null,
            ]
        );

        $despachadoresAdicionales = [
            ['email' => 'despachador2@formulas.test', 'name' => 'Despachador Norte'],
            ['email' => 'despachador3@formulas.test', 'name' => 'Despachador Sur'],
            ['email' => 'despachador4@formulas.test', 'name' => 'Despachador Oriente'],
            ['email' => 'despachador5@formulas.test', 'name' => 'Despachador Occidente'],
        ];

        foreach ($despachadoresAdicionales as $despachador) {
            User::query()->updateOrCreate(
                ['email' => $despachador['email']],
                [
                    'name' => $despachador['name'],
                    'password' => 'password123',
                    'role_id' => $roles['despachador'] ?? null,
                ]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'cliente@formulas.test'],
            [
                'name' => 'Cliente Demo',
                'password' => 'password123',
                'role_id' => $roles['cliente'] ?? null,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password123',
                'role_id' => $roles['cliente'] ?? null,
            ]
        );
    }
}
