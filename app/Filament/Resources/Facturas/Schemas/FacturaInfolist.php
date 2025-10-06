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
                TextEntry::make('prefijo_numero')
                    ->label('Número de Factura')
                    ->getStateUsing(fn($record) => $record->prefijo . $record->numero_factura)
                    ->icon('heroicon-o-document-text')
                    ->badge()
                    ->color('primary')
                    ->size('lg')
                    ->weight('bold'),

                TextEntry::make('fecha_factura')
                    ->label('Fecha de Factura')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar-days'),

                TextEntry::make('estado_nombre')
                    ->label('Estado')
                    ->getStateUsing(fn($record) => $record->estado->nombre)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Pendiente' => 'warning',
                        'Pagada' => 'success',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state) {
                        'Pendiente' => 'heroicon-o-clock',
                        'Pagada' => 'heroicon-o-check-circle',
                        'Cancelada' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextEntry::make('cliente_info')
                    ->label('Cliente')
                    ->getStateUsing(function($record) {
                        $cliente = $record->cliente;
                        if ($cliente->id_tipo_persona == 1 || !$cliente->razon_social) {
                            // Persona Natural
                            $nombre = trim(
                                ($cliente->nombre_1 ?? '') . ' ' . 
                                ($cliente->nombre_2 ?? '') . ' ' . 
                                ($cliente->apellido_1 ?? '') . ' ' . 
                                ($cliente->apellido_2 ?? '')
                            );
                            return $nombre;
                        }
                        // Persona Jurídica
                        return $cliente->razon_social ?? $cliente->nombre_1;
                    })
                    ->icon('heroicon-o-user-circle')
                    ->weight('bold'),

                TextEntry::make('cliente_documento')
                    ->label('Documento del Cliente')
                    ->getStateUsing(fn($record) => $record->cliente->numero_documento)
                    ->icon('heroicon-o-identification')
                    ->copyable()
                    ->copyMessage('Documento copiado'),

                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->icon('heroicon-o-calculator')
                    ->weight('semibold'),

                TextEntry::make('total_impuesto')
                    ->label('Total IVA')
                    ->money('COP')
                    ->icon('heroicon-o-receipt-percent')
                    ->weight('semibold'),

                TextEntry::make('total_factura')
                    ->label('TOTAL FACTURA')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes')
                    ->size('lg')
                    ->weight('bold')
                    ->color('success'),

                RepeatableEntry::make('detalles')
                    ->label('Detalle de Productos')
                    ->schema([
                        TextEntry::make('producto_nombre')
                            ->label('Producto')
                            ->getStateUsing(fn($record) => $record->producto->nombre)
                            ->weight('bold')
                            ->icon('heroicon-o-cube'),

                        TextEntry::make('producto_codigo')
                            ->label('Código')
                            ->getStateUsing(fn($record) => $record->producto->codigo ?? 'N/A'),

                        TextEntry::make('bodega_nombre')
                            ->label('Bodega')
                            ->getStateUsing(fn($record) => $record->bodega->nombre)
                            ->icon('heroicon-o-building-storefront'),

                        TextEntry::make('cantidad')
                            ->label('Cantidad')
                            ->suffix(' unidades')
                            ->icon('heroicon-o-archive-box'),

                        TextEntry::make('precio_venta')
                            ->label('Precio Unitario')
                            ->money('COP'),

                        TextEntry::make('porcentaje_impuesto')
                            ->label('IVA')
                            ->suffix('%')
                            ->default(0),

                        TextEntry::make('total_linea')
                            ->label('Total Línea')
                            ->money('COP')
                            ->weight('semibold')
                            ->color('success'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i:s')
                    ->icon('heroicon-o-clock')
                    ->color('gray'),

                TextEntry::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i:s')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray'),
            ]);
    }
}