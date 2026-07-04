<?php

namespace App\Http\Controllers;

class MedicamentoController extends Controller
{
    public function index()
    {
        return view('medicamentos.index', [
            'pageTitle' => 'Medicamentos',
            'entitySummary' => 'Catalogo base del dispensario para consulta de disponibilidad, presentacion y trazabilidad farmacologica.',
            'keyFields' => ['codigo', 'nombre', 'presentacion', 'concentracion', 'requiere_formula'],
        ]);
    }
}