@extends('layouts.app', [
	'title' => 'Gestion de Inventario | Gestion de Formulas Medicas',
])

@section('module_nav')
	<div class="module-nav">
		<div>
			<p class="module-nav-title">Inventario por Lotes</p>
			<p class="module-nav-subtitle">Controla existencias, vencimientos y puntos de reposicion por medicamento.</p>
		</div>
		<a href="{{ route('inventarios.create') }}" class="btn btn-primary">Nuevo lote</a>
	</div>
@endsection

@section('content')
	<article class="module-card">
		<div class="module-toolbar">
			<form id="filtro-inventario-form" method="GET" action="{{ route('inventarios.index') }}" class="module-filter-form">
				<input id="filtro-inventario-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por medicamento, codigo, lote o ubicacion (min. 6)" class="input-control filter-search-control">
				<select id="filtro-inventario-estado" name="estado" class="select-control">
					<option value="todos" @selected(($filtroEstado ?? 'todos') === 'todos')>Todos</option>
					<option value="bajo_stock" @selected(($filtroEstado ?? 'todos') === 'bajo_stock')>Bajo stock</option>
					<option value="por_vencer" @selected(($filtroEstado ?? 'todos') === 'por_vencer')>Por vencer (60 dias)</option>
					<option value="vencido" @selected(($filtroEstado ?? 'todos') === 'vencido')>Vencido</option>
				</select>

				@if (($filtroEstado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
					<a href="{{ route('inventarios.index') }}" class="btn btn-muted">Limpiar</a>
				@endif
			</form>
		</div>

		<div class="data-table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Medicamento</th>
						<th>Lote</th>
						<th>Stock</th>
						<th>Vencimiento</th>
						<th>Estado</th>
						<th class="action-col">Acciones</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($inventarios as $inventario)
						@php
							$estaVencido = $inventario->fecha_vencimiento && $inventario->fecha_vencimiento->isPast();
							$porVencer = $inventario->fecha_vencimiento && ! $estaVencido && $inventario->fecha_vencimiento->diffInDays(now()) <= 60;
							$bajoStock = $inventario->stock_actual <= $inventario->stock_minimo;
						@endphp
						<tr>
							<td>
								<p class="table-primary-text">{{ $inventario->medicamento?->nombre ?: 'Medicamento no disponible' }}</p>
								<p class="table-secondary-text">{{ $inventario->medicamento?->codigo ?: 'Sin codigo' }} · {{ $inventario->ubicacion ?: 'Sin ubicacion' }}</p>
							</td>
							<td>{{ $inventario->lote }}</td>
							<td>{{ $inventario->stock_actual }} / min {{ $inventario->stock_minimo }}</td>
							<td>{{ $inventario->fecha_vencimiento?->format('Y-m-d') ?: 'Sin fecha' }}</td>
							<td>
								@if ($estaVencido)
									<span class="badge badge-danger">Vencido</span>
								@elseif ($porVencer)
									<span class="badge badge-warning">Por vencer</span>
								@elseif ($bajoStock)
									<span class="badge badge-warning">Bajo stock</span>
								@else
									<span class="badge badge-success">OK</span>
								@endif
							</td>
							<td class="action-cell">
								<div class="table-actions">
									<a href="{{ route('inventarios.edit', $inventario) }}" class="btn btn-muted">Editar</a>

									<form method="POST" action="{{ route('inventarios.destroy', $inventario) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara el lote de inventario. Deseas continuar?');">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger">Eliminar</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="table-empty">No hay lotes de inventario registrados.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="module-footer">
			{{ $inventarios->links() }}
		</div>
	</article>

	<script>
	(function () {
		const form = document.getElementById('filtro-inventario-form');
		const estado = document.getElementById('filtro-inventario-estado');
		const busqueda = document.getElementById('filtro-inventario-busqueda');
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