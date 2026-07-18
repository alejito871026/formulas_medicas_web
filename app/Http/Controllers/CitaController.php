<?php

namespace App\Http\Controllers;

use App\Events\CitaEstadoActualizado;
use App\Models\Cita;
use App\Models\FormulaMedica;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitaController extends Controller
{
    private const ESTADOS = ['programada', 'confirmada', 'reprogramada', 'cancelada', 'atendida', 'no_asistio'];
    private const PDF_MAX_ROWS = 120;

    public function index(Request $request)
    {
        $user = $request->user();
        $rol = $user?->role?->nombre;
        $estado = $request->query('estado', 'todos');
        $motivo = $request->query('motivo', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Cita::query()
            ->with(['paciente.user', 'formulaMedica'])
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_cita');

        if ($rol !== 'administrativo') {
            $query->whereHas('paciente', fn ($subQuery) => $subQuery->where('user_id', $user?->id));
        }

        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        if ($motivo !== 'todos') {
            $query->where('motivo', $motivo);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('motivo', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('formulaMedica', fn ($formulaQuery) => $formulaQuery->where('numero_formula', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('paciente', function ($pacienteQuery) use ($busquedaAplicada): void {
                        $pacienteQuery->where('nombres', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $citas = $query->paginate(15)->withQueryString();

        $motivosDisponibles = Cita::query()
            ->select('motivo')
            ->distinct()
            ->orderBy('motivo')
            ->pluck('motivo')
            ->filter()
            ->values();

        $metricas = [
            'programadas' => (int) Cita::query()->where('estado', 'programada')->count(),
            'confirmadas' => (int) Cita::query()->where('estado', 'confirmada')->count(),
            'atendidas_mes' => (int) Cita::query()->where('estado', 'atendida')->whereMonth('fecha_cita', now()->month)->count(),
            'reprogramadas' => (int) Cita::query()->where('estado', 'reprogramada')->count(),
        ];

        return view('citas.index', [
            'rol' => $rol,
            'citas' => $citas,
            'estado' => $estado,
            'motivo' => $motivo,
            'busqueda' => $busqueda,
            'estadosDisponibles' => self::ESTADOS,
            'motivosDisponibles' => $motivosDisponibles,
            'metricas' => $metricas,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $rol = $user?->role?->nombre;
        $estado = $request->query('estado', 'todos');
        $motivo = $request->query('motivo', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Cita::query()
            ->select(['id', 'paciente_id', 'formula_medica_id', 'fecha_cita', 'hora_cita', 'motivo', 'estado', 'observaciones'])
            ->with([
                'paciente:id,nombres,apellidos,numero_documento,user_id',
                'paciente.user:id,name',
                'formulaMedica:id,numero_formula',
            ])
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_cita');

        if ($rol !== 'administrativo') {
            $query->whereHas('paciente', fn ($subQuery) => $subQuery->where('user_id', $user?->id));
        }

        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        if ($motivo !== 'todos') {
            $query->where('motivo', $motivo);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('motivo', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('formulaMedica', fn ($formulaQuery) => $formulaQuery->where('numero_formula', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('paciente', function ($pacienteQuery) use ($busquedaAplicada): void {
                        $pacienteQuery->where('nombres', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $totalCitas = (clone $query)->count();
        $citas = $query->limit(self::PDF_MAX_ROWS)->get();

        $pdf = app('dompdf.wrapper')->loadView('pdf.citas', [
            'citas' => $citas,
            'totalCitas' => $totalCitas,
            'estado' => $estado,
            'motivo' => $motivo,
            'busqueda' => $busqueda,
            'usuario' => $user,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('listado-citas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        return view('citas.create', [
            'pacientes' => Paciente::query()->orderBy('nombres')->get(),
            'formulas' => FormulaMedica::query()->orderByDesc('id')->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'formula_medica_id' => ['nullable', Rule::exists('formulas_medicas', 'id')],
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:80'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        if (! empty($validated['formula_medica_id'])) {
            $formula = FormulaMedica::query()->find($validated['formula_medica_id']);

            if (! $formula || (int) $formula->paciente_id !== (int) $validated['paciente_id']) {
                return back()->withErrors([
                    'formula_medica_id' => 'La formula seleccionada no pertenece al paciente.',
                ])->withInput();
            }
        }

        Cita::query()->create($validated);

        return redirect()->route('citas.index')->with('success', 'Cita registrada correctamente.');
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', [
            'cita' => $cita,
            'pacientes' => Paciente::query()->orderBy('nombres')->get(),
            'formulas' => FormulaMedica::query()->orderByDesc('id')->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $estadoAnterior = $cita->estado;

        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'formula_medica_id' => ['nullable', Rule::exists('formulas_medicas', 'id')],
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:80'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        if (! empty($validated['formula_medica_id'])) {
            $formula = FormulaMedica::query()->find($validated['formula_medica_id']);

            if (! $formula || (int) $formula->paciente_id !== (int) $validated['paciente_id']) {
                return back()->withErrors([
                    'formula_medica_id' => 'La formula seleccionada no pertenece al paciente.',
                ])->withInput();
            }
        }

        $cita->update($validated);

        if ($estadoAnterior !== $cita->estado) {
            event(new CitaEstadoActualizado(
                $cita->fresh(['paciente.user', 'formulaMedica']),
                $estadoAnterior,
                $cita->estado,
                $request->user(),
            ));
        }

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita): RedirectResponse
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('success', 'Cita eliminada correctamente.');
    }
}