<?php

namespace Database\Seeders;

use App\Models\FormulaMedica;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class FormulaMedicaSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = Paciente::query()->orderBy('id')->get();

        if ($pacientes->isEmpty()) {
            return;
        }

        $estados = ['pendiente', 'en_validacion', 'parcial', 'entregada', 'vencida'];

        for ($i = 1; $i <= 120; $i++) {
            $paciente = $pacientes[($i - 1) % $pacientes->count()];
            $fechaFormula = now()->subDays(random_int(0, 180));

            FormulaMedica::query()->updateOrCreate(
                ['numero_formula' => sprintf('FM-2026-%05d', $i)],
                [
                    'paciente_id' => $paciente->id,
                    'fecha_formula' => $fechaFormula->toDateString(),
                    'fecha_vencimiento' => (clone $fechaFormula)->addDays(random_int(30, 180))->toDateString(),
                    'medico_tratante' => [
                        'Dra. Laura Rios',
                        'Dr. Carlos Mena',
                        'Dra. Paola Herrera',
                        'Dr. Andres Cifuentes',
                    ][array_rand([0, 1, 2, 3])],
                    'estado' => $estados[array_rand($estados)],
                    'observaciones' => 'Formula generada para simulacion operativa del dispensario.',
                    'created_at' => $fechaFormula,
                    'updated_at' => (clone $fechaFormula)->addDays(random_int(0, 25))->min(now()),
                ]
            );
        }
    }
}
