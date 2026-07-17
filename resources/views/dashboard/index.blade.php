@extends('layouts.app', [
    'title' => 'Dashboard | Gestion de Formulas Medicas',
])

@section('module_nav')
    @php
        $titulos = [
            'administrativo' => [
                'titulo' => 'Dashboard Administrativo',
                'subtitulo' => 'Analitica operativa de clientes, atenciones y demanda de medicamentos.',
            ],
            'despachador' => [
                'titulo' => 'Dashboard de Despacho',
                'subtitulo' => 'Seguimiento de entregas, inventario critico y demanda de medicamentos.',
            ],
            'cliente' => [
                'titulo' => 'Mi Panel de Cliente',
                'subtitulo' => 'Estado de formulas, entregas y citas para gestionar tu proceso.',
            ],
        ];
        $encabezado = $titulos[$rol] ?? [
            'titulo' => 'Dashboard',
            'subtitulo' => 'Panel principal del sistema.',
        ];
    @endphp

    <div class="module-nav">
        <div>
            <p class="module-nav-title">{{ $encabezado['titulo'] }}</p>
            <p class="module-nav-subtitle">{{ $encabezado['subtitulo'] }}</p>
        </div>
    </div>
@endsection

@section('content')
    @if ($rol === 'administrativo')
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Clientes registrados</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalClientes }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Usuarios atendidos</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalAtenciones }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Items formulados</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalItemsFormulados }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Medicamentos catalogados</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalMedicamentos }}</p>
            </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-5">
            <article class="module-card p-6 xl:col-span-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Clientes registrados por mes</h3>
                    <span class="badge badge-success">Ultimos 6 meses</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-clientes"></canvas>
                </div>
            </article>

            <article class="module-card p-6 xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Usuarios atendidos por mes</h3>
                    <span class="badge badge-success">Linea mensual</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-atenciones"></canvas>
                </div>
            </article>
        </div>

        <article class="module-card p-6 mt-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900">Medicamentos mas pedidos</h3>
                <span class="badge badge-success">Top demanda</span>
            </div>
            <div class="mt-4 h-[360px]">
                <canvas id="chart-medicamentos"></canvas>
            </div>
        </article>

        <div
            id="dashboard-chart-data"
            class="hidden"
            data-labels-mes='@json($labelsMes)'
            data-clientes='@json($clientesPorMes)'
            data-atenciones='@json($atencionesPorMes)'
            data-top-labels='@json($topMedicamentosLabels)'
            data-top-valores='@json($topMedicamentosValores)'
        ></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const dataElement = document.getElementById('dashboard-chart-data');
            if (!dataElement) {
                return;
            }

            const labelsMes = JSON.parse(dataElement.dataset.labelsMes || '[]');
            const clientesPorMes = JSON.parse(dataElement.dataset.clientes || '[]');
            const atencionesPorMes = JSON.parse(dataElement.dataset.atenciones || '[]');
            const topMedicamentosLabels = JSON.parse(dataElement.dataset.topLabels || '[]');
            const topMedicamentosValores = JSON.parse(dataElement.dataset.topValores || '[]');

            const clientesCtx = document.getElementById('chart-clientes');
            const atencionesCtx = document.getElementById('chart-atenciones');
            const medicamentosCtx = document.getElementById('chart-medicamentos');

            if (!clientesCtx || !atencionesCtx || !medicamentosCtx || typeof Chart === 'undefined') {
                return;
            }

            new Chart(clientesCtx, {
                type: 'bar',
                data: {
                    labels: labelsMes,
                    datasets: [{
                        label: 'Clientes',
                        data: clientesPorMes,
                        backgroundColor: 'rgba(14, 116, 144, 0.8)',
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });

            new Chart(atencionesCtx, {
                type: 'line',
                data: {
                    labels: labelsMes,
                    datasets: [{
                        label: 'Atenciones',
                        data: atencionesPorMes,
                        fill: true,
                        tension: 0.35,
                        borderColor: 'rgba(13, 148, 136, 1)',
                        backgroundColor: 'rgba(20, 184, 166, 0.16)',
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });

            new Chart(medicamentosCtx, {
                type: 'bar',
                data: {
                    labels: topMedicamentosLabels,
                    datasets: [{
                        label: 'Cantidad solicitada',
                        data: topMedicamentosValores,
                        backgroundColor: 'rgba(15, 118, 110, 0.78)',
                        borderRadius: 8,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });
        })();
        </script>
    @elseif ($rol === 'despachador')
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Entregas pendientes</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $entregasPendientes }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Entregas completadas (mes)</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $entregasCompletadasMes }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Inventario bajo stock</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $inventarioBajoStock }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lotes por vencer (30 dias)</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $inventarioPorVencer }}</p>
            </article>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('entregas.index') }}" class="btn btn-primary">Gestionar entregas</a>
            <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">Revisar inventario</a>
            <a href="{{ route('medicamentos.index') }}" class="btn btn-secondary">Catalogo de medicamentos</a>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-5">
            <article class="module-card p-6 xl:col-span-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Entregas registradas por mes</h3>
                    <span class="badge badge-success">Ultimos 6 meses</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-despachador-entregas"></canvas>
                </div>
            </article>

            <article class="module-card p-6 xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Medicamentos mas despachados</h3>
                    <span class="badge badge-success">Top despacho</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-despachador-top"></canvas>
                </div>
            </article>
        </div>

        <div
            id="dashboard-chart-despachador"
            class="hidden"
            data-labels-mes='@json($labelsMes)'
            data-entregas='@json($entregasPorMes)'
            data-top-labels='@json($despachoTopLabels)'
            data-top-valores='@json($despachoTopValores)'
        ></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const dataElement = document.getElementById('dashboard-chart-despachador');
            if (!dataElement) {
                return;
            }

            const labelsMes = JSON.parse(dataElement.dataset.labelsMes || '[]');
            const entregasPorMes = JSON.parse(dataElement.dataset.entregas || '[]');
            const topLabels = JSON.parse(dataElement.dataset.topLabels || '[]');
            const topValores = JSON.parse(dataElement.dataset.topValores || '[]');

            const entregasCtx = document.getElementById('chart-despachador-entregas');
            const topCtx = document.getElementById('chart-despachador-top');

            if (!entregasCtx || !topCtx || typeof Chart === 'undefined') {
                return;
            }

            new Chart(entregasCtx, {
                type: 'line',
                data: {
                    labels: labelsMes,
                    datasets: [{
                        label: 'Entregas',
                        data: entregasPorMes,
                        fill: true,
                        tension: 0.35,
                        borderColor: 'rgba(14, 116, 144, 1)',
                        backgroundColor: 'rgba(14, 116, 144, 0.14)',
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });

            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: 'Cantidad entregada',
                        data: topValores,
                        backgroundColor: 'rgba(15, 118, 110, 0.78)',
                        borderRadius: 8,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });
        })();
        </script>
    @elseif ($rol === 'cliente')
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Mis formulas</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $misFormulas }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Formulas activas</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $misFormulasActivas }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Entregas recibidas</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $misEntregas }}</p>
            </article>
            <article class="module-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Citas pendientes</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $misCitasPendientes }}</p>
            </article>
        </div>

        <article class="module-card p-6 mt-6">
            <h3 class="text-lg font-semibold text-slate-900">Proxima cita</h3>
            @if ($proximaCita)
                <p class="mt-2 text-sm text-slate-600">
                    {{ \Carbon\Carbon::parse($proximaCita->fecha_cita)->format('d/m/Y') }}
                    a las {{ substr((string) $proximaCita->hora_cita, 0, 5) }}
                    ({{ ucfirst((string) $proximaCita->estado) }}).
                </p>
            @else
                <p class="mt-2 text-sm text-slate-600">No tienes citas proximas programadas.</p>
            @endif
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('formulas.index') }}" class="btn btn-primary">Ver mis formulas</a>
                <a href="{{ route('formulas.create') }}" class="btn btn-secondary">Registrar formula</a>
                <a href="{{ route('citas.index') }}" class="btn btn-secondary">Consultar citas</a>
            </div>
        </article>

        <div class="mt-6 grid gap-6 xl:grid-cols-5">
            <article class="module-card p-6 xl:col-span-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Mis formulas por mes</h3>
                    <span class="badge badge-success">Ultimos 6 meses</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-cliente-formulas"></canvas>
                </div>
            </article>

            <article class="module-card p-6 xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">Mis citas por mes</h3>
                    <span class="badge badge-success">Seguimiento</span>
                </div>
                <div class="mt-4 h-[320px]">
                    <canvas id="chart-cliente-citas"></canvas>
                </div>
            </article>
        </div>

        <div
            id="dashboard-chart-cliente"
            class="hidden"
            data-labels-mes='@json($labelsMes)'
            data-formulas='@json($misFormulasPorMes)'
            data-citas='@json($misCitasPorMes)'
        ></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const dataElement = document.getElementById('dashboard-chart-cliente');
            if (!dataElement) {
                return;
            }

            const labelsMes = JSON.parse(dataElement.dataset.labelsMes || '[]');
            const formulasPorMes = JSON.parse(dataElement.dataset.formulas || '[]');
            const citasPorMes = JSON.parse(dataElement.dataset.citas || '[]');

            const formulasCtx = document.getElementById('chart-cliente-formulas');
            const citasCtx = document.getElementById('chart-cliente-citas');

            if (!formulasCtx || !citasCtx || typeof Chart === 'undefined') {
                return;
            }

            new Chart(formulasCtx, {
                type: 'bar',
                data: {
                    labels: labelsMes,
                    datasets: [{
                        label: 'Formulas',
                        data: formulasPorMes,
                        backgroundColor: 'rgba(14, 116, 144, 0.8)',
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });

            new Chart(citasCtx, {
                type: 'line',
                data: {
                    labels: labelsMes,
                    datasets: [{
                        label: 'Citas',
                        data: citasPorMes,
                        fill: true,
                        tension: 0.35,
                        borderColor: 'rgba(13, 148, 136, 1)',
                        backgroundColor: 'rgba(20, 184, 166, 0.16)',
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    plugins: {
                        legend: { display: false },
                    },
                },
            });
        })();
        </script>
    @else
        <article class="module-card p-6">
            <h3 class="text-lg font-semibold text-slate-900">Sin rol asignado</h3>
            <p class="mt-2 text-sm text-slate-600">Solicita a administracion la asignacion de un rol para acceder a los modulos.</p>
        </article>
    @endif
@endsection