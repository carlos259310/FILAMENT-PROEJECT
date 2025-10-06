<?php

namespace App\Filament\Resources\Facturas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacturasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_completo')
                    ->label('N° Factura')
                    ->getStateUsing(fn($record) => $record->prefijo . $record->numero_factura)
                    ->searchable(['prefijo', 'numero_factura'])
                    ->sortable(['numero_factura'])
                    ->icon('heroicon-o-document-text')
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->weight('bold'),

                TextColumn::make('cliente_nombre')
                    ->label('Cliente')
                    ->getStateUsing(function($record) {
                        $cliente = $record->cliente;
                        if ($cliente->id_tipo_persona == 1 || !$cliente->razon_social) {
                            // Persona Natural
                            return trim(
                                ($cliente->nombre_1 ?? '') . ' ' . 
                                ($cliente->apellido_1 ?? '')
                            );
                        }
                        // Persona Jurídica
                        return $cliente->razon_social ?? $cliente->nombre_1;
                    })
                    ->searchable(['cliente.nombre_1', 'cliente.apellido_1', 'cliente.razon_social', 'cliente.numero_documento'])
                    ->icon('heroicon-o-user-circle')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->cliente->numero_documento;
                    }),

                TextColumn::make('fecha_factura')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),

                TextColumn::make('estado_nombre')
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
                    })
                    ->sortable(['estado.nombre']),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_impuesto')
                    ->label('IVA')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_factura')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->icon('heroicon-o-banknotes')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),

                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Puedes agregar filtros aquí si lo necesitas
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(fn($record) => $record->estado->nombre !== 'Pagada' && $record->estado->nombre !== 'Cancelada'),
                \Filament\Actions\Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading('Cambiar estado de la factura')
                    ->modalDescription('Si cambia a "Cancelada", se generará una devolución de inventario automáticamente.')
                    ->modalSubmitActionLabel('Actualizar Estado')
                    ->form([
                        \Filament\Forms\Components\Select::make('id_estado')
                            ->label('Nuevo Estado')
                            ->options(\App\Models\EstadoFactura::all()->pluck('nombre', 'id'))
                            ->native(false)
                            ->required(),
                    ])
                    ->visible(fn($record) => $record->estado->nombre === 'Pendiente')
                    ->action(function ($record, array $data) {
                        $nuevoEstado = \App\Models\EstadoFactura::find($data['id_estado']);
                        
                        if (!$nuevoEstado) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error al cambiar estado')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Si el nuevo estado es "Cancelada", generar devolución
                        if ($nuevoEstado->nombre === 'Cancelada') {
                            // Buscar o crear el motivo "Devolución"
                            $motivoDevolucion = \App\Models\MotivoEntrada::firstOrCreate(
                                ['nombre' => 'Devolución'],
                                ['nombre' => 'Devolución']
                            );

                            // Obtener las salidas de inventario de esta factura
                            $salidas = \App\Models\SalidaInventario::where(
                                'numero_factura',
                                $record->prefijo . $record->numero_factura
                            )->get();

                            // Crear entradas de devolución y restaurar inventario
                            foreach ($salidas as $salida) {
                                // Crear entrada de devolución
                                \App\Models\EntradaInventario::create([
                                    'id_producto' => $salida->id_producto,
                                    'id_bodega' => $salida->id_bodega,
                                    'id_motivo' => $motivoDevolucion->id,
                                    'cantidad' => $salida->cantidad,
                                    'precio_compra' => $salida->precio_costo,
                                    'precio_venta' => $salida->precio_venta,
                                    'numero_factura' => $record->prefijo . $record->numero_factura,
                                    'observacion' => "Devolución por cancelación de factura #{$record->prefijo}{$record->numero_factura}",
                                ]);

                                // Restaurar inventario
                                $inventario = \App\Models\Inventario::where('id_bodega', $salida->id_bodega)
                                    ->where('id_producto', $salida->id_producto)
                                    ->first();

                                if ($inventario) {
                                    $inventario->increment('cantidad', $salida->cantidad);
                                }
                            }

                            // Actualizar estado
                            $record->id_estado = $data['id_estado'];
                            $record->save();

                            \Filament\Notifications\Notification::make()
                                ->title('Estado actualizado correctamente')
                                ->body('Se ha generado la devolución de inventario automáticamente.')
                                ->success()
                                ->send();
                        } else {
                            // Cambio normal de estado
                            $record->id_estado = $data['id_estado'];
                            $record->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Estado actualizado correctamente')
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ]);
    }
}