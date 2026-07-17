<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaMedicaItem extends Model
{
    use HasFactory;

    protected $table = 'formula_medicamento';

    protected $fillable = [
        'formula_medica_id',
        'medicamento_id',
        'cantidad_formulada',
        'cantidad_entregada',
        'dosis',
        'frecuencia',
        'estado_item',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function formulaMedica()
    {
        return $this->belongsTo(FormulaMedica::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'formula_medicamento_id');
    }
}
