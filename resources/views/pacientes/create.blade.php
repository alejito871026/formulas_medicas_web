@extends('layouts.app', [
    'title' => 'Nuevo Cliente | Gestion de Formulas Medicas',
])

@section('content')
    <section class="entity-form-shell entity-form-floating">
    <article class="entity-form-card">
        <div class="entity-form-head">
            <h3 class="entity-form-title">Datos del nuevo cliente</h3>
            <p class="entity-form-subtitle">Completa credenciales, identificacion y ubicacion para habilitar agendamiento de entrega.</p>
        </div>

        <div class="entity-form-body">
        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">No se pudo guardar el cliente.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pacientes.store') }}" data-feedback-form="true" class="entity-form-grid entity-form-grid-two">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nombre de usuario</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Correo de acceso</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Contrasena</label>
                <input type="password" name="password" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Confirmar contrasena</label>
                <input type="password" name="password_confirmation" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tipo documento</label>
                <select name="tipo_documento" required class="select-control w-full">
                    @foreach ($tiposDocumento as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo_documento', 'CC') === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Numero documento</label>
                <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nombres</label>
                <input type="text" name="nombres" value="{{ old('nombres') }}" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Fecha nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Correo de contacto paciente</label>
                <input type="email" name="email_contacto" value="{{ old('email_contacto') }}" class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">EPS</label>
                <select name="eps" required class="select-control w-full">
                    <option value="">Selecciona una EPS</option>
                    @foreach ($epsActivas as $ep)
                        <option value="{{ $ep->nombre }}" @selected(old('eps') === $ep->nombre)>{{ $ep->nombre }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Si no aparece una EPS, registrala en el modulo EPS.</p>
            </div>

            <div class="entity-form-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Direccion</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="input-control w-full">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Departamento</label>
                <select id="departamento" name="departamento" required class="select-control w-full">
                    <option value="">Selecciona un departamento</option>
                    @foreach (array_keys($geografia) as $departamento)
                        <option value="{{ $departamento }}" @selected(old('departamento') === $departamento)>{{ $departamento }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Municipio</label>
                <select id="municipio" name="municipio" required class="select-control w-full">
                    <option value="">Selecciona un municipio</option>
                </select>
            </div>

            <div class="entity-form-actions entity-form-span-2">
                <a href="{{ route('pacientes.index') }}" class="btn btn-muted">Cancelar</a>
                <button type="submit" class="btn btn-teal">Guardar cliente</button>
            </div>
        </form>
        </div>
    </article>
    </section>

    <div id="paciente-form-data" data-geografia='@json($geografia)' data-old-municipio="{{ old('municipio', '') }}" class="hidden"></div>

    <script>
    (function () {
        const dataElement = document.getElementById('paciente-form-data');
        const geografia = JSON.parse(dataElement?.dataset.geografia || '{}');
        const departamentoSelect = document.getElementById('departamento');
        const municipioSelect = document.getElementById('municipio');
        const oldMunicipio = dataElement?.dataset.oldMunicipio || '';

        if (!departamentoSelect || !municipioSelect) {
            return;
        }

        const renderMunicipios = (departamento, selected) => {
            const municipios = geografia[departamento] || [];
            municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';

            municipios.forEach((municipio) => {
                const option = document.createElement('option');
                option.value = municipio;
                option.textContent = municipio;
                if (selected && selected === municipio) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });
        };

        renderMunicipios(departamentoSelect.value, oldMunicipio);

        departamentoSelect.addEventListener('change', () => {
            renderMunicipios(departamentoSelect.value, null);
        });
    })();
    </script>
@endsection
