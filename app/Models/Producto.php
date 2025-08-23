<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'codigo',
        'codigo_barras',
        'descripcion',
        'id_categoria',
        'id_marca',
        'id_proveedor',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];


    //relaciones
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    // ...existing code...
    public function entradasInventario()
    {
        return $this->hasMany(EntradaInventario::class, 'id_producto');
    }
    // ...existing code...

        public function inventario()
    {
        return $this->hasMany(Inventario::class, 'id_producto');
    }


    
}
