<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro | Formulas Medicas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900">
        <main class="mx-auto flex min-h-screen max-w-lg items-center px-6">
            <section class="w-full rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h1 class="text-2xl font-semibold">Crear Cuenta</h1>
                <p class="mt-2 text-sm text-slate-600">Selecciona tu rol para habilitar la interfaz correspondiente del sistema.</p>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="name">Nombre</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="email">Correo</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="role_id">Rol</label>
                        <select id="role_id" name="role_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('role_id') border-red-500 @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ ucfirst($role->nombre) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="password">Contrasena</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="password_confirmation">Confirmar contrasena</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm">
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-teal-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-800">Registrarme</button>
                </form>

                <p class="mt-4 text-center text-sm text-slate-600">
                    Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="font-medium text-teal-700 hover:underline">Inicia sesion</a>
                </p>
            </section>
        </main>
    </body>
</html>
