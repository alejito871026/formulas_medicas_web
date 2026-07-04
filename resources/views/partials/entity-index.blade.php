@extends('layouts.app', [
    'title' => $pageTitle . ' | Gestion de Formulas Medicas',
    'heading' => $pageTitle,
    'intro' => $entitySummary,
])

@section('content')
    <div class="grid gap-6">
        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">Campos clave de la entidad</h3>
            <div class="mt-5 flex flex-wrap gap-3">
                @foreach ($keyFields as $field)
                    <span class="rounded-full bg-teal-50 px-4 py-2 text-sm font-medium text-teal-800 ring-1 ring-teal-100">{{ $field }}</span>
                @endforeach
            </div>
        </article>
    </div>
@endsection