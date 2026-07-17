<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DateRangeAuditSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = DB::table('pacientes')
            ->selectRaw('MIN(created_at) as min_fecha, MAX(created_at) as max_fecha')
            ->first();

        $formulas = DB::table('formulas_medicas')
            ->selectRaw('MIN(fecha_formula) as min_fecha, MAX(fecha_formula) as max_fecha')
            ->first();

        $entregas = DB::table('entregas')
            ->selectRaw('MIN(fecha_entrega) as min_fecha, MAX(fecha_entrega) as max_fecha')
            ->first();

        $citas = DB::table('citas')
            ->selectRaw('MIN(fecha_cita) as min_fecha, MAX(fecha_cita) as max_fecha')
            ->first();

        $this->command?->info('RANGO pacientes.created_at: ' . ($pacientes->min_fecha ?? 'null') . ' -> ' . ($pacientes->max_fecha ?? 'null'));
        $this->command?->info('RANGO formulas_medicas.fecha_formula: ' . ($formulas->min_fecha ?? 'null') . ' -> ' . ($formulas->max_fecha ?? 'null'));
        $this->command?->info('RANGO entregas.fecha_entrega: ' . ($entregas->min_fecha ?? 'null') . ' -> ' . ($entregas->max_fecha ?? 'null'));
        $this->command?->info('RANGO citas.fecha_cita: ' . ($citas->min_fecha ?? 'null') . ' -> ' . ($citas->max_fecha ?? 'null'));
    }
}
