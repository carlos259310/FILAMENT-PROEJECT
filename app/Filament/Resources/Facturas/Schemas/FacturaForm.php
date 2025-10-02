<?php

namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use App\Models\Inventario;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('id_cliente')
                ->label('Cliente')
                ->relationship(
                    name: 'cliente',
                    titleAttribute: 'nombre_1',
                    modifyQueryUsing: fn ($query) => $query
                        ->where('activo', true)
                        ->orderBy('nombre_1')
                )
                ->getOptionLabelFromRecordUsing(function ($record) {
                    // Si es persona natural
                    if ($record->id_tipo_persona == 1 || !$record->razon_social) {
                        $nombreCompleto = trim(
                            ($record->nombre_1 ?? '') . ' ' . 
                            ($record->nombre_2 ?? '') . ' ' . 
                            ($record->apellido_1 ?? '') . ' ' . 
                            ($record->apellido_2 ?? '')
                        );
                        return sprintf(
                            '%s - %s',
                            $record->numero_documento ?? 'N/A',
                            $nombreCompleto
                        );
                    }
                    // Si es persona jurídica (empresa)
                    return sprintf(
                        '%s - %s',
                        $record->numero_documento ?? 'N/A',
                        $record->razon_social ?? $record->nombre_1
                    );
                })
                ->searchable(['nombre_1', 'apellido_1', 'numero_documento', 'razon_social'])
                ->preload()
                ->required()
                ->placeholder('🔍 Buscar cliente por documento o nombre')
                ->helperText('Solo se muestran clientes activos'),

            TextInput::make('prefijo')
                ->label('Prefijo')
                ->required()
                ->default('INV')
                ->placeholder('Ej: INV'),

            TextInput::make('numero_factura')
                ->label('Número de Factura')
                ->required()
                ->placeholder('Número único de factura'),

            DatePicker::make('fecha_factura')
                ->label('Fecha de Factura')
                ->required()
                ->default(now()),

            Repeater::make('detalles')
                ->label('Productos de la Factura')
                ->relationship('detalles')
                ->schema([
                    Select::make('id_bodega')
                        ->label('Bodega')
                        ->relationship(
                            name: 'bodega',
                            titleAttribute: 'nombre',
                            modifyQueryUsing: fn($query) => $query
                                ->whereHas('inventario', function ($q) {
                                    $q->where('cantidad', '>', 0);
                                })
                                ->withCount(['inventario as productos_disponibles' => function ($q) {
                                    $q->where('cantidad', '>', 0);
                                }])
                                ->orderBy('nombre')
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => sprintf(
                            '%s (%d productos)',
                            $record->nombre,
                            $record->productos_disponibles ?? 0
                        ))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->placeholder('Seleccione bodega con stock')
                        ->helperText('Solo se muestran bodegas con productos disponibles')
                        ->afterStateUpdated(function ($set) {
                            $set('id_producto', null);
                            $set('cantidad_disponible', 0);
                            $set('precio_venta', 0);
                            $set('cantidad', 1);
                        }),

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
                                ->mapWithKeys(function ($inv) {
                                    $producto = $inv->producto;
                                    $label = sprintf(
                                        '%s - %s (Stock: %d) - $%s',
                                        $producto->codigo ?? 'N/A',
                                        $producto->nombre,
                                        $inv->cantidad,
                                        number_format($inv->precio_venta ?? 0, 0, ',', '.')
                                    );
                                    return [$producto->id => $label];
                                })
                                ->toArray();
                        })
                        ->required()
                        ->reactive()
                        ->searchable()
                        ->preload()
                        ->live(onBlur: true)
                        ->placeholder('🔍 Buscar por código o nombre...')
                        ->helperText('Escriba para filtrar productos disponibles')
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $bodegaId = $get('id_bodega');
                            if ($bodegaId && $state) {
                                $inventario = Inventario::where('id_bodega', $bodegaId)
                                    ->where('id_producto', $state)
                                    ->with('producto')
                                    ->first();

                                if ($inventario) {
                                    $set('cantidad_disponible', $inventario->cantidad);
                                    $set('precio_venta', $inventario->precio_venta ?? 0);
                                    $set('cantidad', 1); // Cantidad por defecto

                                    // Trigger cálculo inmediato
                                    self::calcularTotales($set, function ($key) use ($get, $inventario) {
                                        if ($key === 'cantidad') return 1;
                                        if ($key === 'precio_venta') return $inventario->precio_venta ?? 0;
                                        return $get($key);
                                    });
                                } else {
                                    $set('cantidad_disponible', 0);
                                    $set('precio_venta', 0);
                                }
                            }
                        }),

                    TextInput::make('cantidad_disponible')
                        ->label('Cantidad Disponible')
                        ->disabled()
                        ->reactive()
                        ->dehydrated(false)
                        ->default(function ($get) {
                            $bodegaId = $get('id_bodega');
                            $productoId = $get('id_producto');
                            if (!$bodegaId || !$productoId) {
                                return 0;
                            }
                            $inventario = Inventario::where('id_bodega', $bodegaId)
                                ->where('id_producto', $productoId)
                                ->select('cantidad')
                                ->first();
                            return $inventario ? $inventario->cantidad : 0;
                        }),

                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->minValue(1)
                        ->step(1)
                        ->inputMode('numeric')
                        ->rules([
                            function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    $bodegaId = $get('id_bodega');
                                    $productoId = $get('id_producto');
                                    if ($bodegaId && $productoId) {
                                        $inventario = Inventario::where('id_bodega', $bodegaId)
                                            ->where('id_producto', $productoId)
                                            ->first();
                                        $stock = $inventario ? $inventario->cantidad : 0;
                                        if ($value > $stock) {
                                            $fail("⚠️ Stock insuficiente. Disponible: {$stock} unidades.");
                                        }
                                    }
                                };
                            }
                        ])
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($set, $get) {
                            self::calcularTotales($set, $get);
                        }),

                    TextInput::make('precio_venta')
                        ->label('Precio Unitario')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->minValue(0)
                        ->step(0.01)
                        ->inputMode('decimal')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($set, $get) {
                            self::calcularTotales($set, $get);
                        }),

                    TextInput::make('porcentaje_impuesto')
                        ->label('% IVA')
                        ->numeric()
                        ->default(0)
                        ->suffix('%')
                        ->step(0.01)
                        ->inputMode('decimal')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($set, $get) {
                            self::calcularTotales($set, $get);
                        }),

                    TextInput::make('impuesto')
                        ->label('Valor Impuesto')
                        ->numeric()
                        ->prefix('$')
                        ->readOnly()
                        ->dehydrated()
                        ->default(0),

                    TextInput::make('subtotal_linea')
                        ->label('Subtotal')
                        ->numeric()
                        ->prefix('$')
                        ->readOnly()
                        ->dehydrated()
                        ->default(0),

                    TextInput::make('total_linea')
                        ->label('Total Línea')
                        ->numeric()
                        ->prefix('$')
                        ->readOnly()
                        ->dehydrated()
                        ->default(0),

                    Textarea::make('observacion')
                        ->label('Observación')
                        ->rows(2)
                        ->placeholder('Observaciones opcionales')
                        ->columnSpanFull(),
                ])
                ->columns(3)
                ->createItemButtonLabel('Agregar Producto')
                ->deleteAction(
                    fn($action) => $action->requiresConfirmation()->label('🗑️')
                )
                ->reorderable()
                ->collapsible()
                ->cloneable()
                ->columnSpanFull()
                ->live()
                ->afterStateUpdated(function ($set, $state) {
                    self::calcularTotalesFactura($set, $state);
                })
                ->defaultItems(1)
                ->addActionLabel('Agregar otro producto'),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->numeric()
                ->prefix('$')
                ->readOnly()
                ->extraAttributes(['class' => 'font-bold text-lg'])
                ->default(0),

            TextInput::make('total_impuesto')
                ->label('Total Impuestos (IVA)')
                ->numeric()
                ->prefix('$')
                ->readOnly()
                ->extraAttributes(['class' => 'font-bold text-lg'])
                ->default(0),

            TextInput::make('total_factura')
                ->label('💰 TOTAL FACTURA')
                ->numeric()
                ->prefix('$')
                ->required()
                ->readOnly()
                ->extraAttributes(['class' => 'font-bold text-2xl text-green-600'])
                ->default(0),
        ]);
    }

    /**
     * Calcular totales de una línea de detalle
     */
    private static function calcularTotales($set, $get): void
    {
        $cantidad = (float) ($get('cantidad') ?? 0);
        $precioVenta = (float) ($get('precio_venta') ?? 0);
        $porcentajeImpuesto = (float) ($get('porcentaje_impuesto') ?? 0);

        // Subtotal = cantidad * precio
        $subtotal = $cantidad * $precioVenta;

        // Impuesto = subtotal * (porcentaje / 100)
        $impuesto = $subtotal * ($porcentajeImpuesto / 100);

        // Total línea = subtotal + impuesto
        $totalLinea = $subtotal + $impuesto;

        $set('subtotal_linea', round($subtotal, 2));
        $set('impuesto', round($impuesto, 2));
        $set('total_linea', round($totalLinea, 2));
    }

    /**
     * Calcular totales generales de la factura
     */
    private static function calcularTotalesFactura($set, $state): void
    {
        $subtotal = 0;
        $totalImpuesto = 0;

        foreach ($state as $detalle) {
            $subtotal += (float) ($detalle['subtotal_linea'] ?? 0);
            $totalImpuesto += (float) ($detalle['impuesto'] ?? 0);
        }

        $totalFactura = $subtotal + $totalImpuesto;

        $set('subtotal', round($subtotal, 2));
        $set('total_impuesto', round($totalImpuesto, 2));
        $set('total_factura', round($totalFactura, 2));
    }
}
