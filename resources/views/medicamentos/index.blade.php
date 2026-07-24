@extends('layouts.app', [
	'title' => 'Gestion de Medicamentos | Gestion de Formulas Medicas',
])

@section('module_nav')
	<div class="module-nav">
		<div>
			<p class="module-nav-title">Catalogo de Medicamentos</p>
			<p class="module-nav-subtitle">Administra ficha tecnica, requisito de formula y datos base del dispensario.</p>
		</div>
		<a href="{{ route('medicamentos.create') }}" class="btn btn-primary">Nuevo medicamento</a>
	</div>
@endsection

@section('content')
	<article class="module-card">
		<div class="module-toolbar">
			<form id="filtro-medicamentos-form" method="GET" action="{{ route('medicamentos.index') }}" class="module-filter-form">
				<input id="filtro-medicamentos-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por codigo, nombre o principio (min. 6)" class="input-control filter-search-control">
				<select id="filtro-medicamentos-formula" name="formula" class="select-control">
					<option value="todos" @selected(($filtroFormula ?? 'todos') === 'todos')>Todos</option>
					<option value="si" @selected(($filtroFormula ?? 'todos') === 'si')>Requiere formula</option>
					<option value="no" @selected(($filtroFormula ?? 'todos') === 'no')>Venta libre</option>
				</select>

				@if (($filtroFormula ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '')
					<a href="{{ route('medicamentos.index') }}" class="btn btn-muted">Limpiar</a>
				@endif
			</form>
		</div>

		<div class="data-table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Medicamento</th>
						<th>Presentacion</th>
						<th>Unidad</th>
						<th>Requiere formula</th>
						<th class="action-col">Acciones</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($medicamentos as $medicamento)
						<tr>
							<td>
								<p class="table-primary-text">{{ $medicamento->nombre }}</p>
								<p class="table-secondary-text">{{ $medicamento->codigo }} · {{ $medicamento->principio_activo ?: 'Sin principio activo' }}</p>
							</td>
							<td>{{ $medicamento->presentacion }} {{ $medicamento->concentracion ? '· ' . $medicamento->concentracion : '' }}</td>
							<td>{{ $medicamento->unidad_medida ?: 'Sin unidad' }}</td>
							<td>
								@if ($medicamento->requiere_formula)
									<span class="badge badge-warning">Si</span>
								@else
									<span class="badge badge-success">No</span>
								@endif
							</td>
							<td class="action-cell">
								<div class="table-actions">
									<a href="{{ route('medicamentos.edit', $medicamento) }}" class="btn btn-muted">Editar</a>

									{{-- Boton de eliminar oculto temporalmente --}}
									{{--
									<form method="POST" action="{{ route('medicamentos.destroy', $medicamento) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara el medicamento. Deseas continuar?');">
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
							<td colspan="5" class="table-empty">No hay medicamentos registrados.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="module-footer">
			{{ $medicamentos->links() }}
		</div>
	</article>

	<script>
	(function () {
		const form = document.getElementById('filtro-medicamentos-form');
		const formula = document.getElementById('filtro-medicamentos-formula');
		const busqueda = document.getElementById('filtro-medicamentos-busqueda');
		let timer = null;

		if (!form || !formula || !busqueda) {
			return;
		}

		formula.addEventListener('change', () => {
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