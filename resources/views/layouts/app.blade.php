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
            $rolLabel = $rol === 'cliente' ? 'paciente' : ($rol ?? 'sin rol');
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
                        <div class="mb-4 flex items-center gap-3">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="h-12 w-12 rounded-full object-cover ring-2 ring-white/60">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-base font-semibold text-white">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-white leading-tight">{{ $user->name }}</p>
                                <p class="mt-1 break-all text-xs leading-5 text-teal-100">{{ $user->email }}</p>
                            </div>
                        </div>

                        <p class="mt-1 text-xs uppercase tracking-wide text-teal-200">Rol: {{ ucfirst($rolLabel) }}</p>
                        <a
                            href="{{ route('profile.edit') }}"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-teal-200/30 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15"
                        >
                            Mi perfil
                        </a>
                        <form class="mt-4" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-teal-200/30 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10"
                            >
                                Cerrar sesión
                            </button>
                        </form>
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
                @if ($user)
                    <div class="mb-5 flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200 lg:hidden">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">Rol: {{ ucfirst($rolLabel) }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('profile.edit') }}"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                            >
                                Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    Salir
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

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