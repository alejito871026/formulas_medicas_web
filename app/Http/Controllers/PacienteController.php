<?php

namespace App\Http\Controllers;

class PacienteController extends Controller
{
    public function index()
    {
        return view('pacientes.index', [
            'pageTitle' => 'Pacientes',
            'entitySummary' => 'Gestion de datos personales, contacto, aseguramiento y estado del paciente dentro del proceso de dispensacion.',
            'keyFields' => ['documento', 'nombres', 'apellidos', 'telefono', 'email', 'eps'],
        ]);
    }
}