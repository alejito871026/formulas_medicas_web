<?php

namespace App\Http\Controllers;

class FormulaMedicaController extends Controller
{
    public function index()
    {
        return view('formulas.index', [
            'pageTitle' => 'Formulas Medicas',
            'entitySummary' => 'Registro de formulas medicas importadas, validadas y asociadas a medicamentos y estados de entrega.',
            'keyFields' => ['numero_formula', 'paciente', 'fecha_formula', 'fecha_vencimiento', 'estado'],
        ]);
    }
}