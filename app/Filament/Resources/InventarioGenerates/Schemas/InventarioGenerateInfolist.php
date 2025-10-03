<?php

namespace App\Filament\Resources\InventarioGenerates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventarioGenerateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('producto.codigo')
                    ->label('Código de Producto')
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->color('primary'),
                
                TextEntry::make('producto.nombre')
                    ->label('Nombre de Producto')
                    ->icon('heroicon-o-cube')
                    ->weight('bold'),
                
                TextEntry::make('bodega.nombre')
                    ->label('Bodega')
                    ->icon('heroicon-o-building-storefront')
                    ->badge()
                    ->color('info'),
                
                TextEntry::make('cantidad')
                    ->label('Stock Disponible')
                    ->icon('heroicon-o-archive-box')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->suffix(' unidades'),
                
                TextEntry::make('precio_compra')
                    ->label('Precio de Compra Actual')
                    ->money('COP')
                    ->icon('heroicon-o-currency-dollar'),
                
                TextEntry::make('precio_compra_promedio')
                    ->label('Precio de Compra Promedio')
                    ->money('COP')
                    ->icon('heroicon-o-chart-bar'),
                
                TextEntry::make('precio_venta')
                    ->label('Precio de Venta Actual')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes'),
                
                TextEntry::make('precio_venta_promedio')
                    ->label('Precio de Venta Promedio')
                    ->money('COP')
                    ->icon('heroicon-o-chart-bar'),
                
                TextEntry::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i:s')
                    ->icon('heroicon-o-clock'),
                
                TextEntry::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i:s')
                    ->icon('heroicon-o-arrow-path'),
            ]);
    }
}
