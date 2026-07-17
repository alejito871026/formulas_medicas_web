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

        $pacientesDisponibles = Paciente::query()
            ->with('user')
            ->orderBy('nombres')
            ->get();

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

    public function create()
    {
        return view('formulas.create', [
            'pacientes' => Paciente::query()->orderBy('nombres')->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'numero_formula' => ['required', 'string', 'max:40', 'unique:formulas_medicas,numero_formula'],
            'fecha_formula' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_formula'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        FormulaMedica::query()->create($validated);

        return redirect()->route('formulas.index')->with('success', 'Formula medica registrada correctamente.');
    }

    public function edit(FormulaMedica $formula)
    {
        return view('formulas.edit', [
            'formula' => $formula,
            'pacientes' => Paciente::query()->orderBy('nombres')->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function update(Request $request, FormulaMedica $formula): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'numero_formula' => ['required', 'string', 'max:40', 'unique:formulas_medicas,numero_formula,' . $formula->id],
            'fecha_formula' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_formula'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => ['nullable', 'string'],
        ]);

        $formula->update($validated);

        return redirect()->route('formulas.index')->with('success', 'Formula medica actualizada correctamente.');
    }

    public function destroy(FormulaMedica $formula): RedirectResponse
    {
        $formula->delete();

        return redirect()->route('formulas.index')->with('success', 'Formula medica eliminada correctamente.');
    }
}