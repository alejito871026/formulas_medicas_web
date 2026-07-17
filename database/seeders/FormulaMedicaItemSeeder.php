<?php

namespace Database\Seeders;

use App\Models\FormulaMedica;
use App\Models\FormulaMedicaItem;
use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class FormulaMedicaItemSeeder extends Seeder
{
    public function run(): void
    {
        $formulas = FormulaMedica::query()->orderBy('id')->get();
        $medicamentos = Medicamento::query()->orderBy('id')->get();

        if ($formulas->isEmpty() || $medicamentos->isEmpty()) {
            return;
        }

        foreach ($formulas as $formula) {
            $cantidadItems = random_int(1, 4);
            $indices = array_rand($medicamentos->all(), $cantidadItems);
            $indices = is_array($indices) ? $indices : [$indices];

            foreach ($indices as $index) {
                $medicamento = $medicamentos[$index];
                $cantidadFormulada = random_int(5, 60);
                $fechaBase = $formula->fecha_formula ? now()->parse($formula->fecha_formula)->startOfDay() : now()->subDays(random_int(0, 180));
                $fechaRegistro = (clone $fechaBase)->addDays(random_int(0, 20))->min(now());

                FormulaMedicaItem::query()->updateOrCreate(
                    [
                        'formula_medica_id' => $formula->id,
                        'medicamento_id' => $medicamento->id,
                    ],
                    [
                        'cantidad_formulada' => $cantidadFormulada,
                        'cantidad_entregada' => 0,
                        'dosis' => random_int(1, 2) . ' unidad(es)',
                        'frecuencia' => ['cada 8 horas', 'cada 12 horas', 'cada 24 horas'][array_rand([0, 1, 2])],
                        'estado_item' => 'pendiente',
                        'created_at' => $fechaRegistro,
                        'updated_at' => (clone $fechaRegistro)->addDays(random_int(0, 15))->min(now()),
                    ]
                );
            }
        }
    }
}
