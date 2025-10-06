<?php

namespace App\Filament\Resources\Facturas\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Models\EstadoFactura;

use App\Filament\Resources\Facturas\FacturaResource;

class ListFacturas extends ListRecords
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    
    protected function getTableRecordActions(): array
    {
        return [
            Action::make('cambiar_estado')
                ->label('Cambiar Estado')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->button()
                ->modalHeading('Cambiar estado de la factura')
                ->modalSubmitActionLabel('Actualizar Estado')
                ->form([
                    \Filament\Forms\Components\Select::make('id_estado')
                        ->label('Nuevo Estado')
                        ->options(EstadoFactura::all()->pluck('nombre', 'id'))
                        ->required(),
                ])
                ->visible(fn ($record) => $record->estado->nombre === 'Pendiente')
                ->action(function ($record, array $data) {
                    $record->id_estado = $data['id_estado'];
                    $record->save();
                    \Filament\Notifications\Notification::make()
                        ->title('Estado actualizado')
                        ->success()
                        ->send();
                }),
            Action::make('anular_factura')
                ->label('Anular Factura')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('¿Anular esta factura?')
                ->modalDescription('Esta acción generará una devolución de inventario automáticamente.')
                ->modalSubmitActionLabel('Sí, anular factura')
                ->visible(fn ($record) => $record->estado->nombre === 'Pagada')
                ->action(function ($record) {
                    $estadoCancelada = EstadoFactura::where('nombre', 'Cancelada')->first();
                    if (!$estadoCancelada) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error: Estado "Cancelada" no encontrado')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Buscar o crear el motivo "Devolución"
                    $motivoDevolucion = \App\Models\MotivoEntrada::firstOrCreate(
                        ['nombre' => 'Devolución'],
                        ['nombre' => 'Devolución']
                    );

                    // Obtener las salidas de inventario de esta factura
                    $salidas = \App\Models\SalidaInventario::where(
                        'numero_factura',
                        $record->prefijo . $record->numero_factura
                    )->get();

                    // Crear entradas de devolución y restaurar inventario
                    foreach ($salidas as $salida) {
                        // Crear entrada de devolución
                        \App\Models\EntradaInventario::create([
                            'id_producto' => $salida->id_producto,
                            'id_bodega' => $salida->id_bodega,
                            'id_motivo' => $motivoDevolucion->id,
                            'cantidad' => $salida->cantidad,
                            'precio_compra' => $salida->precio_costo,
                            'precio_venta' => $salida->precio_venta,
                            'numero_factura' => $record->prefijo . $record->numero_factura,
                            'observacion' => "Devolución por anulación de factura #{$record->prefijo}{$record->numero_factura}",
                        ]);

                        // Restaurar inventario
                        $inventario = \App\Models\Inventario::where('id_bodega', $salida->id_bodega)
                            ->where('id_producto', $salida->id_producto)
                            ->first();

                        if ($inventario) {
                            $inventario->increment('cantidad', $salida->cantidad);
                        }
                    }

                    // Cambiar estado a Cancelada
                    $record->id_estado = $estadoCancelada->id;
                    $record->save();

                    \Filament\Notifications\Notification::make()
                        ->title('Factura anulada correctamente')
                        ->body('Se ha generado la devolución de inventario automáticamente.')
                        ->success()
                        ->send();
                }),
        ];
    }
}