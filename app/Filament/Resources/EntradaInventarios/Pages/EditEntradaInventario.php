<?php

namespace App\Filament\Resources\EntradaInventarios\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
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
        $inventario = \App\Models\Inventario::where('id_bodega', $data['id_bodega'])
            ->where('id_producto', $data['id_producto'])
            ->first();

        $cantidadAnterior = $record->cantidad;
        $cantidadNueva = $data['cantidad'];
        $diferencia = $cantidadNueva - $cantidadAnterior;

        // Para entradas, siempre sumamos la diferencia
        if (!$inventario) {
            // Si no existe inventario, lo creamos
            \App\Models\Inventario::create([
                'id_bodega' => $data['id_bodega'],
                'id_producto' => $data['id_producto'],
                'cantidad' => $cantidadNueva,
                'precio_compra' => $data['precio_compra'] ?? 0,
                'precio_venta' => $data['precio_venta'] ?? 0,
                'precio_compra_promedio' => $data['precio_compra_promedio'] ?? 0,
                'precio_venta_promedio' => $data['precio_venta_promedio'] ?? 0,
            ]);
        } else {
            $inventario->cantidad += $diferencia;
            $inventario->save();
        }

        $record->update($data);

        return $record;
    }

    //funcion redireccionar
       protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}