<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Gestion de Formulas Medicas' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900">
        @php
            $user = auth()->user();
            $rol = $user?->role?->nombre;
            $menu = [
                ['label' => 'Citas', 'route' => 'citas.index', 'ability' => 'acceso-citas'],
                ['label' => 'Dashboard', 'route' => 'dashboard', 'ability' => 'acceso-dashboard'],
                ['label' => 'Despachadores', 'route' => 'despachadores.index', 'ability' => 'acceso-despachadores'],
                ['label' => 'Entregas', 'route' => 'entregas.index', 'ability' => 'acceso-entregas'],
                ['label' => 'EPS', 'route' => 'eps.index', 'ability' => 'acceso-eps'],
                ['label' => 'Formulas Medicas', 'route' => 'formulas.index', 'ability' => 'acceso-formulas'],
                ['label' => 'Inventario', 'route' => 'inventarios.index', 'ability' => 'acceso-inventarios'],
                ['label' => 'Medicamentos', 'route' => 'medicamentos.index', 'ability' => 'acceso-medicamentos'],
                ['label' => 'Pacientes', 'route' => 'pacientes.index', 'ability' => 'acceso-pacientes'],
            ];
        @endphp
        <div class="flex min-h-screen">
            <aside class="hidden w-72 shrink-0 bg-teal-900 px-6 py-8 text-slate-100 lg:block">
                <p class="text-xs uppercase tracking-[0.35em] text-teal-200">Proyecto Seminario</p>
                <h1 class="mt-3 text-2xl font-semibold">Gestion de Formulas Medicas</h1>
                <p class="mt-3 text-sm leading-6 text-teal-100/85">
                    Base funcional inicial del aplicativo propuesto para el dispensario medico de Cartago.
                </p>

                @if ($user)
                    <div class="mt-6 rounded-2xl bg-white/10 p-4 text-sm">
                        <p class="font-semibold text-white">{{ $user->name }}</p>
                        <p class="mt-1 text-teal-100">{{ $user->email }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-teal-200">Rol: {{ ucfirst($rol ?? 'sin rol') }}</p>
                    </div>
                @endif

                <nav class="mt-10 space-y-2 text-sm">
                    @foreach ($menu as $item)
                        @can($item['ability'])
                            <a class="block rounded-xl px-4 py-3 transition hover:bg-white/10" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                        @endcan
                    @endforeach
                </nav>
            </aside>

            <main class="flex-1 px-6 py-8 lg:px-10">
                @hasSection('module_nav')
                    <div class="mb-5">
                        @yield('module_nav')
                    </div>
                @endif

                <section>
                    @yield('content')
                </section>
            </main>
        </div>

        @include('partials.submit-feedback-modal')
    </body>
</html>