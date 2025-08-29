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
            Select::make('id_bodega')
                ->label('Bodega')
                ->relationship('bodega', 'nombre')
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $bodegaId = $get('id_bodega');
                    $productoId = $get('id_producto');
                    
                    if ($bodegaId && $productoId) {
                        $inventario = \App\Models\Inventario::where('id_bodega', $bodegaId)
                            ->where('id_producto', $productoId)
                            ->select('cantidad')
                            ->first();
                        $cantidad = $inventario ? $inventario->cantidad : 0;
                        $set('cantidad_disponible', $cantidad);
                    } else {
                        $set('cantidad_disponible', 0);
                    }
                }),

            Select::make('id_producto')
                ->label('Producto')
                ->options(function ($get) {
                    $bodegaId = $get('id_bodega');
                    if (!$bodegaId) {
                        return [];
                    }
                    
                    return \App\Models\Inventario::where('id_bodega', $bodegaId)
                        ->where('cantidad', '>', 0)
                        ->with('producto')
                        ->get()
                        ->pluck('producto.nombre', 'producto.id')
                        ->toArray();
                })
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $bodegaId = $get('id_bodega');
                    $productoId = $get('id_producto');
                    
                    if ($bodegaId && $productoId) {
                        $inventario = \App\Models\Inventario::where('id_bodega', $bodegaId)
                            ->where('id_producto', $productoId)
                            ->select('cantidad')
                            ->first();
                        $cantidad = $inventario ? $inventario->cantidad : 0;
                        $set('cantidad_disponible', $cantidad);
                    } else {
                        $set('cantidad_disponible', 0);
                    }
                }),

            TextInput::make('cantidad_disponible')
                ->label('Cantidad disponible')
                ->disabled()
                ->reactive()
                ->dehydrated(false)
                ->default(function ($get) {
                    $bodegaId = $get('id_bodega');
                    $productoId = $get('id_producto');
                    if (!$bodegaId || !$productoId) {
                        return 0;
                    }
                    $inventario = \App\Models\Inventario::where('id_bodega', $bodegaId)
                        ->where('id_producto', $productoId)
                        ->select('cantidad')
                        ->first();
                    return $inventario ? $inventario->cantidad : 0;
                }),

            Select::make('id_motivo')
                ->label('Motivo')
                ->relationship('motivo', 'nombre')
                ->required(),

            TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(1)
                ->rules(['numeric', 'min:1']),

            TextInput::make('precio_costo')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(0)
                ->rules(['numeric', 'min:0']),

            TextInput::make('precio_venta')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(1)
                ->rule(function ($get) {
                    return function ($attribute, $value, $fail) use ($get) {
                        $precio_costo = $get('precio_costo');
                        if ($precio_costo !== null && $value < $precio_costo) {
                            $fail('El precio de venta debe ser mayor o igual al precio de costo.');
                        }
                    };
                }),

            TextInput::make('numero_factura')
                ->maxLength(50),

            Textarea::make('observacion')
                ->rows(3),
        ]);
    }
}