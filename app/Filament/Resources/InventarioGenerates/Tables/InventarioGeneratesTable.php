<?php

namespace App\Filament\Resources\InventarioGenerates\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventarioGeneratesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('bodega.nombre')
                    ->label('Bodega')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('cantidad')
                    ->label('Stock Disponible')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->icon(fn ($state): string => match (true) {
                        $state === 0 => 'heroicon-o-x-circle',
                        $state < 10 => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    }),
                TextColumn::make('precio_compra')
                    ->label('Precio Compra')
                    ->money('COP')
                    ->sortable(),
                TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->money('COP')
                    ->sortable(),
                TextColumn::make('precio_compra_promedio')
                    ->label('Compra Promedio')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('precio_venta_promedio')
                    ->label('Venta Promedio')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('id_bodega')
                    ->label('Bodega')
                    ->relationship('bodega', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('id_producto')
                    ->label('Producto')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped();
    }
}
