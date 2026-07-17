@extends('layouts.app', [
    'title' => 'Nuevo Despachador | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Nuevo despachador</p>
            <p class="module-nav-subtitle">Crea una cuenta operativa para gestionar entregas de medicamentos.</p>
        </div>
        <a href="{{ route('despachadores.index') }}" class="btn btn-primary">Volver al listado</a>
    </div>
@endsection

@section('content')
    <article class="entity-form-card">
        <div class="entity-form-head">
            <h3 class="entity-form-title">Datos de acceso</h3>
            <p class="entity-form-subtitle">El usuario quedara creado con rol despachador y estado activo.</p>
        </div>

        <div class="entity-form-body">
            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">No se pudo crear el despachador.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('despachadores.store') }}" data-feedback-form="true" class="entity-form-grid md:grid-cols-2">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Contrasena</label>
                    <input type="password" name="password" required class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Confirmar contrasena</label>
                    <input type="password" name="password_confirmation" required class="input-control w-full">
                </div>

                <div class="entity-form-actions md:col-span-2">
                    <a href="{{ route('despachadores.index') }}" class="btn btn-muted">Cancelar</a>
                    <button type="submit" class="btn btn-teal">Guardar despachador</button>
                </div>
            </form>
        </div>
    </article>
@endsection
