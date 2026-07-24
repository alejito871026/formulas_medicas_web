@extends('layouts.app', [
    'title' => 'Gestion EPS | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Catalogo EPS</p>
            <p class="module-nav-subtitle">Administra las EPS disponibles para el registro y edicion de pacientes.</p>
        </div>
        <button id="mostrar-form-eps" type="button" class="btn btn-primary">Nueva EPS</button>
    </div>
@endsection

@section('content')
    <section class="space-y-5">
        <section class="entity-form-shell entity-form-floating">
        <article id="panel-form-eps" class="entity-form-card {{ $errors->has('nombre') ? '' : 'hidden' }}">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar nueva EPS</h3>
                <p class="entity-form-subtitle">Registra informacion de contacto para facilitar gestion administrativa y trazabilidad.</p>
            </div>

            <div class="entity-form-body">
                <form method="POST" action="{{ route('eps.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf
                    <div class="field-group">
                        <label class="text-sm font-medium text-slate-700">Nombre EPS</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required minlength="3" maxlength="120" class="input-control w-full">
                        @error('nombre')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="text-sm font-medium text-slate-700">Telefono de contacto</label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}" required maxlength="30" inputmode="numeric" class="input-control w-full" placeholder="Ej: 6013077022">
                        @error('telefono')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="text-sm font-medium text-slate-700">Direccion principal</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}" required maxlength="150" class="input-control w-full" placeholder="Ej: Calle 26 # 69-76, Bogota">
                        @error('direccion')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="text-sm font-medium text-slate-700">Nombre del contacto</label>
                        <input type="text" name="nombre_contacto" value="{{ old('nombre_contacto') }}" required minlength="3" maxlength="120" class="input-control w-full" placeholder="Ej: Mesa de Servicio Nueva EPS">
                        @error('nombre_contacto')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <button type="submit" class="btn btn-teal">Guardar EPS</button>
                    </div>
                </form>
            </div>
        </article>
        </section>

        <article class="module-card">
            <div class="module-toolbar">
                <form id="filtro-eps-form" method="GET" action="{{ route('eps.index') }}" class="module-filter-form">
                    <input id="filtro-eps-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar EPS por nombre (min. 6)" class="input-control filter-search-control">
                    <select id="filtro-eps-estado" name="estado" class="select-control">
                        <option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todas</option>
                        <option value="activas" @selected(($estado ?? 'todos') === 'activas')>Activas</option>
                        <option value="inactivas" @selected(($estado ?? 'todos') === 'inactivas')>Inactivas</option>
                    </select>
                    @if (($estado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
                        <a href="{{ route('eps.index') }}" class="btn btn-muted">Limpiar</a>
                    @endif
                </form>
            </div>

            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Direccion</th>
                            <th>Telefono</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th class="action-col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($epsList as $ep)
                            <tr>
                                <td>
                                    <p class="table-primary-text">{{ $ep->nombre }}</p>
                                </td>
                                <td>{{ $ep->direccion ?: 'Sin direccion' }}</td>
                                <td>{{ $ep->telefono ?: 'Sin telefono' }}</td>
                                <td>{{ $ep->nombre_contacto ?: 'Sin contacto' }}</td>
                                <td>
                                    @if ($ep->activo)
                                        <span class="badge badge-success">Activa</span>
                                    @else
                                        <span class="badge badge-danger">Inactiva</span>
                                    @endif
                                </td>
                                <td class="action-cell">
                                    <div class="table-actions">
                                        <a href="{{ route('eps.edit', $ep) }}" class="btn btn-muted">Editar</a>

                                        <form method="POST" action="{{ route('eps.toggle', $ep) }}" data-feedback-form="true">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning">
                                                {{ $ep->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>

                                        {{-- Boton de eliminar oculto temporalmente --}}
                                        {{--
                                        <form method="POST" action="{{ route('eps.destroy', $ep) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara la EPS. Deseas continuar?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                        --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty">No hay EPS registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="module-footer">
                {{ $epsList->links() }}
            </div>
        </article>
    </section>

    <script>
    (function () {
        const form = document.getElementById('filtro-eps-form');
        const estado = document.getElementById('filtro-eps-estado');
        const busqueda = document.getElementById('filtro-eps-busqueda');
        const mostrarForm = document.getElementById('mostrar-form-eps');
        const panelForm = document.getElementById('panel-form-eps');
        let timer = null;

        if (!form || !estado || !busqueda || !mostrarForm || !panelForm) {
            return;
        }

        mostrarForm.addEventListener('click', () => {
            panelForm.classList.toggle('hidden');
            if (!panelForm.classList.contains('hidden')) {
                panelForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        estado.addEventListener('change', () => {
            form.requestSubmit();
        });

        busqueda.addEventListener('input', () => {
            if (timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(() => {
                const value = busqueda.value.trim();

                if (value.length === 0 || value.length > 5) {
                    form.requestSubmit();
                }
            }, 450);
        });
    })();
    </script>
@endsection
