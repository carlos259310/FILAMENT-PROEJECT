<?php


namespace App\Filament\Resources\EntradaInventarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class EntradaInventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            
            // Sección: Información del Producto
            Placeholder::make('info_producto')
                ->label('📦 Información del Producto y Bodega')
                ->content('Selecciona el producto y la bodega donde ingresará el inventario')
                ->columnSpanFull(),
            
            Select::make('id_producto')
                ->label('Producto')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $producto = \App\Models\Producto::find($state);
                        if ($producto) {
                            $set('producto_info', "📋 Código: {$producto->codigo}");
                        }
                    }
                })
                ->helperText('Busca y selecciona el producto')
                ->columnSpan(1),
            
            Select::make('id_bodega')
                ->label('Bodega')
                ->relationship('bodega', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $idProducto = $get('id_producto');
                    if ($state && $idProducto) {
                        $inventario = \App\Models\Inventario::where('id_producto', $idProducto)
                            ->where('id_bodega', $state)
                            ->first();
                        if ($inventario) {
                            $set('stock_actual', "📊 Stock actual: {$inventario->cantidad} unidades");
                        } else {
                            $set('stock_actual', "📊 Stock actual: 0 unidades (Nuevo)");
                        }
                    }
                })
                ->helperText('Selecciona la bodega destino')
                ->columnSpan(1),
            
            Select::make('id_motivo')
                ->label('Motivo de Entrada')
                ->relationship('motivo', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->helperText('Indica el motivo de la entrada')
                ->columnSpan(1),
            
            Placeholder::make('producto_info')
                ->label('')
                ->content('Selecciona un producto para ver su información')
                ->columnSpan(1),
            
            Placeholder::make('stock_actual')
                ->label('')
                ->content('Selecciona producto y bodega para ver el stock')
                ->columnSpan(2),
            
            // Sección: Cantidades y Precios
            Placeholder::make('info_precios')
                ->label('💰 Cantidades y Precios')
                ->content('Ingresa la cantidad y los precios de compra y venta')
                ->columnSpanFull(),
            
            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->required()
                ->minValue(1)
                ->default(1)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $precioCompra = $get('precio_compra');
                    if ($state && $precioCompra) {
                        $total = $state * $precioCompra;
                        $set('total_compra', "💵 Total: $" . number_format($total, 2));
                    }
                })
                ->suffix('unidades')
                ->helperText('Cantidad de unidades a ingresar')
                ->columnSpan(1),
            
            TextInput::make('precio_compra')
                ->label('Precio de Compra')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(0.01)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $cantidad = $get('cantidad');
                    if ($state && $cantidad) {
                        $total = $cantidad * $state;
                        $set('total_compra', "💵 Total: $" . number_format($total, 2));
                    }
                })
                ->helperText('Precio unitario de compra')
                ->columnSpan(1),

            TextInput::make('precio_venta')
                ->label('Precio de Venta')
                ->numeric()
                ->prefix('$')
                ->required()
                ->minValue(0.01)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $precioCompra = $get('precio_compra');
                    if ($state && $precioCompra) {
                        $margen = (($state - $precioCompra) / $precioCompra) * 100;
                        $set('margen_utilidad', "📈 Margen: " . number_format($margen, 2) . "%");
                    }
                })
                ->rules([
                    fn ($get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                        $precioCompra = $get('precio_compra');
                        if ($precioCompra && $value < $precioCompra) {
                            $fail('El precio de venta debe ser mayor o igual al precio de compra.');
                        }
                    },
                ])
                ->helperText('Precio unitario de venta')
                ->columnSpan(1),
            
            Placeholder::make('total_compra')
                ->label('')
                ->content('Ingresa cantidad y precio para calcular')
                ->columnSpan(1),
            
            Placeholder::make('margen_utilidad')
                ->label('')
                ->content('Se calculará al ingresar los precios')
                ->columnSpan(1),
            
            // Sección: Información Adicional
            Placeholder::make('info_adicional')
                ->label('📋 Información Adicional (Opcional)')
                ->content('Datos complementarios de la entrada')
                ->columnSpanFull(),
            
            TextInput::make('numero_factura')
                ->label('Número de Factura')
                ->maxLength(50)
                ->placeholder('Ej: FAC-2025-001')
                ->helperText('Número de factura del proveedor')
                ->columnSpan(1),
            
            TextInput::make('numero_remision')
                ->label('Número de Remisión')
                ->maxLength(50)
                ->placeholder('Ej: REM-2025-001')
                ->helperText('Número de remisión o guía')
                ->columnSpan(1),
            
            Textarea::make('observacion')
                ->label('Observaciones')
                ->rows(3)
                ->columnSpanFull()
                ->placeholder('Detalles adicionales sobre esta entrada de inventario')
                ->helperText('Información adicional relevante'),
        ]);
    }
}
