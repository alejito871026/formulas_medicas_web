@extends('layouts.app', [
    'title' => 'Gestion de Citas | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Citas de Entrega</p>
            <p class="module-nav-subtitle">Programa y gestiona turnos de entrega asociados a formulas medicas.</p>
        </div>
        @if (($rol ?? null) === 'administrativo')
            <a href="{{ route('citas.create') }}" class="btn btn-primary">Nueva cita</a>
        @endif
    </div>
@endsection

@section('content')
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4 mb-6">
        <article class="module-card p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Programadas</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['programadas'] ?? 0 }}</p>
        </article>
        <article class="module-card p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Confirmadas</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['confirmadas'] ?? 0 }}</p>
        </article>
        <article class="module-card p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Atendidas este mes</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['atendidas_mes'] ?? 0 }}</p>
        </article>
        <article class="module-card p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reprogramadas</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['reprogramadas'] ?? 0 }}</p>
        </article>
    </div>

    <article class="module-card">
        <div class="module-toolbar">
            <form id="filtro-citas-form" method="GET" action="{{ route('citas.index') }}" class="module-filter-form">
                <input id="filtro-citas-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por paciente, formula, motivo u observaciones (min. 6)" class="input-control filter-search-control">

                <select id="filtro-citas-estado" name="estado" class="select-control">
                    <option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todos</option>
                    @foreach (($estadosDisponibles ?? []) as $estadoItem)
                        <option value="{{ $estadoItem }}" @selected(($estado ?? 'todos') === $estadoItem)>{{ ucfirst(str_replace('_', ' ', $estadoItem)) }}</option>
                    @endforeach
                </select>

                <select id="filtro-citas-motivo" name="motivo" class="select-control">
                    <option value="todos" @selected(($motivo ?? 'todos') === 'todos')>Todos los motivos</option>
                    @foreach (($motivosDisponibles ?? collect()) as $motivoItem)
                        <option value="{{ $motivoItem }}" @selected(($motivo ?? 'todos') === $motivoItem)>{{ ucfirst($motivoItem) }}</option>
                    @endforeach
                </select>

                @if (($estado ?? 'todos') !== 'todos' || ($motivo ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
                    <a href="{{ route('citas.index') }}" class="btn btn-muted">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="data-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Formula</th>
                        <th>Agenda</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        @if (($rol ?? null) === 'administrativo')
                            <th class="action-col">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citas as $cita)
                        <tr>
                            <td>
                                <p class="table-primary-text">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</p>
                                <p class="table-secondary-text">{{ $cita->paciente?->numero_documento ?: 'Sin documento' }}</p>
                            </td>
                            <td>{{ $cita->formulaMedica?->numero_formula ?: 'Sin formula' }}</td>
                            <td>
                                <p>{{ $cita->fecha_cita?->format('Y-m-d') }}</p>
                                <p class="table-secondary-text">{{ substr((string) $cita->hora_cita, 0, 5) }}</p>
                            </td>
                            <td>{{ ucfirst($cita->motivo) }}</td>
                            <td>
                                @if ($cita->estado === 'atendida')
                                    <span class="badge badge-success">Atendida</span>
                                @elseif (in_array($cita->estado, ['cancelada', 'no_asistio'], true))
                                    <span class="badge badge-danger">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                                @endif
                            </td>
                            @if (($rol ?? null) === 'administrativo')
                                <td class="action-cell">
                                    <div class="table-actions">
                                        <a href="{{ route('citas.edit', $cita) }}" class="btn btn-muted">Editar</a>

                                        <form method="POST" action="{{ route('citas.destroy', $cita) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara la cita. Deseas continuar?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($rol ?? null) === 'administrativo' ? 6 : 5 }}" class="table-empty">No hay citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="module-footer">
            {{ $citas->links() }}
        </div>
    </article>

    <script>
    (function () {
        const form = document.getElementById('filtro-citas-form');
        const estado = document.getElementById('filtro-citas-estado');
        const motivo = document.getElementById('filtro-citas-motivo');
        const busqueda = document.getElementById('filtro-citas-busqueda');
        let timer = null;

        if (!form || !estado || !motivo || !busqueda) {
            return;
        }

        estado.addEventListener('change', () => form.requestSubmit());
        motivo.addEventListener('change', () => form.requestSubmit());

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
