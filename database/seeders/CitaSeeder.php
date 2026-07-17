<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\FormulaMedica;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = Paciente::query()->orderBy('id')->get();

        if ($pacientes->isEmpty()) {
            return;
        }

        $estados = ['programada', 'confirmada', 'reprogramada', 'cancelada', 'atendida', 'no_asistio'];
        $motivos = ['reclamacion', 'entrega_parcial', 'pendiente_stock', 'seguimiento', 'reprogramacion'];

        for ($i = 1; $i <= 140; $i++) {
            $paciente = $pacientes[($i - 1) % $pacientes->count()];
            $formula = FormulaMedica::query()
                ->where('paciente_id', $paciente->id)
                ->inRandomOrder()
                ->first();

            $fecha = now()->subDays(random_int(0, 180));

            Cita::query()->updateOrCreate(
                [
                    'paciente_id' => $paciente->id,
                    'fecha_cita' => $fecha->toDateString(),
                    'hora_cita' => sprintf('%02d:00', random_int(7, 16)),
                    'motivo' => $motivos[array_rand($motivos)],
                ],
                [
                    'formula_medica_id' => $formula?->id,
                    'estado' => $estados[array_rand($estados)],
                    'observaciones' => 'Cita generada para simulacion de agenda de entregas.',
                    'created_at' => $fecha,
                    'updated_at' => (clone $fecha)->addDays(random_int(0, 20))->min(now()),
                ]
            );
        }
    }
}
