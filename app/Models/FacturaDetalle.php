<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaDetalle extends Model
{
    use HasFactory;

    protected $table = 'facturas_detalle';

    protected $fillable = [
        'id_factura',
        'id_producto',
        'id_bodega',
        'cantidad',
        'precio_venta',
        'impuesto',
        'porcentaje_impuesto',
        'subtotal_linea',
        'total_linea',
    ];

    // Relaciones
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }
}