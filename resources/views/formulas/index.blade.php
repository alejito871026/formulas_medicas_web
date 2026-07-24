@extends('layouts.app', [
	'title' => 'Gestion de Formulas Medicas | Gestion de Formulas Medicas',
])

@section('module_nav')
	<div class="module-nav">
		<div>
			<p class="module-nav-title">Formulas Medicas</p>
			<p class="module-nav-subtitle">Administra formulas, vigencias y trazabilidad por paciente.</p>
		</div>
		@if (($rol ?? null) === 'administrativo')
			<a href="{{ route('formulas.create') }}" class="btn btn-primary">Nueva formula</a>
		@endif
	</div>
@endsection

@section('content')
	<article class="module-card">
		<div class="module-toolbar">
			<form id="filtro-formulas-form" method="GET" action="{{ route('formulas.index') }}" class="module-filter-form">
				<input id="filtro-formulas-busqueda" type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por numero, paciente, medico o documento (min. 6)" class="input-control filter-search-control">

				<select id="filtro-formulas-estado" name="estado" class="select-control">
					<option value="todos" @selected(($estado ?? 'todos') === 'todos')>Todos</option>
					@foreach (($estadosDisponibles ?? []) as $estadoItem)
						<option value="{{ $estadoItem }}" @selected(($estado ?? 'todos') === $estadoItem)>{{ ucfirst(str_replace('_', ' ', $estadoItem)) }}</option>
					@endforeach
				</select>

				@if (($rol ?? null) === 'administrativo')
					<select id="filtro-formulas-paciente" name="paciente" class="select-control">
						<option value="todos" @selected(($pacienteFiltro ?? 'todos') === 'todos')>Todos los pacientes</option>
						@foreach (($pacientesDisponibles ?? collect()) as $paciente)
							<option value="{{ $paciente->id }}" @selected((string) ($pacienteFiltro ?? 'todos') === (string) $paciente->id)>
								{{ $paciente->nombres }} {{ $paciente->apellidos }}
							</option>
						@endforeach
					</select>
				@endif

				@if (($estado ?? 'todos') !== 'todos' || ($busqueda ?? '') !== '' || (($pacienteFiltro ?? 'todos') !== 'todos'))
					<a href="{{ route('formulas.index') }}" class="btn btn-muted">Limpiar</a>
				@endif
			</form>
		</div>

		<div class="data-table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Formula</th>
						<th>Paciente</th>
						<th>Fechas</th>
						<th>Medico</th>
						<th>Estado</th>
						@if (($rol ?? null) === 'administrativo')
							<th class="action-col">Acciones</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@forelse ($formulas as $formula)
						<tr>
							<td>
								<p class="table-primary-text">{{ $formula->numero_formula }}</p>
								<p class="table-secondary-text">ID #{{ $formula->id }}</p>
							</td>
							<td>
								<p>{{ $formula->paciente?->nombres }} {{ $formula->paciente?->apellidos }}</p>
								<p class="table-secondary-text">{{ $formula->paciente?->numero_documento ?: 'Sin documento' }}</p>
							</td>
							<td>
								<p>Emision: {{ $formula->fecha_formula?->format('Y-m-d') }}</p>
								<p class="table-secondary-text">Vence: {{ $formula->fecha_vencimiento?->format('Y-m-d') ?: 'Sin vencimiento' }}</p>
							</td>
							<td>{{ $formula->medico_tratante ?: 'No registrado' }}</td>
							<td>
								@if ($formula->estado === 'entregada')
									<span class="badge badge-success">Entregada</span>
								@elseif ($formula->estado === 'parcial')
									<span class="badge badge-warning">Parcial</span>
								@elseif ($formula->estado === 'vencida')
									<span class="badge badge-danger">Vencida</span>
								@else
									<span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $formula->estado)) }}</span>
								@endif
							</td>
							@if (($rol ?? null) === 'administrativo')
								<td class="action-cell">
									<div class="table-actions">
										<a href="{{ route('formulas.edit', $formula) }}" class="btn btn-muted">Editar</a>

										{{-- Boton de eliminar oculto temporalmente --}}
										{{--
										<form method="POST" action="{{ route('formulas.destroy', $formula) }}" data-feedback-form="true" onsubmit="return confirm('Se eliminara la formula medica. Deseas continuar?');">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-danger">Eliminar</button>
										</form>
										--}}
									</div>
								</td>
							@endif
						</tr>
					@empty
						<tr>
							<td colspan="{{ ($rol ?? null) === 'administrativo' ? 6 : 5 }}" class="table-empty">No hay formulas medicas registradas.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="module-footer">
			{{ $formulas->links() }}
		</div>
	</article>

	<script>
	(function () {
		const form = document.getElementById('filtro-formulas-form');
		const estado = document.getElementById('filtro-formulas-estado');
		const paciente = document.getElementById('filtro-formulas-paciente');
		const busqueda = document.getElementById('filtro-formulas-busqueda');
		let timer = null;

		if (!form || !estado || !busqueda) {
			return;
		}

		estado.addEventListener('change', () => {
			form.requestSubmit();
		});

		if (paciente) {
			paciente.addEventListener('change', () => {
				form.requestSubmit();
			});
		}

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