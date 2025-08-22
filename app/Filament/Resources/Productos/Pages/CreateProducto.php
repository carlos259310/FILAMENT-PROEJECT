<?php


namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Bodega;
use App\Models\Inventario;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected function afterCreate(): void
    {
        //funcion crear inventario por cada bodega
        $producto = $this->record;
        $bodegas = Bodega::all();

        foreach ($bodegas as $bodega) {
            Inventario::create([
                'id_producto' => $producto->id,
                'id_bodega' => $bodega->id,
                'cantidad' => 0,
                'precio_compra' => 0,
                'precio_venta' => 0,
                'precio_compra_promedio' => 0,
                'precio_venta_promedio' => 0
            ]);
        }
    }
//funcion redireccionar
       protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
