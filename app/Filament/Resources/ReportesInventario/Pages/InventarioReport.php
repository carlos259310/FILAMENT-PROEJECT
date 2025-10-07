<?php

namespace App\Filament\Resources\ReportesInventario\Pages;

use App\Filament\Resources\ReportesInventario\ReportesInventarioResource;
use App\Models\Inventario;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class InventarioReport extends ListRecords
{
    protected static string $resource = ReportesInventarioResource::class;
    
    protected static ?string $title = 'Reportes de Inventario';
    
    protected static ?string $navigationLabel = 'Reportes de Inventario';

    public function table(Table $table): Table
    {
        return $table
            ->query(Inventario::with(['producto.categoria', 'producto.marca', 'bodega']))
            ->columns([
                TextColumn::make('producto.codigo')
                    ->label('Código Producto')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('producto.codigo_barras')
                    ->label('Código Barras')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                TextColumn::make('producto.categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('producto.marca.nombre')
                    ->label('Marca')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('bodega.nombre')
                    ->label('Bodega')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn ($record) => $record->bodega->principal ? 'warning' : 'gray'),
                
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                
                TextColumn::make('precio_compra')
                    ->label('Precio Compra')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('precio_compra_promedio')
                    ->label('P. Compra Prom.')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('precio_venta_promedio')
                    ->label('P. Venta Prom.')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('valor_total_compra')
                    ->label('Valor Total Compra')
                    ->money('COP')
                    ->alignEnd()
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => $record->cantidad * $record->precio_compra)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("cantidad * precio_compra {$direction}");
                    }),
                
                TextColumn::make('valor_total_venta')
                    ->label('Valor Total Venta')
                    ->money('COP')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->cantidad * $record->precio_venta)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("cantidad * precio_venta {$direction}");
                    }),
                
                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('bodega')
                    ->label('Bodega')
                    ->relationship('bodega', 'nombre')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->relationship('producto.categoria', 'nombre')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('marca')
                    ->label('Marca')
                    ->relationship('producto.marca', 'nombre')
                    ->searchable()
                    ->preload(),
                
                Filter::make('stock')
                    ->label('Estado de Stock')
                    ->form([
                        \Filament\Forms\Components\Select::make('estado_stock')
                            ->label('Estado')
                            ->options([
                                'sin_stock' => 'Sin Stock (0)',
                                'stock_bajo' => 'Stock Bajo (1-10)',
                                'stock_normal' => 'Stock Normal (>10)',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['estado_stock'])) {
                            return $query;
                        }
                        
                        return match ($data['estado_stock']) {
                            'sin_stock' => $query->where('cantidad', '=', 0),
                            'stock_bajo' => $query->whereBetween('cantidad', [1, 10]),
                            'stock_normal' => $query->where('cantidad', '>', 10),
                            default => $query,
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['estado_stock'])) {
                            return null;
                        }
                        
                        return match ($data['estado_stock']) {
                            'sin_stock' => 'Stock: Sin Stock',
                            'stock_bajo' => 'Stock: Bajo',
                            'stock_normal' => 'Stock: Normal',
                            default => null,
                        };
                    }),
                
                Filter::make('updated_at')
                    ->form([
                        DatePicker::make('actualizado_desde')
                            ->label('Actualizado Desde')
                            ->native(false),
                        DatePicker::make('actualizado_hasta')
                            ->label('Actualizado Hasta')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['actualizado_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['actualizado_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['actualizado_desde'] ?? null) {
                            $indicators[] = 'Desde: ' . Carbon::parse($data['actualizado_desde'])->format('d/m/Y');
                        }

                        if ($data['actualizado_hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . Carbon::parse($data['actualizado_hasta'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generar_pdf')
                ->label('📄 Generar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action('generarPDF')
                ->requiresConfirmation()
                ->modalHeading('Generar Reporte PDF')
                ->modalDescription('¿Está seguro que desea generar el reporte PDF con los filtros aplicados?')
                ->modalSubmitActionLabel('Generar PDF'),
        ];
    }
    
    public function generarPDF()
    {
        // Obtener el inventario filtrado de la tabla actual
        $inventarios = $this->getFilteredTableQuery()->get();
        
        if ($inventarios->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin datos para generar')
                ->body('No hay registros de inventario que coincidan con los filtros aplicados.')
                ->warning()
                ->send();
            return;
        }
        
        $totales = [
            'cantidad_items' => $inventarios->count(),
            'cantidad_total' => $inventarios->sum('cantidad'),
            'valor_compra_total' => $inventarios->sum(fn ($item) => $item->cantidad * $item->precio_compra),
            'valor_venta_total' => $inventarios->sum(fn ($item) => $item->cantidad * $item->precio_venta),
            'sin_stock' => $inventarios->where('cantidad', '=', 0)->count(),
            'stock_bajo' => $inventarios->whereBetween('cantidad', [1, 10])->count(),
            'stock_normal' => $inventarios->where('cantidad', '>', 10)->count(),
        ];
        
        $html = $this->generarHTML($inventarios, $totales);
        
        // Crear el PDF usando HTML simple
        $nombreArchivo = 'reporte_inventario_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        // Usar una respuesta de descarga directa con HTML
        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $nombreArchivo, [
            'Content-Type' => 'text/html',
        ]);
    }
    
    private function generarHTML($inventarios, $totales): string
    {
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; }
        .header h2 { color: #666; margin: 5px 0; }
        .info { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        th, td { padding: 6px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totales { background: #f0f9ff; padding: 15px; border-radius: 8px; }
        .total-final { font-size: 18px; font-weight: bold; color: #2563eb; }
        .badge-danger { color: #dc2626; font-weight: bold; }
        .badge-warning { color: #d97706; font-weight: bold; }
        .badge-success { color: #059669; font-weight: bold; }
        .resumen-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .resumen-card { background: #f8fafc; padding: 10px; border-radius: 5px; text-align: center; }
        .resumen-card h3 { margin: 0; font-size: 14px; color: #666; }
        .resumen-card p { margin: 5px 0 0 0; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNICIENCIA BUCARAMANGA</h1>
        <h2>Reporte de Inventario</h2>
        <p>Generado el: ' . $fechaGeneracion . '</p>
    </div>
    
    <div class="resumen-grid">
        <div class="resumen-card">
            <h3>Total Items</h3>
            <p>' . $totales['cantidad_items'] . '</p>
        </div>
        <div class="resumen-card">
            <h3>Unidades Totales</h3>
            <p>' . number_format($totales['cantidad_total'], 0, ',', '.') . '</p>
        </div>
        <div class="resumen-card">
            <h3>Valor Inventario (Venta)</h3>
            <p>$' . number_format($totales['valor_venta_total'], 0, ',', '.') . '</p>
        </div>
    </div>
    
    <div class="info">
        <strong>Estado del Stock:</strong> 
        <span style="color: #dc2626;">Sin Stock: ' . $totales['sin_stock'] . '</span> | 
        <span style="color: #d97706;">Stock Bajo: ' . $totales['stock_bajo'] . '</span> | 
        <span style="color: #059669;">Stock Normal: ' . $totales['stock_normal'] . '</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Bodega</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">P. Compra</th>
                <th class="text-right">P. Venta</th>
                <th class="text-right">V. Total Compra</th>
                <th class="text-right">V. Total Venta</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($inventarios as $item) {
            $cantidadClass = match (true) {
                $item->cantidad <= 0 => 'badge-danger',
                $item->cantidad <= 10 => 'badge-warning',
                default => 'badge-success',
            };
            
            $valorTotalCompra = $item->cantidad * $item->precio_compra;
            $valorTotalVenta = $item->cantidad * $item->precio_venta;
            
            $html .= '<tr>
                <td>' . htmlspecialchars($item->producto->codigo ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($item->producto->nombre ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($item->producto->categoria->nombre ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($item->producto->marca->nombre ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($item->bodega->nombre ?? 'N/A') . '</td>
                <td class="text-center ' . $cantidadClass . '">' . $item->cantidad . '</td>
                <td class="text-right">$' . number_format($item->precio_compra, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($item->precio_venta, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($valorTotalCompra, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($valorTotalVenta, 0, ',', '.') . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="totales">
        <table style="margin: 0;">
            <tr>
                <td><strong>Total Items en Inventario:</strong></td>
                <td class="text-right">' . $totales['cantidad_items'] . '</td>
            </tr>
            <tr>
                <td><strong>Total Unidades:</strong></td>
                <td class="text-right">' . number_format($totales['cantidad_total'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td><strong>Valor Total Inventario (Compra):</strong></td>
                <td class="text-right">$' . number_format($totales['valor_compra_total'], 0, ',', '.') . '</td>
            </tr>
            <tr class="total-final">
                <td><strong>VALOR TOTAL INVENTARIO (VENTA):</strong></td>
                <td class="text-right">$' . number_format($totales['valor_venta_total'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 15px;">
                    <strong>Margen de Utilidad Potencial:</strong> 
                    <span style="color: #059669; font-size: 16px;">
                        $' . number_format($totales['valor_venta_total'] - $totales['valor_compra_total'], 0, ',', '.') . '
                    </span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>';
        
        return $html;
    }
}
