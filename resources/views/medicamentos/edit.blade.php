@extends('layouts.app', [
    'title' => 'Editar Medicamento | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Actualizar medicamento</h3>
                <p class="entity-form-subtitle">Ajusta ficha tecnica y reglas de dispensacion del medicamento.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo actualizar el medicamento.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('medicamentos.update', $medicamento) }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Codigo</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $medicamento->codigo) }}" required maxlength="30" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $medicamento->nombre) }}" required minlength="3" maxlength="120" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Principio activo</label>
                        <input type="text" name="principio_activo" value="{{ old('principio_activo', $medicamento->principio_activo) }}" maxlength="120" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Presentacion</label>
                        <input type="text" name="presentacion" value="{{ old('presentacion', $medicamento->presentacion) }}" required maxlength="80" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Concentracion</label>
                        <input type="text" name="concentracion" value="{{ old('concentracion', $medicamento->concentracion) }}" maxlength="60" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Unidad de medida</label>
                        <input type="text" name="unidad_medida" value="{{ old('unidad_medida', $medicamento->unidad_medida) }}" maxlength="30" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Requiere formula</label>
                        <select name="requiere_formula" required class="select-control w-full">
                            <option value="1" @selected((string) old('requiere_formula', $medicamento->requiere_formula ? '1' : '0') === '1')>Si</option>
                            <option value="0" @selected((string) old('requiere_formula', $medicamento->requiere_formula ? '1' : '0') === '0')>No</option>
                        </select>
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="input-control w-full">{{ old('observaciones', $medicamento->observaciones) }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('medicamentos.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Actualizar medicamento</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
