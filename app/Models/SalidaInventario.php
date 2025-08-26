<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaInventario extends Model
{
    use HasFactory;

    protected $table = 'salidas_inventario';

    protected $fillable = [
        'id_producto',
        'id_bodega',
        'id_motivo',
        'cantidad',
        'precio_costo',
        'precio_venta',
        'numero_factura',
        'observacion',
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

    public function motivo()
    {
        return $this->belongsTo(MotivoSalida::class, 'id_motivo');
    }
}