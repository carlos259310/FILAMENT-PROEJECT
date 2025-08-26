<?php

namespace App\Filament\Resources\SalidaInventarios\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalidaInventarioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('producto.nombre')->label('Producto'),
                TextEntry::make('bodega.nombre')->label('Bodega'),
                TextEntry::make('motivo.nombre')->label('Motivo'),
                TextEntry::make('cantidad')->label('Cantidad'),
                TextEntry::make('precio_costo')->label('Precio Costo'),
                TextEntry::make('precio_venta')->label('Precio Venta'),
                TextEntry::make('numero_factura')->label('N° Factura'),
                TextEntry::make('observacion')->label('Observación'),
                TextEntry::make('created_at')->dateTime()->label('Creado'),
                TextEntry::make('updated_at')->dateTime()->label('Actualizado'),
            ]);
    }
}