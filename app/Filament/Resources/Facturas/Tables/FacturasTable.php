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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('cliente.nombre_1')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('numero_factura')
                    ->label('N° Factura')
                    ->sortable(),
                TextColumn::make('fecha_factura')
                    ->label('Fecha')
                    ->date(),
                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state === 'Pendiente' ? 'warning' : ($state === 'Pagada' ? 'success' : 'danger'),
                    ),
                TextColumn::make('total_factura')
                    ->label('Total')
                    ->money('COP'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Puedes agregar filtros aquí si lo necesitas
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->button()
                    ->modalHeading('Cambiar estado de la factura')
                    ->modalSubmitActionLabel('Actualizar Estado')
                    ->form([
                        \Filament\Forms\Components\Select::make('id_estado')
                            ->label('Nuevo Estado')
                            ->options(\App\Models\EstadoFactura::all()->pluck('nombre', 'id'))
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->estado->nombre === 'Pendiente')
                    ->action(function ($record, array $data) {
                        $record->id_estado = $data['id_estado'];
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Estado actualizado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}