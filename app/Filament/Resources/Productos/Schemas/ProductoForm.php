<?php

namespace App\Filament\Resources\Productos\Schemas;


use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema ->schema([
            TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('id_categoria')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->required(),
            Select::make('id_marca')
                ->label('Marca')
                ->relationship('marca', 'nombre')
                ->required(),
            Select::make('id_proveedor')
                ->label('Proveedor')
                ->relationship('proveedor', 'nombre')
                ->required(),
            TextInput::make('codigo')
                ->required()
                ->maxLength(20),
            TextInput::make('codigo_barras')
                ->maxLength(255),
            Textarea::make('descripcion')
                ->rows(3),
            Toggle::make('activo')
                ->default(true),
        ]);

    }
}
