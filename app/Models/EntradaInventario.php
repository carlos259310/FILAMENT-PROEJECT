<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaInventario extends Model
{
    use HasFactory;

    protected $table = 'entradas_inventario';

    protected $fillable = [
        'id_producto',
        'id_bodega',
        'id_motivo',
        'id_factura',
        'cantidad',
        'precio_compra',
        'precio_venta',
        'numero_factura',
        'numero_remision',
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
        return $this->belongsTo(MotivoEntrada::class, 'id_motivo');
    }

 /*   public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }*/
}