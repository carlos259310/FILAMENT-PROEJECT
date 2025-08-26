<?php


namespace App\Filament\Resources\SalidaInventarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalidaInventariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable(),
                TextColumn::make('bodega.nombre')
                    ->label('Bodega')
                    ->searchable(),
                TextColumn::make('motivo.nombre')
                    ->label('Motivo')
                    ->searchable(),
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('precio_costo')
                    ->label('Precio Costo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('numero_factura')
                    ->label('N° Factura')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('observacion')
                    ->label('Observación')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
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