<?php

namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\RepeatableEntry;

use Filament\Schemas\Schema;

class FacturaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cliente.nombre_1')
                    ->label('Cliente'),
                TextEntry::make('estado.nombre')
                    ->label('Estado'),
                TextEntry::make('prefijo')
                    ->label('Prefijo'),
                TextEntry::make('numero_factura')
                    ->label('Número de Factura'),
                TextEntry::make('fecha_factura')
                    ->label('Fecha')
                    ->date(),
                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->numeric(),
                TextEntry::make('total_impuesto')
                    ->label('Total Impuesto')
                    ->numeric(),
                TextEntry::make('total_factura')
                    ->label('Total Factura')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime(),
                RepeatableEntry::make('detalles')
                    ->label('Detalle de Productos')
                    ->schema([
                        TextEntry::make('producto.nombre')
                            ->label('Producto')
                            ->weight('bold'),
                        TextEntry::make('bodega.nombre')
                            ->label('Bodega'),
                        TextEntry::make('cantidad')
                            ->label('Cantidad')
                            ->numeric(),
                        TextEntry::make('precio_venta')
                            ->label('Precio Unitario')
                            ->money('COP'),
                        TextEntry::make('subtotal_linea')
                            ->label('Subtotal')
                            ->money('COP')
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}