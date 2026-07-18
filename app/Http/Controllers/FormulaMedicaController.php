<?php

namespace App\Http\Controllers;

use App\Models\FormulaMedica;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormulaMedicaController extends Controller
{
    private const ESTADOS = ['pendiente', 'en_validacion', 'parcial', 'entregada', 'vencida'];
    private const MEDICOS_TRATANTES_SUGERIDOS = [
        'Dra. Laura Rios',
        'Dr. Carlos Mena',
        'Dra. Paola Herrera',
        'Dr. Andres Cifuentes',
    ];

    private function pacienteAutenticado(Request $request): ?Paciente
    {
        return Paciente::query()
            ->where('user_id', $request->user()?->id)
            ->first();
    }

    private function pacientesDisponiblesParaUsuario(Request $request)
    {
        if ($request->user()?->hasRole('administrativo')) {
            return Paciente::query()->orderBy('nombres')->get();
        }

        $paciente = $this->pacienteAutenticado($request);

        return $paciente ? collect([$paciente]) : collect();
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', FormulaMedica::class);

        $user = $request->user();
        $rol = $user?->role?->nombre;
        $estado = $request->query('estado', 'todos');
        $pacienteFiltro = $request->query('paciente', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = FormulaMedica::query()
            ->with('paciente.user')
            ->orderByDesc('id');

        if ($rol !== 'administrativo') {
            $query->whereHas('paciente', fn ($subQuery) => $subQuery->where('user_id', $user?->id));
        }

        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        if ($pacienteFiltro !== 'todos' && $rol === 'administrativo') {
            $query->where('paciente_id', $pacienteFiltro);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('numero_formula', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('medico_tratante', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('paciente', function ($pacienteQuery) use ($busquedaAplicada): void {
                        $pacienteQuery->where('nombres', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $formulas = $query->paginate(15)->withQueryString();

        $pacientesDisponibles = $rol === 'administrativo'
            ? Paciente::query()->with('user')->orderBy('nombres')->get()
            : Paciente::query()->with('user')->where('user_id', $user?->id)->orderBy('nombres')->get();

        return view('formulas.index', [
            'rol' => $rol,
            'formulas' => $formulas,
            'estado' => $estado,
            'pacienteFiltro' => $pacienteFiltro,
            'busqueda' => $busqueda,
            'pacientesDisponibles' => $pacientesDisponibles,
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', FormulaMedica::class);

        $pacientes = $this->pacientesDisponiblesParaUsuario($request);

        if ($pacientes->isEmpty()) {
            return redirect()->route('formulas.index')->with('error', 'Tu cuenta no tiene un paciente asociado.');
        }

        return view('formulas.create', [
            'pacientes' => $pacientes,
            'estadosDisponibles' => self::ESTADOS,
            'pacienteBloqueado' => ! $request->user()?->hasRole('administrativo'),
            'medicosTratantes' => self::MEDICOS_TRATANTES_SUGERIDOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', FormulaMedica::class);

        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'numero_formula' => ['required', 'string', 'max:40', 'unique:formulas_medicas,numero_formula'],
            'fecha_formula' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_formula'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        $paciente = Paciente::query()->findOrFail($validated['paciente_id']);

        if (! $request->user()?->hasRole('administrativo') && (int) $paciente->user_id !== (int) $request->user()?->id) {
            abort(403);
        }

        FormulaMedica::query()->create($validated);

        return redirect()->route('formulas.index')->with('success', 'Formula medica registrada correctamente.');
    }

    public function edit(FormulaMedica $formula)
    {
        $this->authorize('update', $formula);

        return view('formulas.edit', [
            'formula' => $formula,
            'pacientes' => request()->user()?->hasRole('administrativo')
                ? Paciente::query()->orderBy('nombres')->get()
                : Paciente::query()->where('user_id', request()->user()?->id)->orderBy('nombres')->get(),
            'estadosDisponibles' => self::ESTADOS,
            'pacienteBloqueado' => ! request()->user()?->hasRole('administrativo'),
            'medicosTratantes' => self::MEDICOS_TRATANTES_SUGERIDOS,
        ]);
    }

    public function update(Request $request, FormulaMedica $formula): RedirectResponse
    {
        $this->authorize('update', $formula);

        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'numero_formula' => ['required', 'string', 'max:40', 'unique:formulas_medicas,numero_formula,' . $formula->id],
            'fecha_formula' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_formula'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        $paciente = Paciente::query()->findOrFail($validated['paciente_id']);

        if (! $request->user()?->hasRole('administrativo') && (int) $paciente->user_id !== (int) $request->user()?->id) {
            abort(403);
        }

        $formula->update($validated);

        return redirect()->route('formulas.index')->with('success', 'Formula medica actualizada correctamente.');
    }

    public function destroy(FormulaMedica $formula): RedirectResponse
    {
        $this->authorize('delete', $formula);

        $formula->delete();

        return redirect()->route('formulas.index')->with('success', 'Formula medica eliminada correctamente.');
    }
}