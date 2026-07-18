@extends('layouts.app', [
    'title' => 'Nuevo Lote de Inventario | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar lote de inventario</h3>
                <p class="entity-form-subtitle">Asocia medicamento, lote y niveles de stock para control de dispensacion.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo guardar el lote.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('inventarios.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Medicamento</label>
                        <select name="medicamento_id" required class="select-control w-full">
                            <option value="">Selecciona un medicamento</option>
                            @foreach ($medicamentos as $medicamento)
                                <option value="{{ $medicamento->id }}" @selected((string) old('medicamento_id') === (string) $medicamento->id)>
                                    {{ $medicamento->codigo }} · {{ $medicamento->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Lote</label>
                        <input type="text" name="lote" value="{{ old('lote') }}" required maxlength="40" class="input-control w-full" placeholder="Ej: LOTE-2026-001">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Stock actual</label>
                        <input type="number" name="stock_actual" value="{{ old('stock_actual', 0) }}" min="0" step="1" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Stock minimo</label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" min="0" step="1" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Ubicacion</label>
                        <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" maxlength="80" class="input-control w-full" placeholder="Ej: Estante B2">
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('inventarios.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar lote</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
