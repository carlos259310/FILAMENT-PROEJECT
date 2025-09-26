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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}