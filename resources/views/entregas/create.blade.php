@extends('layouts.app', [
    'title' => 'Nueva Entrega | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar entrega</h3>
                <p class="entity-form-subtitle">Registra avance de entrega por item formulado y responsable operativo.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo guardar la entrega.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('entregas.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Item de formula</label>
                        <select name="formula_medicamento_id" required class="select-control w-full">
                            <option value="">Selecciona item</option>
                            @foreach ($itemsFormula as $item)
                                <option value="{{ $item->id }}" @selected((string) old('formula_medicamento_id') === (string) $item->id)>
                                    {{ $item->formulaMedica?->numero_formula }} · {{ $item->formulaMedica?->paciente?->nombres }} {{ $item->formulaMedica?->paciente?->apellidos }} · {{ $item->medicamento?->nombre }} ({{ $item->cantidad_entregada }}/{{ $item->cantidad_formulada }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha de entrega</label>
                        <input type="date" name="fecha_entrega" value="{{ old('fecha_entrega', now()->toDateString()) }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Cantidad entregada</label>
                        <input type="number" name="cantidad_entregada" value="{{ old('cantidad_entregada', 1) }}" min="1" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Estado de entrega</label>
                        <select name="estado_entrega" required class="select-control w-full">
                            @foreach ($estadosDisponibles as $estado)
                                <option value="{{ $estado }}" @selected(old('estado', 'pendiente') === $estado)>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha estimada (opcional)</label>
                        <input type="date" name="fecha_estimada" value="{{ old('fecha_estimada') }}" class="input-control w-full">
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="input-control w-full">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('entregas.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar entrega</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
