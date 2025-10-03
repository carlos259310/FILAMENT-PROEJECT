<?php

namespace App\Filament\Resources\InventarioGenerates\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
use App\Filament\Resources\InventarioGenerates\InventarioGenerateResource;
use App\Filament\Resources\SalidaInventarios\SalidaInventarioResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListInventarioGenerates extends ListRecords
{
    protected static string $resource = InventarioGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crear_entrada')
                ->label('📥 Crear Entrada')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->url(fn (): string => EntradaInventarioResource::getUrl('create'))
                ->tooltip('Registrar entrada de productos al inventario'),
            
            Action::make('crear_salida')
                ->label('📤 Crear Salida')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('danger')
                ->url(fn (): string => SalidaInventarioResource::getUrl('create'))
                ->tooltip('Registrar salida de productos del inventario'),
        ];
    }
}
