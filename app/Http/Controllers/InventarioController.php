<?php

namespace App\Http\Controllers;

class InventarioController extends Controller
{
    public function index()
    {
        return view('inventarios.index', [
            'pageTitle' => 'Inventario',
            'entitySummary' => 'Control de existencias por medicamento, lote, fecha de vencimiento y punto de dispensacion.',
            'keyFields' => ['medicamento', 'lote', 'stock_actual', 'stock_minimo', 'fecha_vencimiento'],
        ]);
    }
}