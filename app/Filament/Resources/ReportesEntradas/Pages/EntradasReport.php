<?php

namespace App\Filament\Resources\ReportesEntradas\Pages;

use App\Filament\Resources\ReportesEntradas\ReportesEntradasResource;
use App\Models\EntradaInventario;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class EntradasReport extends ListRecords
{
    protected static string $resource = ReportesEntradasResource::class;
    
    protected static ?string $title = 'Reportes de Entradas de Inventario';
    
    protected static ?string $navigationLabel = 'Reportes de Entradas';

    public function table(Table $table): Table
    {
        return $table
            ->query(EntradaInventario::with(['producto.categoria', 'producto.marca', 'bodega', 'motivo']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label('Fecha Entrada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('producto.codigo')
                    ->label('Cód. Producto')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40),
                
                TextColumn::make('producto.categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                
                TextColumn::make('producto.marca.nombre')
                    ->label('Marca')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(),
                
                TextColumn::make('bodega.nombre')
                    ->label('Bodega')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->bodega && $record->bodega->principal ? 'warning' : 'gray'),
                
                TextColumn::make('motivo.nombre')
                    ->label('Motivo')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold')
                    ->color('success'),
                
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
                
                TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->money('COP')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->cantidad * $record->precio_compra)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("cantidad * precio_compra {$direction}");
                    }),
                
                TextColumn::make('numero_factura')
                    ->label('N° Factura')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('numero_remision')
                    ->label('N° Remisión')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('observacion')
                    ->label('Observación')
                    ->limit(30)
                    ->tooltip(function ($record): ?string {
                        return $record->observacion;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Fecha Desde')
                            ->native(false),
                        DatePicker::make('fecha_hasta')
                            ->label('Fecha Hasta')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['fecha_desde'] ?? null) {
                            $indicators[] = 'Desde: ' . Carbon::parse($data['fecha_desde'])->format('d/m/Y');
                        }

                        if ($data['fecha_hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . Carbon::parse($data['fecha_hasta'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
                
                SelectFilter::make('bodega')
                    ->label('Bodega')
                    ->relationship('bodega', 'nombre')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('motivo')
                    ->label('Motivo')
                    ->relationship('motivo', 'nombre')
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
            ])
            ->defaultSort('created_at', 'desc')
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
        // Obtener las entradas filtradas de la tabla actual
        $entradas = $this->getFilteredTableQuery()->get();
        
        if ($entradas->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin datos para generar')
                ->body('No hay entradas de inventario que coincidan con los filtros aplicados.')
                ->warning()
                ->send();
            return;
        }
        
        $totales = [
            'cantidad_registros' => $entradas->count(),
            'cantidad_total' => $entradas->sum('cantidad'),
            'valor_compra_total' => $entradas->sum(fn ($item) => $item->cantidad * $item->precio_compra),
            'valor_venta_estimado' => $entradas->sum(fn ($item) => $item->cantidad * $item->precio_venta),
            'margen_estimado' => $entradas->sum(fn ($item) => ($item->cantidad * $item->precio_venta) - ($item->cantidad * $item->precio_compra)),
        ];
        
        $html = $this->generarHTML($entradas, $totales);
        
        // Crear el PDF usando HTML simple
        $nombreArchivo = 'reporte_entradas_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        // Usar una respuesta de descarga directa con HTML
        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $nombreArchivo, [
            'Content-Type' => 'text/html',
        ]);
    }
    
    private function generarHTML($entradas, $totales): string
    {
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Entradas de Inventario</title>
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
        .badge-success { color: #059669; font-weight: bold; }
        .resumen-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .resumen-card { background: #f8fafc; padding: 10px; border-radius: 5px; text-align: center; }
        .resumen-card h3 { margin: 0; font-size: 14px; color: #666; }
        .resumen-card p { margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #059669; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNICIENCIA BUCARAMANGA</h1>
        <h2>Reporte de Entradas de Inventario</h2>
        <p>Generado el: ' . $fechaGeneracion . '</p>
    </div>
    
    <div class="resumen-grid">
        <div class="resumen-card">
            <h3>Total Entradas</h3>
            <p>' . $totales['cantidad_registros'] . '</p>
        </div>
        <div class="resumen-card">
            <h3>Unidades Ingresadas</h3>
            <p>' . number_format($totales['cantidad_total'], 0, ',', '.') . '</p>
        </div>
        <div class="resumen-card">
            <h3>Inversión Total</h3>
            <p>$' . number_format($totales['valor_compra_total'], 0, ',', '.') . '</p>
        </div>
    </div>
    
    <div class="info">
        <strong>Resumen Financiero:</strong> 
        Inversión: $' . number_format($totales['valor_compra_total'], 0, ',', '.') . ' | 
        Valor Venta Estimado: $' . number_format($totales['valor_venta_estimado'], 0, ',', '.') . ' | 
        Margen Estimado: $' . number_format($totales['margen_estimado'], 0, ',', '.') . '
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cód. Prod.</th>
                <th>Producto</th>
                <th>Bodega</th>
                <th>Motivo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">P. Compra</th>
                <th class="text-right">P. Venta</th>
                <th class="text-right">Valor Total</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($entradas as $entrada) {
            $valorTotal = $entrada->cantidad * $entrada->precio_compra;
            
            $html .= '<tr>
                <td>' . Carbon::parse($entrada->created_at)->format('d/m/Y H:i') . '</td>
                <td>' . htmlspecialchars($entrada->producto->codigo ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($entrada->producto->nombre ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($entrada->bodega->nombre ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($entrada->motivo->nombre ?? 'N/A') . '</td>
                <td class="text-center badge-success">' . $entrada->cantidad . '</td>
                <td class="text-right">$' . number_format($entrada->precio_compra, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($entrada->precio_venta, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($valorTotal, 0, ',', '.') . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="totales">
        <table style="margin: 0;">
            <tr>
                <td><strong>Total Registros de Entradas:</strong></td>
                <td class="text-right">' . $totales['cantidad_registros'] . '</td>
            </tr>
            <tr>
                <td><strong>Total Unidades Ingresadas:</strong></td>
                <td class="text-right">' . number_format($totales['cantidad_total'], 0, ',', '.') . '</td>
            </tr>
            <tr class="total-final">
                <td><strong>INVERSIÓN TOTAL:</strong></td>
                <td class="text-right">$' . number_format($totales['valor_compra_total'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td><strong>Valor Venta Estimado:</strong></td>
                <td class="text-right" style="color: #059669;">$' . number_format($totales['valor_venta_estimado'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td><strong>Margen de Utilidad Estimado:</strong></td>
                <td class="text-right" style="color: #059669; font-weight: bold;">$' . number_format($totales['margen_estimado'], 0, ',', '.') . '</td>
            </tr>
        </table>
    </div>
</body>
</html>';
        
        return $html;
    }
}
