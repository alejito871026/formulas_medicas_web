<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro | Formulas Medicas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900">
        <main class="mx-auto flex min-h-screen max-w-3xl items-center px-6 py-10">
            <section class="w-full rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200 sm:p-10">
                <h1 class="text-2xl font-semibold">Registro de paciente</h1>
                <p class="mt-2 text-sm text-slate-600">Crea tu cuenta y tu ficha de paciente en un solo paso.</p>

                <form class="mt-6 grid gap-4 md:grid-cols-2" method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="tipo_documento">Tipo de documento</label>
                        <select id="tipo_documento" name="tipo_documento" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('tipo_documento') border-red-500 @enderror">
                            @foreach ($tiposDocumento as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipo_documento', 'CC') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="numero_documento">Numero de documento</label>
                        <input id="numero_documento" name="numero_documento" type="text" value="{{ old('numero_documento') }}" required maxlength="20" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('numero_documento') border-red-500 @enderror">
                        @error('numero_documento')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="nombres">Nombres</label>
                        <input id="nombres" name="nombres" type="text" value="{{ old('nombres') }}" required minlength="2" maxlength="80" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('nombres') border-red-500 @enderror">
                        @error('nombres')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="apellidos">Apellidos</label>
                        <input id="apellidos" name="apellidos" type="text" value="{{ old('apellidos') }}" required minlength="2" maxlength="80" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('apellidos') border-red-500 @enderror">
                        @error('apellidos')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="email">Correo de acceso</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="120" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="fecha_nacimiento">Fecha de nacimiento</label>
                        <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" value="{{ old('fecha_nacimiento') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('fecha_nacimiento') border-red-500 @enderror">
                        @error('fecha_nacimiento')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="telefono">Telefono</label>
                        <input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}" maxlength="20" inputmode="numeric" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="eps">EPS</label>
                        <select id="eps" name="eps" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('eps') border-red-500 @enderror" data-searchable="true" data-search-limit="10" data-search-placeholder="Busca una EPS por nombre">
                            <option value="">Selecciona una EPS</option>
                            @foreach ($epsActivas as $ep)
                                <option value="{{ $ep->nombre }}" @selected(old('eps') === $ep->nombre)>{{ $ep->nombre }}</option>
                            @endforeach
                        </select>
                        @error('eps')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="departamento">Departamento</label>
                        <select id="departamento" name="departamento" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('departamento') border-red-500 @enderror">
                            <option value="">Selecciona un departamento</option>
                            @foreach (array_keys($geografia) as $departamento)
                                <option value="{{ $departamento }}" @selected(old('departamento') === $departamento)>{{ $departamento }}</option>
                            @endforeach
                        </select>
                        @error('departamento')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="municipio">Municipio</label>
                        <select id="municipio" name="municipio" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('municipio') border-red-500 @enderror">
                            <option value="">Selecciona un municipio</option>
                        </select>
                        @error('municipio')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium" for="direccion">Direccion</label>
                        <textarea id="direccion" name="direccion" rows="3" maxlength="150" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('direccion') border-red-500 @enderror">{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium" for="avatar">Avatar</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('avatar') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-slate-500">Opcional. Maximo 2 MB.</p>
                        @error('avatar')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="password">Contrasena</label>
                        <input id="password" name="password" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="password_confirmation">Confirmar contrasena</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="w-full rounded-xl bg-teal-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-800">Registrarme como paciente</button>
                    </div>
                </form>

                <p class="mt-4 text-center text-sm text-slate-600">
                    Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="font-medium text-teal-700 hover:underline">Inicia sesion</a>
                </p>

                <div id="register-form-data" data-geografia='@json($geografia)' data-old-municipio="{{ old('municipio', '') }}" class="hidden"></div>
            </section>
        </main>

        <script>
        (function () {
            const dataElement = document.getElementById('register-form-data');
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
    </body>
</html>
