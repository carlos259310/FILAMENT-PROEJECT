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
        // Generar códigos automáticos si no están presentes
        if (empty($data['codigo'])) {
            $ultimaEntrada = static::getModel()::latest()->first();
            $ultimoId = $ultimaEntrada ? $ultimaEntrada->id : 0;
            $data['codigo'] = str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
        }
        
        if (empty($data['codigo_barras'])) {
            $data['codigo_barras'] = date('Ymd') . rand(1000, 9999);
        }

        // Crear la entrada
        $entrada = static::getModel()::create($data);

        // Buscar o crear inventario
        $inventario = Inventario::firstOrNew([
            'id_bodega' => $data['id_bodega'],
            'id_producto' => $data['id_producto']
        ]);

        if ($inventario->exists) {
            // Inventario existente: calcular nuevo promedio ponderado
            $cantidadAnterior = $inventario->cantidad ?? 0;
            $precioAnterior = $inventario->precio_compra ?? 0;
            $cantidadNueva = $data['cantidad'];
            $precioNuevo = $data['precio_compra'];
            
            $totalCantidad = $cantidadAnterior + $cantidadNueva;
            $nuevoPrecioPromedio = $totalCantidad > 0 ? 
                (($cantidadAnterior * $precioAnterior) + ($cantidadNueva * $precioNuevo)) / $totalCantidad : 0;

            $inventario->cantidad = $totalCantidad;
            $inventario->precio_compra = $precioNuevo;
            $inventario->precio_venta = $data['precio_venta'] ?? $inventario->precio_venta;
            $inventario->precio_compra_promedio = $nuevoPrecioPromedio;
        } else {
            // Inventario nuevo
            $inventario->cantidad = $data['cantidad'];
            $inventario->precio_compra = $data['precio_compra'];
            $inventario->precio_venta = $data['precio_venta'] ?? 0;
            $inventario->precio_compra_promedio = $data['precio_compra'];
            $inventario->precio_venta_promedio = $data['precio_venta'] ?? 0;
        }
        
        $inventario->save();

        return $entrada;
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
