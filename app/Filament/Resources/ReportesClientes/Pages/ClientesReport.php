<?php

namespace App\Filament\Resources\ReportesClientes\Pages;

use App\Filament\Resources\ReportesClientes\ReportesClientesResource;
use App\Models\Cliente;
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

class ClientesReport extends ListRecords
{
    protected static string $resource = ReportesClientesResource::class;
    
    protected static ?string $title = 'Reportes de Clientes';
    
    protected static ?string $navigationLabel = 'Reportes de Clientes';

    public function table(Table $table): Table
    {
        return $table
            ->query(Cliente::with(['tipoDocumento', 'tipoPersona', 'ciudad', 'departamento', 'facturas']))
            ->columns([
                TextColumn::make('numero_documento')
                    ->label('N° Documento')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tipoDocumento.documento')
                    ->label('Tipo Doc.')
                    ->sortable(),
                
                TextColumn::make('tipoPersona.tipo_persona')
                    ->label('Tipo Persona')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Natural' => 'info',
                        'Jurídica' => 'success',
                        default => 'gray',
                    }),
                
                TextColumn::make('nombre_completo')
                    ->label('Nombre / Razón Social')
                    ->searchable(['nombre_1', 'nombre_2', 'apellido_1', 'apellido_2', 'razon_social'])
                    ->formatStateUsing(function ($record) {
                        if ($record->razon_social) {
                            return $record->razon_social;
                        }
                        return trim(
                            ($record->nombre_1 ?? '') . ' ' . 
                            ($record->nombre_2 ?? '') . ' ' . 
                            ($record->apellido_1 ?? '') . ' ' . 
                            ($record->apellido_2 ?? '')
                        );
                    }),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Copiar'),
                
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                
                TextColumn::make('ciudad.nombre')
                    ->label('Ciudad')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('facturas_count')
                    ->label('N° Facturas')
                    ->counts('facturas')
                    ->alignCenter()
                    ->sortable(),
                
                TextColumn::make('total_compras')
                    ->label('Total Compras')
                    ->money('COP')
                    ->alignEnd()
                    ->getStateUsing(function ($record) {
                        return $record->facturas()->sum('total_factura');
                    }),
                
                TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipoDocumento')
                    ->label('Tipo Documento')
                    ->relationship('tipoDocumento', 'documento')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('tipoPersona')
                    ->label('Tipo Persona')
                    ->relationship('tipoPersona', 'tipo_persona')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('departamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombre')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('ciudad')
                    ->label('Ciudad')
                    ->relationship('ciudad', 'nombre')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->where('activo', $data['value'] === '1');
                        }
                        return $query;
                    }),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Registrado Desde')
                            ->native(false),
                        DatePicker::make('fecha_hasta')
                            ->label('Registrado Hasta')
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
        // Obtener los clientes filtrados de la tabla actual
        $clientes = $this->getFilteredTableQuery()->get();
        
        if ($clientes->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin datos para generar')
                ->body('No hay clientes que coincidan con los filtros aplicados.')
                ->warning()
                ->send();
            return;
        }
        
        $totales = [
            'cantidad' => $clientes->count(),
            'activos' => $clientes->where('activo', true)->count(),
            'inactivos' => $clientes->where('activo', false)->count(),
            'total_compras' => $clientes->sum(function ($cliente) {
                return $cliente->facturas()->sum('total_factura');
            }),
        ];
        
        $html = $this->generarHTML($clientes, $totales);
        
        // Generar PDF con DomPDF
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);
        
        $nombreArchivo = 'reporte_clientes_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $nombreArchivo);
    }
    
    private function generarHTML($clientes, $totales): string
    {
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; }
        .header h2 { color: #666; margin: 5px 0; }
        .info { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totales { background: #f0f9ff; padding: 15px; border-radius: 8px; }
        .total-final { font-size: 18px; font-weight: bold; color: #2563eb; }
        .badge-activo { color: #059669; font-weight: bold; }
        .badge-inactivo { color: #dc2626; font-weight: bold; }
        .badge-natural { color: #3b82f6; }
        .badge-juridica { color: #059669; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNICIENCIA BUCARAMANGA</h1>
        <h2>Reporte de Clientes</h2>
        <p>Generado el: ' . $fechaGeneracion . '</p>
    </div>
    
    <div class="info">
        <strong>Resumen:</strong> ' . $totales['cantidad'] . ' clientes encontrados | 
        <span style="color: #059669;">Activos: ' . $totales['activos'] . '</span> | 
        <span style="color: #dc2626;">Inactivos: ' . $totales['inactivos'] . '</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>N° Documento</th>
                <th>Tipo Doc.</th>
                <th>Tipo Persona</th>
                <th>Nombre / Razón Social</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Ciudad</th>
                <th class="text-center">N° Facturas</th>
                <th class="text-right">Total Compras</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($clientes as $cliente) {
            $nombreCompleto = $cliente->razon_social ? 
                $cliente->razon_social : 
                trim(
                    ($cliente->nombre_1 ?? '') . ' ' . 
                    ($cliente->nombre_2 ?? '') . ' ' . 
                    ($cliente->apellido_1 ?? '') . ' ' . 
                    ($cliente->apellido_2 ?? '')
                );
            
            $estadoClass = $cliente->activo ? 'badge-activo' : 'badge-inactivo';
            $estadoText = $cliente->activo ? 'Activo' : 'Inactivo';
            
            $tipoPersonaClass = $cliente->tipoPersona && $cliente->tipoPersona->tipo_persona === 'Natural' ? 
                'badge-natural' : 'badge-juridica';
            
            $totalCompras = $cliente->facturas()->sum('total_factura');
            $numeroFacturas = $cliente->facturas()->count();
            
            $html .= '<tr>
                <td>' . htmlspecialchars($cliente->numero_documento ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($cliente->tipoDocumento->documento ?? 'N/A') . '</td>
                <td class="' . $tipoPersonaClass . '">' . htmlspecialchars($cliente->tipoPersona->tipo_persona ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($nombreCompleto) . '</td>
                <td>' . htmlspecialchars($cliente->email ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($cliente->telefono ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($cliente->ciudad->nombre ?? 'N/A') . '</td>
                <td class="text-center">' . $numeroFacturas . '</td>
                <td class="text-right">$' . number_format($totalCompras, 0, ',', '.') . '</td>
                <td class="text-center ' . $estadoClass . '">' . $estadoText . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="totales">
        <table style="margin: 0;">
            <tr>
                <td><strong>Total Clientes:</strong></td>
                <td class="text-right">' . $totales['cantidad'] . '</td>
            </tr>
            <tr>
                <td><strong>Clientes Activos:</strong></td>
                <td class="text-right" style="color: #059669;">' . $totales['activos'] . '</td>
            </tr>
            <tr>
                <td><strong>Clientes Inactivos:</strong></td>
                <td class="text-right" style="color: #dc2626;">' . $totales['inactivos'] . '</td>
            </tr>
            <tr class="total-final">
                <td><strong>TOTAL COMPRAS GENERAL:</strong></td>
                <td class="text-right">$' . number_format($totales['total_compras'], 0, ',', '.') . '</td>
            </tr>
        </table>
    </div>
</body>
</html>';
        
        return $html;
    }
}
