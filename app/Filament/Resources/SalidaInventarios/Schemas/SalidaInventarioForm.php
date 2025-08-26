<?php

namespace App\Filament\Resources\SalidaInventarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SalidaInventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('id_producto')
                ->label('Producto')
                ->relationship('producto', 'nombre')
                ->required(),
            Select::make('id_bodega')
                ->label('Bodega')
                ->relationship('bodega', 'nombre')
                ->required(),
            Select::make('id_motivo')
                ->label('Motivo')
                ->relationship('motivo', 'nombre')
                ->required(),
            TextInput::make('cantidad')
                ->numeric()
                ->required(),
            TextInput::make('precio_costo')
                ->numeric()
                ->prefix('$'),
            TextInput::make('precio_venta')
                ->numeric()
                ->prefix('$'),
            TextInput::make('numero_factura')
                ->maxLength(50),
            Textarea::make('observacion')
                ->rows(3),
        ]);
    }
}