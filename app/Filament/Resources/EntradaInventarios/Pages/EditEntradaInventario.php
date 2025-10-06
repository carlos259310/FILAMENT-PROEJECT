<?php

namespace App\Filament\Resources\EntradaInventarios\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
use App\Models\Inventario;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;


class EditEntradaInventario extends EditRecord
{
    protected static string $resource = EntradaInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Obtener datos originales antes de la actualización
        $datosOriginales = [
            'cantidad' => $record->cantidad,
            'precio_compra' => $record->precio_compra,
            'id_bodega' => $record->id_bodega,
            'id_producto' => $record->id_producto
        ];

        // Actualizar el registro
        $record->update($data);

        // Manejar cambios en inventario
        $this->actualizarInventario($datosOriginales, $data);

        return $record;
    }

    private function actualizarInventario(array $datosOriginales, array $datosNuevos): void
    {
        // Revertir inventario original si cambió bodega o producto
        if ($datosOriginales['id_bodega'] !== $datosNuevos['id_bodega'] || 
            $datosOriginales['id_producto'] !== $datosNuevos['id_producto']) {
            
            $inventarioOriginal = Inventario::where('id_bodega', $datosOriginales['id_bodega'])
                ->where('id_producto', $datosOriginales['id_producto'])
                ->first();
                
            if ($inventarioOriginal) {
                $nuevaCantidad = max(0, $inventarioOriginal->cantidad - $datosOriginales['cantidad']);
                
                if ($nuevaCantidad > 0) {
                    $inventarioOriginal->cantidad = $nuevaCantidad;
                    $inventarioOriginal->save();
                } else {
                    $inventarioOriginal->delete();
                }
            }
        }

        // Actualizar inventario destino
        $inventario = Inventario::firstOrNew([
            'id_bodega' => $datosNuevos['id_bodega'],
            'id_producto' => $datosNuevos['id_producto']
        ]);

        if ($inventario->exists) {
            // Si no cambió ubicación, ajustar diferencia
            if ($datosOriginales['id_bodega'] === $datosNuevos['id_bodega'] && 
                $datosOriginales['id_producto'] === $datosNuevos['id_producto']) {
                
                $diferenciaCantidad = $datosNuevos['cantidad'] - $datosOriginales['cantidad'];
                $inventario->cantidad += $diferenciaCantidad;
                
                // Recalcular promedio si hay cambio en precio
                if ($datosOriginales['precio_compra'] !== $datosNuevos['precio_compra']) {
                    $cantidadTotal = $inventario->cantidad;
                    $cantidadAnterior = $cantidadTotal - $datosNuevos['cantidad'];
                    
                    if ($cantidadTotal > 0) {
                        $inventario->precio_compra_promedio = (
                            ($cantidadAnterior * $inventario->precio_compra_promedio) + 
                            ($datosNuevos['cantidad'] * $datosNuevos['precio_compra'])
                        ) / $cantidadTotal;
                    }
                }
            } else {
                // Nueva ubicación: añadir cantidad
                $cantidadAnterior = $inventario->cantidad;
                $precioAnterior = $inventario->precio_compra_promedio ?? 0;
                $cantidadNueva = $datosNuevos['cantidad'];
                $precioNuevo = $datosNuevos['precio_compra'];
                
                $totalCantidad = $cantidadAnterior + $cantidadNueva;
                
                $inventario->cantidad = $totalCantidad;
                $inventario->precio_compra = $precioNuevo;
                
                if ($totalCantidad > 0) {
                    $inventario->precio_compra_promedio = (
                        ($cantidadAnterior * $precioAnterior) + 
                        ($cantidadNueva * $precioNuevo)
                    ) / $totalCantidad;
                }
            }
        } else {
            // Inventario nuevo
            $inventario->cantidad = $datosNuevos['cantidad'];
            $inventario->precio_compra = $datosNuevos['precio_compra'];
            $inventario->precio_venta = $datosNuevos['precio_venta'] ?? 0;
            $inventario->precio_compra_promedio = $datosNuevos['precio_compra'];
            $inventario->precio_venta_promedio = $datosNuevos['precio_venta'] ?? 0;
        }
        
        if ($inventario->cantidad > 0) {
            $inventario->save();
        } elseif ($inventario->exists) {
            $inventario->delete();
        }
    }

    //funcion redireccionar
       protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}