<?php

namespace App\Http\Controllers;

class CitaController extends Controller
{
    public function index()
    {
        return view('citas.index', [
            'pageTitle' => 'Citas',
            'entitySummary' => 'Planeacion de visitas del paciente al dispensario para evitar filas y ordenar la capacidad operativa.',
            'keyFields' => ['paciente', 'fecha_cita', 'hora_cita', 'motivo', 'estado'],
        ]);
    }
}