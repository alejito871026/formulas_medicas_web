@extends('layouts.app', [
    'title' => 'Editar EPS | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Editar EPS</p>
            <p class="module-nav-subtitle">Actualiza datos de contacto y ubicacion para la entidad seleccionada.</p>
        </div>
        <a href="{{ route('eps.index') }}" class="btn btn-primary">Volver al catalogo</a>
    </div>
@endsection

@section('content')
    <article class="entity-form-card">
        <div class="entity-form-head">
            <h3 class="entity-form-title">Informacion de la EPS</h3>
            <p class="entity-form-subtitle">Mantener estos datos actualizados mejora la gestion operativa y el soporte a pacientes.</p>
        </div>

        <div class="entity-form-body">
            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">No se pudo actualizar la EPS.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('eps.update', $ep) }}" data-feedback-form="true" class="entity-form-grid md:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label class="text-sm font-medium text-slate-700">Nombre EPS</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $ep->nombre) }}" required minlength="3" maxlength="120" class="input-control w-full">
                </div>

                <div class="field-group">
                    <label class="text-sm font-medium text-slate-700">Telefono de contacto</label>
                    <input type="tel" name="telefono" value="{{ old('telefono', $ep->telefono) }}" required maxlength="30" inputmode="numeric" class="input-control w-full">
                </div>

                <div class="field-group md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Direccion principal</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $ep->direccion) }}" required maxlength="150" class="input-control w-full">
                </div>

                <div class="field-group md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Nombre del contacto</label>
                    <input type="text" name="nombre_contacto" value="{{ old('nombre_contacto', $ep->nombre_contacto) }}" required minlength="3" maxlength="120" class="input-control w-full">
                </div>

                <div class="entity-form-actions md:col-span-2">
                    <a href="{{ route('eps.index') }}" class="btn btn-muted">Cancelar</a>
                    <button type="submit" class="btn btn-teal">Guardar cambios</button>
                </div>
            </form>
        </div>
    </article>
@endsection
