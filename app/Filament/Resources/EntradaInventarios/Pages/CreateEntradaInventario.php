<?php


namespace App\Filament\Resources\EntradaInventarios\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Inventario;

class CreateEntradaInventario extends CreateRecord
{
    protected static string $resource = EntradaInventarioResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Crea la entrada normalmente
        $entrada = static::getModel()::create($data);

        // Actualiza el inventario de la bodega y producto
        $inventario = Inventario::where('id_bodega', $data['id_bodega'])
            ->where('id_producto', $data['id_producto'])
            ->first();

        if ($inventario) {
            $inventario->cantidad += $data['cantidad'];
            $inventario->save();
        } else {
            Inventario::create([
                'id_bodega' => $data['id_bodega'],
                'id_producto' => $data['id_producto'],
                'cantidad' => $data['cantidad'],
                'precio_compra' => $data['precio_compra'] ?? 0,
                'precio_venta' => $data['precio_venta'] ?? 0,
                'precio_compra_promedio' => $data['precio_compra_promedio'] ?? 0,
                'precio_venta_promedio' => $data['precio_venta_promedio'] ?? 0,
            ]);
        }

        return $entrada;
    }
}