<?php

namespace App\Http\Controllers;

class EntregaController extends Controller
{
    public function index()
    {
        return view('entregas.index', [
            'pageTitle' => 'Entregas',
            'entitySummary' => 'Seguimiento de entregas completas o parciales, con fechas de compromiso y responsable de dispensacion.',
            'keyFields' => ['formula', 'medicamento', 'cantidad_entregada', 'estado_entrega', 'fecha_entrega'],
        ]);
    }
}