<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'formula_medica_id',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function formulaMedica()
    {
        return $this->belongsTo(FormulaMedica::class);
    }
}
