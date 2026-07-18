@extends('layouts.app', [
    'title' => 'Editar Cita | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
        <article class="entity-form-card">
            <div class="entity-form-head">
                <h3 class="entity-form-title">Actualizar cita</h3>
                <p class="entity-form-subtitle">Edita agenda, estado y relacion con la formula del paciente.</p>
            </div>

            <div class="entity-form-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">No se pudo actualizar la cita.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('citas.update', $cita) }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Paciente</label>
                        <select name="paciente_id" required class="select-control w-full">
                            @foreach ($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" @selected((string) old('paciente_id', $cita->paciente_id) === (string) $paciente->id)>
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
                                <option value="{{ $formula->id }}" @selected((string) old('formula_medica_id', $cita->formula_medica_id) === (string) $formula->id)>
                                    {{ $formula->numero_formula }} · Paciente #{{ $formula->paciente_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Fecha cita</label>
                        <input type="date" name="fecha_cita" value="{{ old('fecha_cita', $cita->fecha_cita?->toDateString()) }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Hora cita</label>
                        <input type="time" name="hora_cita" value="{{ old('hora_cita', substr((string) $cita->hora_cita, 0, 5)) }}" required class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Motivo</label>
                        <input type="text" name="motivo" value="{{ old('motivo', $cita->motivo) }}" required maxlength="80" class="input-control w-full">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                        <select name="estado" required class="select-control w-full">
                            @foreach ($estadosDisponibles as $estado)
                                <option value="{{ $estado }}" @selected(old('estado', $cita->estado) === $estado)>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="entity-form-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                        <textarea name="observaciones" rows="3" maxlength="255" class="input-control w-full">{{ old('observaciones', $cita->observaciones) }}</textarea>
                    </div>

                    <div class="entity-form-actions entity-form-span-2">
                        <a href="{{ route('citas.index') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Actualizar cita</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
