<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Entrega;
use App\Models\FormulaMedica;
use App\Models\FormulaMedicaItem;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BackfillLastSixMonthsSeeder extends Seeder
{
    private function randomDate(Carbon $start, Carbon $end): Carbon
    {
        $startTs = $start->timestamp;
        $endTs = max($startTs, $end->timestamp);

        return Carbon::createFromTimestamp(random_int($startTs, $endTs));
    }

    public function run(): void
    {
        $inicio = now()->subMonths(6)->startOfDay();
        $fin = now()->endOfDay();

        Paciente::query()->chunkById(200, function ($pacientes) use ($inicio, $fin): void {
            foreach ($pacientes as $paciente) {
                $fecha = $this->randomDate(clone $inicio, clone $fin);
                $paciente->created_at = $fecha;
                $paciente->updated_at = (clone $fecha)->addDays(random_int(0, 30))->min(now());
                $paciente->save();
            }
        });

        FormulaMedica::query()->with('paciente')->chunkById(200, function ($formulas) use ($inicio, $fin): void {
            foreach ($formulas as $formula) {
                $base = $formula->paciente?->created_at ? Carbon::parse($formula->paciente->created_at) : clone $inicio;
                if ($base->lt($inicio)) {
                    $base = clone $inicio;
                }
                if ($base->gt($fin)) {
                    $base = clone $inicio;
                }

                $fechaFormula = $this->randomDate($base->copy()->startOfDay(), clone $fin);
                $formula->fecha_formula = $fechaFormula->toDateString();
                $formula->fecha_vencimiento = (clone $fechaFormula)->addDays(random_int(30, 180))->toDateString();
                $formula->created_at = $fechaFormula;
                $formula->updated_at = (clone $fechaFormula)->addDays(random_int(0, 25))->min(now());
                $formula->save();
            }
        });

        FormulaMedicaItem::query()->with('formulaMedica')->chunkById(400, function ($items) use ($inicio): void {
            foreach ($items as $item) {
                $formulaFecha = $item->formulaMedica?->fecha_formula
                    ? Carbon::parse($item->formulaMedica->fecha_formula)
                    : now()->subDays(180);

                if ($formulaFecha->lt($inicio)) {
                    $formulaFecha = clone $inicio;
                }

                $fecha = (clone $formulaFecha)->addDays(random_int(0, 20))->min(now());
                $item->created_at = $fecha;
                $item->updated_at = (clone $fecha)->addDays(random_int(0, 15))->min(now());
                $item->save();
            }
        });

        Entrega::query()->with('formulaItem.formulaMedica')->chunkById(400, function ($entregas) use ($inicio, $fin): void {
            foreach ($entregas as $entrega) {
                $formulaFecha = $entrega->formulaItem?->formulaMedica?->fecha_formula
                    ? Carbon::parse($entrega->formulaItem->formulaMedica->fecha_formula)
                    : clone $inicio;

                if ($formulaFecha->lt($inicio)) {
                    $formulaFecha = clone $inicio;
                }

                $fechaEntrega = $this->randomDate($formulaFecha->copy()->startOfDay(), clone $fin);
                $entrega->fecha_entrega = $fechaEntrega->toDateString();
                $entrega->fecha_estimada = (clone $fechaEntrega)->addDays(random_int(1, 30))->toDateString();
                $entrega->created_at = $fechaEntrega;
                $entrega->updated_at = (clone $fechaEntrega)->addDays(random_int(0, 15))->min(now());
                $entrega->save();
            }
        });

        Cita::query()->chunkById(300, function ($citas) use ($inicio, $fin): void {
            foreach ($citas as $cita) {
                $fecha = $this->randomDate(clone $inicio, clone $fin);
                $cita->fecha_cita = $fecha->toDateString();
                $cita->created_at = $fecha;
                $cita->updated_at = (clone $fecha)->addDays(random_int(0, 20))->min(now());
                $cita->save();
            }
        });
    }
}
