<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicamento_id',
        'lote',
        'stock_actual',
        'stock_minimo',
        'fecha_vencimiento',
        'ubicacion',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }
}
