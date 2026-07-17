@extends('layouts.app', [
    'title' => 'Gestion de Despachadores | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Despachadores</p>
            <p class="module-nav-subtitle">Administra cuentas del personal encargado de entregar medicamentos.</p>
        </div>
        <a href="{{ route('despachadores.create') }}" class="btn btn-primary">Nuevo despachador</a>
    </div>
@endsection

@section('content')
    <article class="module-card">
        <div class="module-toolbar">
            <form id="filtro-despachadores-form" method="GET" action="{{ route('despachadores.index') }}" class="module-filter-form">
                <input id="filtro-despachadores-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por nombre o correo (min. 6)" class="input-control filter-search-control">
                <select id="filtro-despachadores-estado" name="estado" class="select-control">
                    <option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todos</option>
                    <option value="activos" @selected(($estado ?? 'todos') === 'activos')>Activos</option>
                    <option value="inactivos" @selected(($estado ?? 'todos') === 'inactivos')>Inactivos</option>
                </select>

                @if (($estado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
                    <a href="{{ route('despachadores.index') }}" class="btn btn-muted">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="data-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Despachador</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="action-col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($despachadores as $despachador)
                        <tr>
                            <td>
                                <p class="table-primary-text">{{ $despachador->name }}</p>
                                <p class="table-secondary-text">ID {{ $despachador->id }}</p>
                            </td>
                            <td>{{ $despachador->email }}</td>
                            <td>{{ ucfirst($despachador->role?->nombre ?? 'sin rol') }}</td>
                            <td>
                                @if ($despachador->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                <div class="table-actions">
                                    <a href="{{ route('despachadores.edit', $despachador) }}" class="btn btn-muted">Editar</a>

                                    <form method="POST" action="{{ route('despachadores.toggle', $despachador) }}" data-feedback-form="true">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning">{{ $despachador->activo ? 'Desactivar' : 'Activar' }}</button>
                                    </form>

                                    <form method="POST" action="{{ route('despachadores.destroy', $despachador) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara el despachador. Deseas continuar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty">No hay despachadores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="module-footer">
            {{ $despachadores->links() }}
        </div>
    </article>

    <script>
    (function () {
        const form = document.getElementById('filtro-despachadores-form');
        const estado = document.getElementById('filtro-despachadores-estado');
        const busqueda = document.getElementById('filtro-despachadores-busqueda');
        let timer = null;

        if (!form || !estado || !busqueda) {
            return;
        }

        estado.addEventListener('change', () => form.requestSubmit());

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
