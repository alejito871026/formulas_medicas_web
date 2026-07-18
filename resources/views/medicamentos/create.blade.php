@extends('layouts.app', [
    'title' => 'Nuevo Medicamento | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar medicamento</h3>
                <p class="entity-form-subtitle">Completa informacion farmacologica para habilitar formulas, inventario y entregas.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo guardar el medicamento.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('medicamentos.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Codigo</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required maxlength="30" class="input-control w-full" placeholder="Ej: MED-00123">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required minlength="3" maxlength="120" class="input-control w-full" placeholder="Ej: Acetaminofen">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Principio activo</label>
                        <input type="text" name="principio_activo" value="{{ old('principio_activo') }}" maxlength="120" class="input-control w-full" placeholder="Ej: Paracetamol">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Presentacion</label>
                        <input type="text" name="presentacion" value="{{ old('presentacion') }}" required maxlength="80" class="input-control w-full" placeholder="Ej: Tableta">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Concentracion</label>
                        <input type="text" name="concentracion" value="{{ old('concentracion') }}" maxlength="60" class="input-control w-full" placeholder="Ej: 500 mg">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Unidad de medida</label>
                        <input type="text" name="unidad_medida" value="{{ old('unidad_medida') }}" maxlength="30" class="input-control w-full" placeholder="Ej: mg, ml, UI">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Requiere formula</label>
                        <select name="requiere_formula" required class="select-control w-full">
                            <option value="1" @selected(old('requiere_formula', '1') === '1')>Si</option>
                            <option value="0" @selected(old('requiere_formula') === '0')>No</option>
                        </select>
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="input-control w-full" placeholder="Notas de conservacion, restricciones o presentacion institucional">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('medicamentos.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar medicamento</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
