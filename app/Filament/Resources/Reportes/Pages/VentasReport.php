<?php

namespace App\Filament\Resources\Reportes\Pages;

use App\Filament\Resources\Reportes\ReportesResource;
use App\Models\Factura;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class VentasReport extends ListRecords
{
    protected static string $resource = ReportesResource::class;
    
    protected static ?string $title = 'Reportes de Ventas';
    
    protected static ?string $navigationLabel = 'Reportes de Ventas';

    public function table(Table $table): Table
    {
        return $table
            ->query(Factura::with(['cliente', 'estado', 'detalles']))
            ->columns([
                TextColumn::make('prefijo')
                    ->label('Prefijo')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('numero_factura')
                    ->label('N° Factura')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('fecha_factura')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('cliente.numero_documento')
                    ->label('Doc. Cliente')
                    ->searchable(),
                
                TextColumn::make('cliente.nombre_1')
                    ->label('Cliente')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if (!$record->cliente) return 'N/A';
                        return trim($record->cliente->nombre_1 . ' ' . 
                               ($record->cliente->apellido_1 ?? ''));
                    }),
                
                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Pagada' => 'success',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->alignEnd(),
                
                TextColumn::make('total_impuesto')
                    ->label('Impuestos')
                    ->money('COP')
                    ->alignEnd(),
                
                TextColumn::make('total_factura')
                    ->label('Total')
                    ->money('COP')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->filters([
                Filter::make('fecha_factura')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_factura', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_factura', '<=', $date),
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
                    
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('fecha_factura', 'desc')
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
        // Obtener las facturas filtradas de la tabla actual
        $facturas = $this->getFilteredTableQuery()->get();
        
        if ($facturas->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin datos para generar')
                ->body('No hay facturas que coincidan con los filtros aplicados.')
                ->warning()
                ->send();
            return;
        }
        
        $totales = [
            'subtotal' => $facturas->sum('subtotal'),
            'impuestos' => $facturas->sum('total_impuesto'),
            'total' => $facturas->sum('total_factura'),
            'cantidad' => $facturas->count(),
        ];
        
        $html = $this->generarHTML($facturas, $totales);
        
        // Generar PDF con DomPDF
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);
        
        $nombreArchivo = 'reporte_ventas_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $nombreArchivo);
    }
    
    private function generarHTML($facturas, $totales): string
    {
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; }
        .header h2 { color: #666; margin: 5px 0; }
        .info { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-right { text-align: right; }
        .totales { background: #f0f9ff; padding: 15px; border-radius: 8px; }
        .total-final { font-size: 18px; font-weight: bold; color: #2563eb; }
        .badge-pendiente { color: #d97706; }
        .badge-pagada { color: #059669; }
        .badge-cancelada { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNICIENCIA BUCARAMANGA</h1>
        <h2>Reporte de Ventas</h2>
        <p>Generado el: ' . $fechaGeneracion . '</p>
    </div>
    
    <div class="info">
        <strong>Resumen:</strong> ' . $totales['cantidad'] . ' facturas encontradas
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Prefijo</th>
                <th>N° Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Impuestos</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($facturas as $factura) {
            $cliente = $factura->cliente ? 
                trim($factura->cliente->nombre_1 . ' ' . ($factura->cliente->apellido_1 ?? '')) : 
                'N/A';
            
            $estadoClass = match($factura->estado->nombre ?? '') {
                'Pendiente' => 'badge-pendiente',
                'Pagada' => 'badge-pagada',
                'Cancelada' => 'badge-cancelada',
                default => ''
            };
            
            $html .= '<tr>
                <td>' . htmlspecialchars($factura->prefijo) . '</td>
                <td>' . htmlspecialchars($factura->numero_factura) . '</td>
                <td>' . Carbon::parse($factura->fecha_factura)->format('d/m/Y') . '</td>
                <td>' . htmlspecialchars($cliente) . '</td>
                <td class="' . $estadoClass . '">' . htmlspecialchars($factura->estado->nombre ?? 'N/A') . '</td>
                <td class="text-right">$' . number_format($factura->subtotal, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($factura->total_impuesto, 0, ',', '.') . '</td>
                <td class="text-right">$' . number_format($factura->total_factura, 0, ',', '.') . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="totales">
        <table style="margin: 0;">
            <tr>
                <td><strong>Total Facturas:</strong></td>
                <td class="text-right">' . $totales['cantidad'] . '</td>
            </tr>
            <tr>
                <td><strong>Subtotal General:</strong></td>
                <td class="text-right">$' . number_format($totales['subtotal'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td><strong>Impuestos Totales:</strong></td>
                <td class="text-right">$' . number_format($totales['impuestos'], 0, ',', '.') . '</td>
            </tr>
            <tr class="total-final">
                <td><strong>TOTAL GENERAL:</strong></td>
                <td class="text-right">$' . number_format($totales['total'], 0, ',', '.') . '</td>
            </tr>
        </table>
    </div>
</body>
</html>';
        
        return $html;
    }
}