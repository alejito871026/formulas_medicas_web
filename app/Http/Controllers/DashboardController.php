<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $rol = $user?->role?->nombre ?? 'sin_rol';
        $startRange = Carbon::now()->startOfMonth()->subMonths(5);

        $months = collect(range(5, 0))->map(function (int $offset) {
            return Carbon::now()->startOfMonth()->subMonths($offset);
        });

        $labelsMes = $months->map(fn (Carbon $month): string => $month->translatedFormat('M Y'))->values()->all();

        $clientesCrudos = DB::table('pacientes')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->where('created_at', '>=', $startRange)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $atencionesCrudas = DB::table('entregas')
            ->selectRaw("DATE_FORMAT(fecha_entrega, '%Y-%m') as ym, COUNT(*) as total")
            ->where('fecha_entrega', '>=', $startRange)
            ->whereIn('estado_entrega', ['entregada', 'completa', 'atendida'])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $clientesPorMes = $months->map(function (Carbon $month) use ($clientesCrudos): int {
            return (int) ($clientesCrudos[$month->format('Y-m')] ?? 0);
        })->values()->all();

        $atencionesPorMes = $months->map(function (Carbon $month) use ($atencionesCrudas): int {
            return (int) ($atencionesCrudas[$month->format('Y-m')] ?? 0);
        })->values()->all();

        $medicamentosTop = DB::table('formula_medicamento')
            ->join('medicamentos', 'medicamentos.id', '=', 'formula_medicamento.medicamento_id')
            ->selectRaw('medicamentos.nombre as medicamento, SUM(formula_medicamento.cantidad_formulada) as total')
            ->groupBy('medicamentos.nombre')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        $topMedicamentosLabels = $medicamentosTop->pluck('medicamento')->values()->all();
        $topMedicamentosValores = $medicamentosTop->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

        $totalClientes = (int) DB::table('pacientes')->count();
        $totalAtenciones = (int) DB::table('entregas')->whereIn('estado_entrega', ['entregada', 'completa', 'atendida'])->count();
        $totalItemsFormulados = (int) DB::table('formula_medicamento')->count();
        $totalMedicamentos = (int) DB::table('medicamentos')->count();

        $entregasMesCrudas = DB::table('entregas')
            ->selectRaw("DATE_FORMAT(fecha_entrega, '%Y-%m') as ym, COUNT(*) as total")
            ->where('fecha_entrega', '>=', $startRange)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $entregasPorMes = $months->map(function (Carbon $month) use ($entregasMesCrudas): int {
            return (int) ($entregasMesCrudas[$month->format('Y-m')] ?? 0);
        })->values()->all();

        $despachoTop = DB::table('entregas')
            ->join('formula_medicamento', 'formula_medicamento.id', '=', 'entregas.formula_medicamento_id')
            ->join('medicamentos', 'medicamentos.id', '=', 'formula_medicamento.medicamento_id')
            ->selectRaw('medicamentos.nombre as medicamento, SUM(entregas.cantidad_entregada) as total')
            ->groupBy('medicamentos.nombre')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        $despachoTopLabels = $despachoTop->pluck('medicamento')->values()->all();
        $despachoTopValores = $despachoTop->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

        $entregasPendientes = (int) DB::table('entregas')->whereIn('estado_entrega', ['pendiente', 'parcial'])->count();
        $entregasCompletadasMes = (int) DB::table('entregas')
            ->whereBetween('fecha_entrega', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->whereIn('estado_entrega', ['entregada', 'completa', 'atendida'])
            ->count();

        $inventarioBajoStock = (int) DB::table('inventarios')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();

        $inventarioPorVencer = (int) DB::table('inventarios')
            ->whereDate('fecha_vencimiento', '>=', Carbon::today())
            ->whereDate('fecha_vencimiento', '<=', Carbon::today()->addDays(30))
            ->count();

        $pacienteId = (int) DB::table('pacientes')
            ->where('user_id', $user?->id)
            ->value('id');

        $misFormulas = 0;
        $misFormulasActivas = 0;
        $misEntregas = 0;
        $misCitasPendientes = 0;
        $misFormulasPorMes = array_fill(0, 6, 0);
        $misCitasPorMes = array_fill(0, 6, 0);
        $proximaCita = null;

        if ($pacienteId > 0) {
            $misFormulas = (int) DB::table('formulas_medicas')->where('paciente_id', $pacienteId)->count();
            $misFormulasActivas = (int) DB::table('formulas_medicas')
                ->where('paciente_id', $pacienteId)
                ->whereIn('estado', ['pendiente', 'en_validacion', 'parcial'])
                ->count();

            $misEntregas = (int) DB::table('entregas')
                ->join('formula_medicamento', 'formula_medicamento.id', '=', 'entregas.formula_medicamento_id')
                ->join('formulas_medicas', 'formulas_medicas.id', '=', 'formula_medicamento.formula_medica_id')
                ->where('formulas_medicas.paciente_id', $pacienteId)
                ->whereIn('entregas.estado_entrega', ['entregada', 'completa', 'atendida'])
                ->count();

            $misCitasPendientes = (int) DB::table('citas')
                ->where('paciente_id', $pacienteId)
                ->whereIn('estado', ['programada', 'confirmada', 'reprogramada'])
                ->count();

            $formulasMesCrudas = DB::table('formulas_medicas')
                ->selectRaw("DATE_FORMAT(fecha_formula, '%Y-%m') as ym, COUNT(*) as total")
                ->where('paciente_id', $pacienteId)
                ->where('fecha_formula', '>=', $startRange)
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $citasMesCrudas = DB::table('citas')
                ->selectRaw("DATE_FORMAT(fecha_cita, '%Y-%m') as ym, COUNT(*) as total")
                ->where('paciente_id', $pacienteId)
                ->where('fecha_cita', '>=', $startRange)
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $misFormulasPorMes = $months->map(function (Carbon $month) use ($formulasMesCrudas): int {
                return (int) ($formulasMesCrudas[$month->format('Y-m')] ?? 0);
            })->values()->all();

            $misCitasPorMes = $months->map(function (Carbon $month) use ($citasMesCrudas): int {
                return (int) ($citasMesCrudas[$month->format('Y-m')] ?? 0);
            })->values()->all();

            $proximaCita = DB::table('citas')
                ->where('paciente_id', $pacienteId)
                ->whereDate('fecha_cita', '>=', Carbon::today())
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])
                ->orderBy('fecha_cita')
                ->orderBy('hora_cita')
                ->first(['fecha_cita', 'hora_cita', 'estado']);
        }

        return view('dashboard.index', [
            'rol' => $rol,
            'labelsMes' => $labelsMes,
            'clientesPorMes' => $clientesPorMes,
            'atencionesPorMes' => $atencionesPorMes,
            'topMedicamentosLabels' => $topMedicamentosLabels,
            'topMedicamentosValores' => $topMedicamentosValores,
            'totalClientes' => $totalClientes,
            'totalAtenciones' => $totalAtenciones,
            'totalItemsFormulados' => $totalItemsFormulados,
            'totalMedicamentos' => $totalMedicamentos,
            'entregasPorMes' => $entregasPorMes,
            'despachoTopLabels' => $despachoTopLabels,
            'despachoTopValores' => $despachoTopValores,
            'entregasPendientes' => $entregasPendientes,
            'entregasCompletadasMes' => $entregasCompletadasMes,
            'inventarioBajoStock' => $inventarioBajoStock,
            'inventarioPorVencer' => $inventarioPorVencer,
            'misFormulas' => $misFormulas,
            'misFormulasActivas' => $misFormulasActivas,
            'misEntregas' => $misEntregas,
            'misCitasPendientes' => $misCitasPendientes,
            'misFormulasPorMes' => $misFormulasPorMes,
            'misCitasPorMes' => $misCitasPorMes,
            'proximaCita' => $proximaCita,
        ]);
    }
}