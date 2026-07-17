<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index(Request $request)
    {
        $filtroFormula = $request->query('formula', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Medicamento::query()->orderBy('nombre');

        if ($filtroFormula === 'si') {
            $query->where('requiere_formula', true);
        }

        if ($filtroFormula === 'no') {
            $query->where('requiere_formula', false);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('codigo', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('nombre', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('principio_activo', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('presentacion', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('concentracion', 'like', "%{$busquedaAplicada}%");
            });
        }

        $medicamentos = $query
            ->paginate(15)
            ->withQueryString();

        return view('medicamentos.index', [
            'medicamentos' => $medicamentos,
            'filtroFormula' => $filtroFormula,
            'busqueda' => $busqueda,
        ]);
    }

    public function create()
    {
        return view('medicamentos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:medicamentos,codigo'],
            'nombre' => ['required', 'string', 'max:120'],
            'principio_activo' => ['nullable', 'string', 'max:120'],
            'presentacion' => ['required', 'string', 'max:80'],
            'concentracion' => ['nullable', 'string', 'max:60'],
            'unidad_medida' => ['nullable', 'string', 'max:30'],
            'requiere_formula' => ['required', 'boolean'],
            'observaciones' => ['nullable', 'string'],
        ]);

        Medicamento::query()->create($validated);

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento registrado correctamente.');
    }

    public function edit(Medicamento $medicamento)
    {
        return view('medicamentos.edit', [
            'medicamento' => $medicamento,
        ]);
    }

    public function update(Request $request, Medicamento $medicamento): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:medicamentos,codigo,' . $medicamento->id],
            'nombre' => ['required', 'string', 'max:120'],
            'principio_activo' => ['nullable', 'string', 'max:120'],
            'presentacion' => ['required', 'string', 'max:80'],
            'concentracion' => ['nullable', 'string', 'max:60'],
            'unidad_medida' => ['nullable', 'string', 'max:30'],
            'requiere_formula' => ['required', 'boolean'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $medicamento->update($validated);

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicamento $medicamento): RedirectResponse
    {
        $tieneInventario = $medicamento->inventarios()->exists();
        $tieneFormula = $medicamento->formulaItems()->exists();

        if ($tieneInventario || $tieneFormula) {
            return redirect()->route('medicamentos.index')->with('error', 'No puedes eliminar el medicamento porque tiene inventario o historial de formulas.');
        }

        $medicamento->delete();

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento eliminado correctamente.');
    }
}