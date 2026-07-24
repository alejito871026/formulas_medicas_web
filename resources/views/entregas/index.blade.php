@extends('layouts.app', [
	'title' => 'Gestion de Entregas | Gestion de Formulas Medicas',
])

@section('module_nav')
	<div class="module-nav">
		<div>
			<p class="module-nav-title">Entregas de Medicamentos</p>
			<p class="module-nav-subtitle">Controla entregas por item formulado y estado de cumplimiento.</p>
		</div>
		<a href="{{ route('entregas.create') }}" class="btn btn-primary">Nueva entrega</a>
	</div>
@endsection

@section('content')
	<article class="module-card">
		<div class="module-toolbar">
			<form id="filtro-entregas-form" method="GET" action="{{ route('entregas.index') }}" class="module-filter-form">
				<input id="filtro-entregas-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por formula, paciente, medicamento o estado (min. 6)" class="input-control filter-search-control">

				<select id="filtro-entregas-estado" name="estado" class="select-control">
					<option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todos</option>
					@foreach (($estadosDisponibles ?? []) as $estadoItem)
						<option value="{{ $estadoItem }}" @selected(($estado ?? 'todos') === $estadoItem)>{{ ucfirst(str_replace('_', ' ', $estadoItem)) }}</option>
					@endforeach
				</select>

				@if (($estado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
					<a href="{{ route('entregas.index') }}" class="btn btn-muted">Limpiar</a>
				@endif

				<a href="{{ route('entregas.export-pdf', request()->query()) }}" class="btn btn-danger">Exportar PDF</a>
			</form>
		</div>

		<div class="data-table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Formula / Paciente</th>
						<th>Medicamento</th>
						<th>Cantidad</th>
						<th>Fechas</th>
						<th>Estado</th>
						<th class="action-col">Acciones</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($entregas as $entrega)
						<tr>
							<td>
								<p class="table-primary-text">{{ $entrega->formulaItem?->formulaMedica?->numero_formula ?: 'Sin formula' }}</p>
								<p class="table-secondary-text">{{ $entrega->formulaItem?->formulaMedica?->paciente?->nombres }} {{ $entrega->formulaItem?->formulaMedica?->paciente?->apellidos }}</p>
							</td>
							<td>{{ $entrega->formulaItem?->medicamento?->nombre ?: 'Sin medicamento' }}</td>
							<td>
								<p>{{ $entrega->cantidad_entregada }}</p>
								<p class="table-secondary-text">Registrado por: {{ $entrega->user?->name ?: 'Sistema' }}</p>
							</td>
							<td>
								<p>Entrega: {{ $entrega->fecha_entrega?->format('Y-m-d') }}</p>
								<p class="table-secondary-text">Estimada: {{ $entrega->fecha_estimada?->format('Y-m-d') ?: 'N/A' }}</p>
							</td>
							<td>
								@if (in_array($entrega->estado_entrega, ['entregada', 'completa', 'atendida'], true))
									<span class="badge badge-success">{{ ucfirst($entrega->estado_entrega) }}</span>
								@elseif ($entrega->estado_entrega === 'cancelada')
									<span class="badge badge-danger">Cancelada</span>
								@else
									<span class="badge badge-warning">{{ ucfirst($entrega->estado_entrega) }}</span>
								@endif
							</td>
							<td class="action-cell">
								<div class="table-actions">
									<a href="{{ route('entregas.edit', $entrega) }}" class="btn btn-muted">Editar</a>

									{{-- Boton de eliminar oculto temporalmente --}}
									{{--
									<form method="POST" action="{{ route('entregas.destroy', $entrega) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara el registro de entrega. Deseas continuar?');">
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
							<td colspan="6" class="table-empty">No hay entregas registradas.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="module-footer">
			{{ $entregas->links() }}
		</div>
	</article>

	<script>
	(function () {
		const form = document.getElementById('filtro-entregas-form');
		const estado = document.getElementById('filtro-entregas-estado');
		const busqueda = document.getElementById('filtro-entregas-busqueda');
		let timer = null;

		if (!form || !estado || !busqueda) {
			return;
		}

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