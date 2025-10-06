<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'id_producto',
        'id_bodega',
        'cantidad',
        'precio_compra',
        'precio_venta',
        'precio_compra_promedio',
        'precio_venta_promedio',
    ];
    //ocultos
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'cantidad' => 'integer',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'precio_compra_promedio' => 'decimal:2',
        'precio_venta_promedio' => 'decimal:2',
    ];

        // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    // Scopes útiles
    public function scopeConStock($query)
    {
        return $query->where('cantidad', '>', 0);
    }

    public function scopePorBodega($query, $bodegaId)
    {
        return $query->where('id_bodega', $bodegaId);
    }

    public function scopeConProducto($query)
    {
        return $query->with('producto');
    }
}
