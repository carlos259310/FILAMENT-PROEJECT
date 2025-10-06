<?php


namespace App\Filament\Resources\EntradaInventarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EntradaInventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('codigo')
                ->label('Código')
                ->disabled()
                ->dehydrated()
                ->default(function () {
                    $ultimaEntrada = \App\Models\EntradaInventario::latest()->first();
                    $ultimoId = $ultimaEntrada ? $ultimaEntrada->id : 0;
                    return str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
                }),

            TextInput::make('codigo_barras')
                ->label('Código de Barras')
                ->disabled()
                ->dehydrated()
                ->default(function () {
                    return date('Ymd') . rand(1000, 9999);
                }),

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
                ->required()
                ->minValue(1)
                ->rules(['numeric', 'min:1']),
            TextInput::make('precio_compra')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(1)
                ->rules(['numeric', 'min:1']),

            TextInput::make('precio_venta')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(1)
                ->rule(function ($get) {
                    return function ($attribute, $value, $fail) use ($get) {
                        $precio_compra = $get('precio_compra');
                        if ($precio_compra !== null && $value < $precio_compra) {
                            $fail('El precio de venta debe ser mayor o igual al precio de compra.');
                        }
                    };
                }),

            TextInput::make('numero_factura')
                ->maxLength(50),
            TextInput::make('numero_remision')
                ->maxLength(50),
            Textarea::make('observacion')
                ->rows(3),
        ]);
    }
}
