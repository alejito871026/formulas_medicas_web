@extends('layouts.app', [
    'title' => $pageTitle . ' | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">{{ $pageTitle }}</p>
            <p class="module-nav-subtitle">{{ $entitySummary }}</p>
        </div>
    </div>
@endsection

@section('content')
    <article class="module-card">
        <div class="module-toolbar">
            <p class="module-toolbar-label">Campos clave del modulo</p>
            <span class="field-help">Estructura base del proceso administrativo.</span>
        </div>

        <div class="p-4">
            <div class="flex flex-wrap gap-3">
                @foreach ($keyFields as $field)
                    <span class="badge badge-success">{{ $field }}</span>
                @endforeach
            </div>
        </div>
    </article>
@endsection