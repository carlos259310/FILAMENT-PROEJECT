<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    use HasFactory;

    protected $table = 'bodegas';

    protected $fillable = [
        'nombre',
        'codigo',
        'ubicacion',
        'descripcion',
        'activo',
        'principal'

    ];

    protected $casts = [
        'activo' => 'boolean',
        'principal' => 'boolean',
    ];
}
