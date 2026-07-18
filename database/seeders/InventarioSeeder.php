<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $ubicaciones = ['Estante A1', 'Estante A2', 'Estante B1', 'Estante B2', 'Frio C1', 'Frio C2', 'Bodega D1', 'Bodega D2'];

        $totalMedicamentos = max(20, (int) env('DEMO_MEDICAMENTOS_CON_INVENTARIO', 60));
        $lotesMinimos = max(1, (int) env('DEMO_LOTES_POR_MED_MIN', 1));
        $lotesMaximos = max($lotesMinimos, (int) env('DEMO_LOTES_POR_MED_MAX', 1));

        $medicamentos = Medicamento::query()->orderBy('id')->limit($totalMedicamentos)->get();

        if ($medicamentos->isEmpty()) {
            return;
        }

        foreach ($medicamentos as $medicamento) {
            $cantidadLotes = mt_rand($lotesMinimos, $lotesMaximos);

            for ($i = 1; $i <= $cantidadLotes; $i++) {
                $stockMinimo = mt_rand(15, 80);
                $stockActual = mt_rand(0, 350);

                Inventario::query()->updateOrCreate(
                    [
                        'medicamento_id' => $medicamento->id,
                        'lote' => sprintf('L%05d-%02d', $medicamento->id, $i),
                    ],
                    [
                        'stock_actual' => $stockActual,
                        'stock_minimo' => $stockMinimo,
                        'fecha_vencimiento' => now()->addDays(mt_rand(-120, 720))->toDateString(),
                        'ubicacion' => $ubicaciones[array_rand($ubicaciones)],
                    ]
                );
            }
        }
    }
}
