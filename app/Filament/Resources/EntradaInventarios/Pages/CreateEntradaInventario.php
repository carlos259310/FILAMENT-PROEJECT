<?php

namespace App\Filament\Resources\EntradaInventarios\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEntradaInventario extends CreateRecord
{
    protected static string $resource = EntradaInventarioResource::class;

    /**
     * Mensaje de éxito personalizado
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return '✅ Entrada de Inventario Registrada';
    }

    /**
     * Notificación personalizada con más información
     */
    protected function afterCreate(): void
    {
        $entrada = $this->record;
        
        Notification::make()
            ->success()
            ->title('Entrada Registrada Exitosamente')
            ->body(
                "Producto: {$entrada->producto->nombre}\n" .
                "Bodega: {$entrada->bodega->nombre}\n" .
                "Cantidad: {$entrada->cantidad} unidades\n" .
                "💰 Total: $" . number_format($entrada->cantidad * $entrada->precio_compra, 2)
            )
            ->duration(5000)
            ->send();
    }

    /**
     * Redirección después de crear
     */
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
