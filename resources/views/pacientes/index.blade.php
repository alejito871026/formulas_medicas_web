@extends('layouts.app', [
	'title' => 'Gestion de Clientes | Gestion de Formulas Medicas',
])

@section('module_nav')
	<div class="module-nav">
		<div>
			<p class="module-nav-title">Gestion de Clientes</p>
			<p class="module-nav-subtitle">Administra clientes, filtros de estado y acciones de acceso.</p>
		</div>
		<a href="{{ route('pacientes.create') }}" class="btn btn-primary">
			Nuevo cliente
		</a>
	</div>
@endsection

@section('content')
	<div class="module-card">
		<div class="module-toolbar">
			<form id="filtro-clientes-form" method="GET" action="{{ route('pacientes.index') }}" class="module-filter-form">
				<input id="filtro-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por nombre, documento o correo (min. 6)" class="input-control filter-search-control">
				<select id="filtro-estado" name="estado" class="select-control">
					<option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todos</option>
					<option value="activos" @selected(($estado ?? 'todos') === 'activos')>Activos</option>
					<option value="inactivos" @selected(($estado ?? 'todos') === 'inactivos')>Inactivos</option>
				</select>
				<select id="filtro-eps" name="eps" class="select-control">
					<option value="todos" @selected(($epsFiltro ?? 'todos') === 'todos')>Todas las EPS</option>
					@foreach (($epsDisponibles ?? collect()) as $epsNombre)
						<option value="{{ $epsNombre }}" @selected(($epsFiltro ?? 'todos') === $epsNombre)>{{ $epsNombre }}</option>
					@endforeach
				</select>
				@if (($estado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '' || ($epsFiltro ?? 'todos') !== 'todos')
					<a href="{{ route('pacientes.index') }}" class="btn btn-muted">
						Limpiar
					</a>
				@endif
			</form>
		</div>

		<div class="data-table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Cliente</th>
						<th>Documento</th>
						<th>Contacto</th>
						<th>EPS</th>
						<th>Acceso</th>
						<th class="action-col">Acciones</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($pacientes as $paciente)
						<tr>
							<td>
								<p class="table-primary-text">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
								<p class="table-secondary-text">{{ $paciente->user?->email ?? 'Sin usuario' }}</p>
							</td>
							<td>{{ $paciente->tipo_documento }} {{ $paciente->numero_documento }}</td>
							<td>
								<p>{{ $paciente->telefono ?: 'Sin telefono' }}</p>
								<p class="table-secondary-text">{{ $paciente->email ?: 'Sin correo' }}</p>
							</td>
							<td>{{ $paciente->eps ?: 'No registrada' }}</td>
							<td>
								@if ($paciente->user?->activo)
									<span class="badge badge-success">Activo</span>
								@else
									<span class="badge badge-danger">Desactivado</span>
								@endif
							</td>
							<td class="action-cell">
								<div class="table-actions">
									<a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-muted">
										Editar
									</a>

									<form method="POST" action="{{ route('pacientes.toggle', $paciente) }}" data-feedback-form="true">
										@csrf
										@method('PATCH')
										<button type="submit" class="btn btn-warning">
											{{ $paciente->user?->activo ? 'Desactivar' : 'Activar' }}
										</button>
									</form>

									<form method="POST" action="{{ route('pacientes.destroy', $paciente) }}" data-feedback-form="true" onsubmit="return confirm('Esta accion eliminara el cliente y su usuario. Deseas continuar?');">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger">
											Eliminar
										</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="table-empty">No hay clientes registrados.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="module-footer">
			{{ $pacientes->links() }}
		</div>
	</div>

	<script>
	(function () {
		const form = document.getElementById('filtro-clientes-form');
		const estado = document.getElementById('filtro-estado');
		const eps = document.getElementById('filtro-eps');
		const busqueda = document.getElementById('filtro-busqueda');
		let timer = null;

		if (!form || !estado || !eps || !busqueda) {
			return;
		}

		estado.addEventListener('change', () => {
			form.requestSubmit();
		});

		eps.addEventListener('change', () => {
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