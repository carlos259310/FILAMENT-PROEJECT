<?php

namespace App\Filament\Resources\SalidaInventarios\Pages;

use App\Filament\Resources\SalidaInventarios\SalidaInventarioResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Inventario;
use Filament\Notifications\Notification;

class CreateSalidaInventario extends CreateRecord
{
    protected static string $resource = SalidaInventarioResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Buscar inventario en la bodega y producto
        $inventario = Inventario::where('id_bodega', $data['id_bodega'])
            ->where('id_producto', $data['id_producto'])
            ->first();

        // Validar stock suficiente
        if (!$inventario || $inventario->cantidad < $data['cantidad']) {
            Notification::make()
                ->title('Stock insuficiente')
                ->body('No hay suficiente stock en la bodega para este producto.')
                ->danger()
                ->send();

            // Detener la creación lanzando una excepción de validación
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cantidad' => ['No hay suficiente stock en la bodega para este producto.'],
            ]);
        }

        // Actualizar inventario (restar cantidad)
        $inventario->cantidad -= $data['cantidad'];
        $inventario->save();

        // Crear la salida normalmente
        return static::getModel()::create($data);
    }

    //funcion redireccionar
       protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
