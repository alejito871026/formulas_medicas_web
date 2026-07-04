@extends('layouts.app', [
    'title' => 'Dashboard | Gestion de Formulas Medicas',
    'heading' => 'Dashboard Inicial del Proyecto',
    'intro' => 'Esta primera version traduce el alcance identificado en el documento: importacion y validacion de formulas, consulta de disponibilidad, seguimiento de pendientes y agendamiento de visitas.',
])

@section('content')
    <div class="grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold">Entidades principales identificadas</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>Paciente: eje del proceso de reclamacion y seguimiento.</li>
                <li>Formula medica: documento central que habilita la dispensacion.</li>
                <li>Medicamento e inventario: soporte para disponibilidad y control de existencias.</li>
                <li>Entrega: trazabilidad de parciales, pendientes y compromisos.</li>
                <li>Cita: mecanismo para ordenar la atencion y evitar filas.</li>
            </ul>
        </article>

        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold">Documentacion creada</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>MER en formato Mermaid para trasladar a MySQL Workbench.</li>
                <li>Diccionario de datos con tipos, longitudes, nulabilidad y comentarios.</li>
                <li>Rutas iniciales con controladores `index` para cada modulo funcional.</li>
            </ul>
        </article>
    </div>
@endsection