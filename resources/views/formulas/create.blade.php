@extends('layouts.app', [
    'title' => 'Nueva Formula Medica | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar formula medica</h3>
                <p class="entity-form-subtitle">Registra una formula para el paciente autenticado o para un paciente seleccionado por administracion.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo guardar la formula.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('formulas.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Paciente</label>
                        <select name="paciente_id" required @disabled($pacienteBloqueado ?? false) class="select-control w-full" data-searchable="true" data-search-limit="10" data-search-placeholder="Busca por nombre o documento">
                            <option value="">Selecciona un paciente</option>
                            @foreach ($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" @selected((string) old('paciente_id', $pacientes->count() === 1 ? $pacientes->first()->id : null) === (string) $paciente->id)>
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }} · {{ $paciente->numero_documento }}
                                </option>
                            @endforeach
                        </select>
                        @if (($pacienteBloqueado ?? false) && $pacientes->count() === 1)
                            <input type="hidden" name="paciente_id" value="{{ old('paciente_id', $pacientes->first()->id) }}">
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Numero de formula</label>
                        <input type="text" name="numero_formula" value="{{ old('numero_formula') }}" required maxlength="40" class="input-control w-full" placeholder="Ej: FM-2026-0001">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha de formula</label>
                        <input type="date" name="fecha_formula" value="{{ old('fecha_formula', now()->toDateString()) }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Medico tratante</label>
                        <input type="text" name="medico_tratante" list="medicos-tratantes" value="{{ old('medico_tratante') }}" maxlength="120" class="input-control w-full" placeholder="Ej: Dra. Laura Rios">
                        <datalist id="medicos-tratantes">
                            @foreach (($medicosTratantes ?? []) as $medico)
                                <option value="{{ $medico }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                        <select name="estado" required class="select-control w-full">
                            @foreach ($estadosDisponibles as $estado)
                                <option value="{{ $estado }}" @selected(old('estado', 'pendiente') === $estado)>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" maxlength="255" class="input-control w-full">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('formulas.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar formula</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
