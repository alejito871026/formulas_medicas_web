<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar sesion | Formulas Medicas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="relative mx-auto flex min-h-screen w-full items-center justify-center overflow-hidden px-6 py-10">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-16 top-10 h-80 w-80 rounded-full bg-cyan-400/20 blur-3xl"></div>
                <div class="absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
                <div class="absolute left-1/2 top-1/3 h-72 w-72 -translate-x-1/2 rounded-full bg-sky-300/10 blur-3xl"></div>
            </div>

            <section class="relative grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur lg:grid-cols-2">
                <aside class="hidden bg-gradient-to-br from-cyan-500 via-emerald-500 to-teal-600 p-10 lg:block">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-100/90">Dispensario Digital</p>
                            <h1 class="mt-5 text-4xl font-semibold leading-tight text-white">
                                Gestion segura de formulas y entregas de medicamentos
                            </h1>
                            <p class="mt-5 max-w-sm text-sm leading-7 text-teal-50/90">
                                Plataforma para priorizar pacientes, organizar inventario y acelerar la entrega oportuna en el punto de dispensacion.
                            </p>
                        </div>

                        <div class="grid gap-3">
                            <article class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/30">
                                <p class="text-sm font-semibold text-white">Trazabilidad completa</p>
                                <p class="mt-1 text-xs text-teal-50/90">Consulta de formulas, estados y entregas pendientes en un solo flujo.</p>
                            </article>
                            <article class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/30">
                                <p class="text-sm font-semibold text-white">Interfaz por actor</p>
                                <p class="mt-1 text-xs text-teal-50/90">Vistas diferenciadas para cliente, despachador y administrativo.</p>
                            </article>
                        </div>
                    </div>
                </aside>

                <div class="p-8 sm:p-10">
                    <div class="mx-auto max-w-md">
                        <div class="inline-flex items-center gap-3 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-xs font-medium text-cyan-200">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M9 12H15M12 9V15M7 4H17C19.2091 4 21 5.79086 21 8V16C21 18.2091 19.2091 20 17 20H7C4.79086 20 3 18.2091 3 16V8C3 5.79086 4.79086 4 7 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Acceso al sistema clinico
                        </div>

                        <h2 class="mt-5 text-3xl font-semibold text-white">Iniciar sesion</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-300">
                            Ingresa con tu cuenta para acceder al tablero operativo de formulas medicas.
                        </p>

                        <form class="mt-8 space-y-5" method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-200" for="email">Correo</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                    class="w-full rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 @error('email') border-red-400 focus:border-red-400 focus:ring-red-400/20 @enderror"
                                    placeholder="usuario@formulas.test">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-200" for="password">Contrasena</label>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                    class="w-full rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 @error('password') border-red-400 focus:border-red-400 focus:ring-red-400/20 @enderror"
                                    placeholder="••••••••">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm text-slate-300">
                                <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-800 text-cyan-500 focus:ring-cyan-500/40">
                                Mantener sesion activa
                            </label>

                            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:from-cyan-400 hover:to-emerald-400">
                                Entrar al panel
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-300">
                            No tienes cuenta?
                            <a href="{{ route('register') }}" class="font-semibold text-cyan-300 hover:text-cyan-200 hover:underline">Registrate</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
