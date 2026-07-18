<?php

namespace Database\Seeders;

use App\Models\Entrega;
use App\Models\FormulaMedica;
use App\Models\FormulaMedicaItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntregaSeeder extends Seeder
{
    public function run(): void
    {
        $items = FormulaMedicaItem::query()->with('formulaMedica')->orderBy('id')->get();
        $usuarios = User::query()->whereHas('role', fn ($query) => $query->whereIn('nombre', ['despachador', 'administrativo']))->get();

        if ($items->isEmpty() || $usuarios->isEmpty()) {
            return;
        }

        foreach ($items as $item) {
            $cantidadObjetivo = random_int(0, (int) $item->cantidad_formulada);

            if ($cantidadObjetivo === 0) {
                $item->cantidad_entregada = 0;
                $item->estado_item = 'pendiente';
                $item->save();
                continue;
            }

            $cantidadRegistros = random_int(1, min(3, $cantidadObjetivo));
            $restante = $cantidadObjetivo;
            $totalEntregado = 0;

            for ($i = 1; $i <= $cantidadRegistros; $i++) {
                $cantidadEntrega = $i === $cantidadRegistros ? $restante : random_int(1, max(1, $restante - ($cantidadRegistros - $i)));
                $restante -= $cantidadEntrega;
                $fechaBase = $item->formulaMedica?->fecha_formula
                    ? now()->parse($item->formulaMedica->fecha_formula)->startOfDay()
                    : now()->subDays(180);
                $fechaEntrega = (clone $fechaBase)->addDays(random_int(0, 90))->min(now());

                Entrega::query()->updateOrCreate(
                    [
                        'formula_medicamento_id' => $item->id,
                        'fecha_entrega' => $fechaEntrega->toDateString(),
                        'cantidad_entregada' => $cantidadEntrega,
                    ],
                    [
                        'user_id' => $usuarios[random_int(0, $usuarios->count() - 1)]->id,
                        'estado_entrega' => $cantidadEntrega >= $item->cantidad_formulada ? 'entregada' : ['pendiente', 'parcial', 'atendida'][array_rand([0, 1, 2])],
                        'fecha_estimada' => (clone $fechaEntrega)->addDays(random_int(1, 30))->toDateString(),
                        'observaciones' => 'Registro de entrega generado por seeder.',
                        'created_at' => $fechaEntrega,
                        'updated_at' => (clone $fechaEntrega)->addDays(random_int(0, 15))->min(now()),
                    ]
                );

                $totalEntregado += $cantidadEntrega;
            }

            $item->cantidad_entregada = min($totalEntregado, (int) $item->cantidad_formulada);
            $item->estado_item = $item->cantidad_entregada >= (int) $item->cantidad_formulada ? 'entregada' : 'parcial';
            $item->save();
        }

        $formulas = FormulaMedica::query()->with('items')->get();

        foreach ($formulas as $formula) {
            if ($formula->items->isEmpty()) {
                $formula->estado = 'pendiente';
            } elseif ($formula->items->every(fn ($item) => (int) $item->cantidad_entregada >= (int) $item->cantidad_formulada)) {
                $formula->estado = 'entregada';
            } elseif ($formula->items->contains(fn ($item) => (int) $item->cantidad_entregada > 0)) {
                $formula->estado = 'parcial';
            } else {
                $formula->estado = 'pendiente';
            }

            $formula->save();
        }
    }
}
