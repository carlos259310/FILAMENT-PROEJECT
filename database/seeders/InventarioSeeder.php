<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Bodega;
use App\Models\EntradaInventario;
use App\Models\MotivoEntrada;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inventarios')->delete();
        DB::table('entradas_inventario')->delete();

        $productos = Producto::all();
        $bodegas = Bodega::all();
        $motivoCompra = MotivoEntrada::where('nombre', 'Compra')->first();

        if (!$motivoCompra) {
            $motivoCompra = MotivoEntrada::create(['nombre' => 'Compra']);
        }

        foreach ($productos as $producto) {
            // Selecciona una bodega aleatoria para este producto
            $bodega = $bodegas->random();

            $cantidad = rand(10, 100);
            $precioCompra = rand(1000, 5000);
            $precioVenta = $precioCompra + rand(500, 2000);

            // Crear entrada de inventario (histórico)
            EntradaInventario::create([
                'id_producto'     => $producto->id,
                'id_bodega'       => $bodega->id,
                'id_motivo'       => $motivoCompra->id,
                'cantidad'        => $cantidad,
                'precio_compra'   => $precioCompra,
                'precio_venta'    => $precioVenta,
                'numero_factura'  => 'FAC-' . rand(1000, 9999),
                'numero_remision' => 'REM-' . rand(1000, 9999),
                'observacion'     => 'Carga inicial de inventario por compra',
            ]);

            // Crear o actualizar inventario actual
            Inventario::updateOrCreate(
                [
                    'id_producto' => $producto->id,
                    'id_bodega'   => $bodega->id,
                ],
                [
                    'cantidad'     => $cantidad,
                    'precio_venta' => $precioVenta,
                ]
            );
        }
    }
}