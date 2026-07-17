<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_medicamento_id',
        'user_id',
        'fecha_entrega',
        'cantidad_entregada',
        'estado_entrega',
        'fecha_estimada',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_estimada' => 'date',
    ];

    public function formulaItem()
    {
        return $this->belongsTo(FormulaMedicaItem::class, 'formula_medicamento_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
