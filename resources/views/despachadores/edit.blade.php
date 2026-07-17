@extends('layouts.app', [
    'title' => 'Editar Despachador | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Editar despachador</p>
            <p class="module-nav-subtitle">Actualiza datos de acceso del usuario operativo.</p>
        </div>
        <a href="{{ route('despachadores.index') }}" class="btn btn-primary">Volver al listado</a>
    </div>
@endsection

@section('content')
    <article class="entity-form-card">
        <div class="entity-form-head">
            <h3 class="entity-form-title">Informacion de la cuenta</h3>
            <p class="entity-form-subtitle">Puedes actualizar nombre, correo y contrasena del despachador.</p>
        </div>

        <div class="entity-form-body">
            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">No se pudo actualizar el despachador.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('despachadores.update', $despachador) }}" data-feedback-form="true" class="entity-form-grid md:grid-cols-2">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $despachador->name) }}" required class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $despachador->email) }}" required class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nueva contrasena (opcional)</label>
                    <input type="password" name="password" class="input-control w-full">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Confirmar nueva contrasena</label>
                    <input type="password" name="password_confirmation" class="input-control w-full">
                </div>

                <div class="entity-form-actions md:col-span-2">
                    <a href="{{ route('despachadores.index') }}" class="btn btn-muted">Cancelar</a>
                    <button type="submit" class="btn btn-teal">Actualizar despachador</button>
                </div>
            </form>
        </div>
    </article>
@endsection
