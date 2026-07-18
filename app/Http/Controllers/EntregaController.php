<?php

namespace App\Http\Controllers;

use App\Events\EntregaEstadoActualizado;
use App\Models\Entrega;
use App\Models\FormulaMedicaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntregaController extends Controller
{
    private const ESTADOS = ['pendiente', 'parcial', 'entregada', 'completa', 'atendida', 'cancelada'];
    private const PDF_MAX_ROWS = 120;

    public function index(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Entrega::query()
            ->with(['formulaItem.formulaMedica.paciente', 'formulaItem.medicamento', 'user'])
            ->orderByDesc('id');

        if ($estado !== 'todos') {
            $query->where('estado_entrega', $estado);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('estado_entrega', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('formulaItem.formulaMedica', fn ($formulaQuery) => $formulaQuery->where('numero_formula', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('formulaItem.medicamento', fn ($medQuery) => $medQuery->where('nombre', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('formulaItem.formulaMedica.paciente', function ($pacienteQuery) use ($busquedaAplicada): void {
                        $pacienteQuery->where('nombres', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $entregas = $query->paginate(15)->withQueryString();

        return view('entregas.index', [
            'entregas' => $entregas,
            'estado' => $estado,
            'busqueda' => $busqueda,
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = Entrega::query()
            ->select(['id', 'formula_medicamento_id', 'user_id', 'fecha_entrega', 'fecha_estimada', 'cantidad_entregada', 'estado_entrega'])
            ->with([
                'formulaItem:id,formula_medica_id,medicamento_id',
                'formulaItem.formulaMedica:id,numero_formula,paciente_id',
                'formulaItem.formulaMedica.paciente:id,nombres,apellidos',
                'formulaItem.medicamento:id,nombre',
                'user:id,name',
            ])
            ->orderByDesc('id');

        if ($estado !== 'todos') {
            $query->where('estado_entrega', $estado);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('estado_entrega', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('formulaItem.formulaMedica', fn ($formulaQuery) => $formulaQuery->where('numero_formula', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('formulaItem.medicamento', fn ($medQuery) => $medQuery->where('nombre', 'like', "%{$busquedaAplicada}%"))
                    ->orWhereHas('formulaItem.formulaMedica.paciente', function ($pacienteQuery) use ($busquedaAplicada): void {
                        $pacienteQuery->where('nombres', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $totalEntregas = (clone $query)->count();
        $entregas = $query->limit(self::PDF_MAX_ROWS)->get();

        $pdf = app('dompdf.wrapper')->loadView('pdf.entregas', [
            'entregas' => $entregas,
            'totalEntregas' => $totalEntregas,
            'estado' => $estado,
            'busqueda' => $busqueda,
            'usuario' => $request->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('listado-entregas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        return view('entregas.create', [
            'itemsFormula' => FormulaMedicaItem::query()
                ->with(['formulaMedica.paciente', 'medicamento'])
                ->orderByDesc('id')
                ->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'formula_medicamento_id' => ['required', Rule::exists('formula_medicamento', 'id')],
            'fecha_entrega' => ['required', 'date'],
            'cantidad_entregada' => ['required', 'integer', 'min:1'],
            'estado_entrega' => ['required', Rule::in(self::ESTADOS)],
            'fecha_estimada' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $item = FormulaMedicaItem::query()->findOrFail($validated['formula_medicamento_id']);

        $sumatoria = Entrega::query()
            ->where('formula_medicamento_id', $item->id)
            ->sum('cantidad_entregada');

        if (($sumatoria + (int) $validated['cantidad_entregada']) > $item->cantidad_formulada) {
            return back()->withErrors([
                'cantidad_entregada' => 'La cantidad excede lo formulado para este item.',
            ])->withInput();
        }

        $entrega = Entrega::query()->create([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        $this->sincronizarItemYFormula($item->id);

        return redirect()->route('entregas.index')->with('success', 'Entrega registrada correctamente.');
    }

    public function edit(Entrega $entrega)
    {
        return view('entregas.edit', [
            'entrega' => $entrega,
            'itemsFormula' => FormulaMedicaItem::query()
                ->with(['formulaMedica.paciente', 'medicamento'])
                ->orderByDesc('id')
                ->get(),
            'estadosDisponibles' => self::ESTADOS,
        ]);
    }

    public function update(Request $request, Entrega $entrega): RedirectResponse
    {
        $estadoAnterior = $entrega->estado_entrega;

        $validated = $request->validate([
            'formula_medicamento_id' => ['required', Rule::exists('formula_medicamento', 'id')],
            'fecha_entrega' => ['required', 'date'],
            'cantidad_entregada' => ['required', 'integer', 'min:1'],
            'estado_entrega' => ['required', Rule::in(self::ESTADOS)],
            'fecha_estimada' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $item = FormulaMedicaItem::query()->findOrFail($validated['formula_medicamento_id']);

        $sumatoriaSinActual = Entrega::query()
            ->where('formula_medicamento_id', $item->id)
            ->where('id', '!=', $entrega->id)
            ->sum('cantidad_entregada');

        if (($sumatoriaSinActual + (int) $validated['cantidad_entregada']) > $item->cantidad_formulada) {
            return back()->withErrors([
                'cantidad_entregada' => 'La cantidad excede lo formulado para este item.',
            ])->withInput();
        }

        $itemAnteriorId = $entrega->formula_medicamento_id;

        $entrega->update([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        $this->sincronizarItemYFormula($validated['formula_medicamento_id']);

        if ((int) $itemAnteriorId !== (int) $validated['formula_medicamento_id']) {
            $this->sincronizarItemYFormula($itemAnteriorId);
        }

        if ($estadoAnterior !== $entrega->estado_entrega) {
            event(new EntregaEstadoActualizado(
                $entrega->fresh(['formulaItem.formulaMedica.paciente.user', 'formulaItem.medicamento']),
                $estadoAnterior,
                $entrega->estado_entrega,
                $request->user(),
            ));
        }

        return redirect()->route('entregas.index')->with('success', 'Entrega actualizada correctamente.');
    }

    public function destroy(Entrega $entrega): RedirectResponse
    {
        $itemId = $entrega->formula_medicamento_id;
        $entrega->delete();

        $this->sincronizarItemYFormula($itemId);

        return redirect()->route('entregas.index')->with('success', 'Entrega eliminada correctamente.');
    }

    private function sincronizarItemYFormula(int $formulaItemId): void
    {
        $item = FormulaMedicaItem::query()->with('formulaMedica.items')->find($formulaItemId);

        if (! $item) {
            return;
        }

        $totalEntregado = Entrega::query()
            ->where('formula_medicamento_id', $item->id)
            ->sum('cantidad_entregada');

        $cantidadEntregada = min((int) $totalEntregado, (int) $item->cantidad_formulada);
        $item->cantidad_entregada = $cantidadEntregada;

        if ($cantidadEntregada <= 0) {
            $item->estado_item = 'pendiente';
        } elseif ($cantidadEntregada < (int) $item->cantidad_formulada) {
            $item->estado_item = 'parcial';
        } else {
            $item->estado_item = 'entregada';
        }

        $item->save();

        $formula = $item->formulaMedica;

        if (! $formula) {
            return;
        }

        $items = $formula->items;

        if ($items->isEmpty()) {
            $formula->estado = 'pendiente';
        } elseif ($items->every(fn ($formulaItem) => (int) $formulaItem->cantidad_entregada >= (int) $formulaItem->cantidad_formulada)) {
            $formula->estado = 'entregada';
        } elseif ($items->contains(fn ($formulaItem) => (int) $formulaItem->cantidad_entregada > 0)) {
            $formula->estado = 'parcial';
        } else {
            $formula->estado = 'pendiente';
        }

        $formula->save();
    }
}