<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
  use HasFactory;
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }
}
