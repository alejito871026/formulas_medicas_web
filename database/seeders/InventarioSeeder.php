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

        $medicamentos = Medicamento::query()->orderBy('id')->get();

        foreach ($medicamentos as $medicamento) {
            $cantidadLotes = random_int(1, 3);

            for ($i = 1; $i <= $cantidadLotes; $i++) {
                $stockMinimo = random_int(15, 80);
                $stockActual = random_int(0, 350);

                Inventario::query()->updateOrCreate(
                    [
                        'medicamento_id' => $medicamento->id,
                        'lote' => sprintf('L%05d-%02d', $medicamento->id, $i),
                    ],
                    [
                        'stock_actual' => $stockActual,
                        'stock_minimo' => $stockMinimo,
                        'fecha_vencimiento' => now()->addDays(random_int(-120, 720))->toDateString(),
                        'ubicacion' => $ubicaciones[array_rand($ubicaciones)],
                    ]
                );
            }
        }
    }
}
