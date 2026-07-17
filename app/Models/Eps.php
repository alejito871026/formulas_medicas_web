<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    use HasFactory;

    protected $table = 'eps';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'nombre_contacto',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
