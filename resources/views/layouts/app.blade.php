<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Gestion de Formulas Medicas' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900">
        <div class="flex min-h-screen">
            <aside class="hidden w-72 shrink-0 bg-teal-900 px-6 py-8 text-slate-100 lg:block">
                <p class="text-xs uppercase tracking-[0.35em] text-teal-200">Proyecto Seminario</p>
                <h1 class="mt-3 text-2xl font-semibold">Gestion de Formulas Medicas</h1>
                <p class="mt-3 text-sm leading-6 text-teal-100/85">
                    Base funcional inicial del aplicativo propuesto para el dispensario medico de Cartago.
                </p>

                <nav class="mt-10 space-y-2 text-sm">
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('pacientes.index') }}">Pacientes</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('formulas.index') }}">Formulas Medicas</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('medicamentos.index') }}">Medicamentos</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('inventarios.index') }}">Inventario</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('entregas.index') }}">Entregas</a>
                    <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route('citas.index') }}">Citas</a>
                </nav>
            </aside>

            <main class="flex-1 px-6 py-8 lg:px-10">
                <header class="rounded-3xl bg-white px-6 py-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-teal-700"> </p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight">{{ $heading ?? 'Inicio del proyecto' }}</h2>
                    @isset($intro)
                        <p class="mt-3 max-w-4xl text-sm leading-6 text-slate-600">{{ $intro }}</p>
                    @endisset
                </header>

                <section class="mt-8">
                    @yield('content')
                </section>
            </main>
        </div>
    </body>
</html>