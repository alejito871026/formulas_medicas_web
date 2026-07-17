@extends('layouts.app', [
    'title' => 'Nueva Cita | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Registrar cita</h3>
                <p class="entity-form-subtitle">Programa la cita de entrega asociando paciente y formula (opcional).</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo guardar la cita.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('citas.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Paciente</label>
                        <select name="paciente_id" required class="select-control w-full">
                            <option value="">Selecciona un paciente</option>
                            @foreach ($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" @selected((string) old('paciente_id') === (string) $paciente->id)>
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }} · {{ $paciente->numero_documento }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Formula medica (opcional)</label>
                        <select name="formula_medica_id" class="select-control w-full">
                            <option value="">Sin formula asociada</option>
                            @foreach ($formulas as $formula)
                                <option value="{{ $formula->id }}" @selected((string) old('formula_medica_id') === (string) $formula->id)>
                                    {{ $formula->numero_formula }} · Paciente #{{ $formula->paciente_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha cita</label>
                        <input type="date" name="fecha_cita" value="{{ old('fecha_cita', now()->toDateString()) }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Hora cita</label>
                        <input type="time" name="hora_cita" value="{{ old('hora_cita', '08:00') }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Motivo</label>
                        <input type="text" name="motivo" value="{{ old('motivo', 'reclamacion') }}" required class="input-control w-full" maxlength="80">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                        <select name="estado" required class="select-control w-full">
                            @foreach ($estadosDisponibles as $estado)
                                <option value="{{ $estado }}" @selected(old('estado', 'programada') === $estado)>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="input-control w-full">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('citas.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar cita</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
