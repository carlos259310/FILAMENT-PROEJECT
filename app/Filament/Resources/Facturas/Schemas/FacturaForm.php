<?php


namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use App\Models\Inventario;
use App\Models\Cliente;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('id_cliente')
                ->label('Cliente')
                ->options(Cliente::all()->pluck('nombre_1', 'id_cliente'))
                ->searchable()
                ->required(),

            TextInput::make('prefijo')
                ->label('Prefijo')
                ->required(),

            TextInput::make('numero_factura')
                ->label('Número de Factura')
                ->required(),

            DatePicker::make('fecha_factura')
                ->label('Fecha de Factura')
                ->required(),

            Repeater::make('detalles')
                ->label('Detalle de Productos')
                ->relationship('detalles')
                ->columns(2)
                ->schema([
                    Select::make('id_bodega')
                        ->label('Bodega')
                        ->relationship('bodega', 'nombre')
                        ->required()
                        ->reactive(),

                    Select::make('id_producto')
                        ->label('Producto')
                        ->options(function ($get) {
                            $bodegaId = $get('id_bodega');
                            if (!$bodegaId) {
                                return [];
                            }
                            return Inventario::where('id_bodega', $bodegaId)
                                ->where('cantidad', '>', 0)
                                ->with('producto')
                                ->get()
                                ->pluck('producto.nombre', 'producto.id')
                                ->toArray();
                        })
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $bodegaId = $get('id_bodega');
                            if ($bodegaId && $state) {
                                $inventario = Inventario::where('id_bodega', $bodegaId)
                                    ->where('id_producto', $state)
                                    ->first();
                                $set('cantidad_disponible', $inventario ? $inventario->cantidad : 0);
                                // Solo sugerimos el precio, pero el usuario puede editarlo
                                $set('precio_venta', $inventario && $inventario->producto ? $inventario->producto->precio_venta : 0);
                            } else {
                                $set('cantidad_disponible', 0);
                                $set('precio_venta', 0);
                            }
                        }),

                    TextInput::make('cantidad_disponible')
                        ->label('Disponible')
                        ->disabled()
                        ->dehydrated(false)
                        ->default(function ($get) {
                            $bodegaId = $get('id_bodega');
                            $productoId = $get('id_producto');
                            if (!$bodegaId || !$productoId) {
                                return 0;
                            }
                            $inventario = Inventario::where('id_bodega', $bodegaId)
                                ->where('id_producto', $productoId)
                                ->first();
                            return $inventario ? $inventario->cantidad : 0;
                        }),

                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(fn($get) => $get('cantidad_disponible'))
                        ->rules(['numeric', 'min:1'])
                        ->reactive()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $cantidad = (float) $state;
                            $precio = (float) $get('precio_venta');
                            $impuesto = (float) $get('impuesto');
                            $subtotal = ($cantidad * $precio) + $impuesto;
                            $set('subtotal_linea', $subtotal);
                        }),

                    TextInput::make('precio_venta')
                        ->label('Precio')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        // El usuario puede editar el precio
                        ->reactive()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $cantidad = (float) $get('cantidad');
                            $precio = (float) $state;
                            $impuesto = (float) $get('impuesto');
                            $subtotal = ($cantidad * $precio) + $impuesto;
                            $set('subtotal_linea', $subtotal);
                        }),

                    TextInput::make('impuesto')
                        ->label('Imp.')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $cantidad = (float) $get('cantidad');
                            $precio = (float) $get('precio_venta');
                            $impuesto = (float) $state;
                            $subtotal = ($cantidad * $precio) + $impuesto;
                            $set('subtotal_linea', $subtotal);
                        }),

                    TextInput::make('porcentaje_impuesto')
                        ->label('% Imp.')
                        ->numeric()
                        ->default(0),

                    Textarea::make('observacion')
                        ->label('Obs.')
                        ->rows(1),

                    TextInput::make('subtotal_linea')
                        ->label('Subtotal')
                        ->numeric()
                        ->readOnly()
                        ->default(0),
                ])
                ->createItemButtonLabel('Agregar Producto')
                ->columnSpanFull()
                ->reactive()
                ->afterStateUpdated(function ($set, $state) {
                    // Calcula el total de la factura en tiempo real
                    $total = 0;
                    foreach ($state as $detalle) {
                        $subtotal = isset($detalle['subtotal_linea']) ? (float)$detalle['subtotal_linea'] : 0;
                        $total += $subtotal;
                    }
                    $set('total_factura', $total);
                }),

            TextInput::make('total_factura')
                ->label('Total Factura')
                ->numeric()
                ->required()
                ->readOnly()
                ->default(0),
        ]);
    }
}
