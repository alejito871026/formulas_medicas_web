<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Medicamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $filtroEstado = $request->query('estado', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Inventario::query()
            ->with('medicamento')
            ->orderByDesc('id');

        if ($filtroEstado === 'bajo_stock') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        if ($filtroEstado === 'vencido') {
            $query->whereNotNull('fecha_vencimiento')->whereDate('fecha_vencimiento', '<', now()->toDateString());
        }

        if ($filtroEstado === 'por_vencer') {
            $query->whereNotNull('fecha_vencimiento')
                ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
                ->whereDate('fecha_vencimiento', '<=', now()->addDays(60)->toDateString());
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('lote', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('ubicacion', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('medicamento', function ($medQuery) use ($busquedaAplicada): void {
                        $medQuery->where('codigo', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('nombre', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $inventarios = $query
            ->paginate(15)
            ->withQueryString();

        return view('inventarios.index', [
            'inventarios' => $inventarios,
            'filtroEstado' => $filtroEstado,
            'busqueda' => $busqueda,
        ]);
    }

    public function create()
    {
        return view('inventarios.create', [
            'medicamentos' => Medicamento::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'medicamento_id' => ['required', Rule::exists('medicamentos', 'id')],
            'lote' => [
                'required',
                'string',
                'max:40',
                Rule::unique('inventarios', 'lote')->where(fn ($query) => $query->where('medicamento_id', $request->input('medicamento_id'))),
            ],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'ubicacion' => ['nullable', 'string', 'max:80'],
        ]);

        Inventario::query()->create($validated);

        return redirect()->route('inventarios.index')->with('success', 'Lote de inventario registrado correctamente.');
    }

    public function edit(Inventario $inventario)
    {
        return view('inventarios.edit', [
            'inventario' => $inventario,
            'medicamentos' => Medicamento::query()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Inventario $inventario): RedirectResponse
    {
        $validated = $request->validate([
            'medicamento_id' => ['required', Rule::exists('medicamentos', 'id')],
            'lote' => [
                'required',
                'string',
                'max:40',
                Rule::unique('inventarios', 'lote')
                    ->ignore($inventario->id)
                    ->where(fn ($query) => $query->where('medicamento_id', $request->input('medicamento_id'))),
            ],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'ubicacion' => ['nullable', 'string', 'max:80'],
        ]);

        $inventario->update($validated);

        return redirect()->route('inventarios.index')->with('success', 'Inventario actualizado correctamente.');
    }

    public function destroy(Inventario $inventario): RedirectResponse
    {
        $inventario->delete();

        return redirect()->route('inventarios.index')->with('success', 'Lote eliminado correctamente.');
    }
}