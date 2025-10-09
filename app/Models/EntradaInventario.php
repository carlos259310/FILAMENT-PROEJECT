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

    protected $casts = [
        'cantidad' => 'integer',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    /**
     * Boot del modelo para manejar eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Después de crear una entrada, actualizar el inventario
        static::created(function ($entrada) {
            $entrada->actualizarInventario();
        });
    }

    /**
     * Actualiza el inventario después de registrar una entrada
     */
    public function actualizarInventario(): void
    {
        $inventario = Inventario::firstOrNew([
            'id_producto' => $this->id_producto,
            'id_bodega' => $this->id_bodega,
        ]);

        if ($inventario->exists) {
            // Inventario existente: calcular promedio ponderado
            $cantidadAnterior = $inventario->cantidad;
            $precioCompraAnterior = $inventario->precio_compra;
            $precioVentaAnterior = $inventario->precio_venta;
            
            $cantidadNueva = $this->cantidad;
            $precioCompraNuevo = $this->precio_compra;
            $precioVentaNuevo = $this->precio_venta;
            
            // Nueva cantidad total
            $cantidadTotal = $cantidadAnterior + $cantidadNueva;
            
            // Precio promedio ponderado de compra
            $precioCompraPromedio = (($cantidadAnterior * $precioCompraAnterior) + ($cantidadNueva * $precioCompraNuevo)) / $cantidadTotal;
            
            // Precio promedio ponderado de venta
            $precioVentaPromedio = (($cantidadAnterior * $precioVentaAnterior) + ($cantidadNueva * $precioVentaNuevo)) / $cantidadTotal;
            
            // Actualizar inventario
            $inventario->cantidad = $cantidadTotal;
            $inventario->precio_compra = $precioCompraNuevo; // Último precio de compra
            $inventario->precio_venta = $precioVentaNuevo; // Último precio de venta
            $inventario->precio_compra_promedio = round($precioCompraPromedio, 2);
            $inventario->precio_venta_promedio = round($precioVentaPromedio, 2);
        } else {
            // Inventario nuevo
            $inventario->cantidad = $this->cantidad;
            $inventario->precio_compra = $this->precio_compra;
            $inventario->precio_venta = $this->precio_venta;
            $inventario->precio_compra_promedio = $this->precio_compra;
            $inventario->precio_venta_promedio = $this->precio_venta;
        }
        
        $inventario->save();
    }

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
}