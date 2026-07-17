<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulaMedica extends Model
{
    use HasFactory;

    protected $table = 'formulas_medicas';

    protected $fillable = [
        'paciente_id',
        'numero_formula',
        'fecha_formula',
        'fecha_vencimiento',
        'medico_tratante',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_formula' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\Paciente');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FormulaMedicaItem::class, 'formula_medica_id');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
