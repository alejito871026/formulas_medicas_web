<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'principio_activo',
        'presentacion',
        'concentracion',
        'unidad_medida',
        'requiere_formula',
        'observaciones',
    ];

    protected $casts = [
        'requiere_formula' => 'boolean',
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function formulaItems()
    {
        return $this->hasMany(FormulaMedicaItem::class);
    }
}
