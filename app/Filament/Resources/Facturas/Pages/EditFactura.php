<?php

namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Models\Inventario;
use App\Models\MotivoSalida;
use App\Models\SalidaInventario;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFactura extends EditRecord
{
    protected static string $resource = FacturaResource::class;


    protected function afterSave(): void
    {
        $factura = $this->record;
        $motivoVenta = MotivoSalida::where('nombre', 'Venta')->first();

        // Restaurar inventario de las salidas anteriores
        $salidasAnteriores = SalidaInventario::where('numero_factura', $factura->prefijo . $factura->numero_factura)->get();
        
        foreach ($salidasAnteriores as $salida) {
            $inventario = Inventario::where('id_bodega', $salida->id_bodega)
                ->where('id_producto', $salida->id_producto)
                ->first();
            
            if ($inventario) {
                $inventario->increment('cantidad', $salida->cantidad);
            }
        }

        // Eliminar salidas anteriores
        SalidaInventario::where('numero_factura', $factura->prefijo . $factura->numero_factura)->delete();

        // Crear nuevas salidas de inventario
        foreach ($factura->detalles as $detalle) {
            SalidaInventario::create([
                'id_bodega' => $detalle->id_bodega,
                'id_producto' => $detalle->id_producto,
                'id_motivo' => $motivoVenta?->id ?? 1,
                'cantidad' => $detalle->cantidad,
                'precio_costo' => 0,
                'precio_venta' => $detalle->precio_venta,
                'numero_factura' => $factura->prefijo . $factura->numero_factura,
                'observacion' => "Salida por factura #{$factura->prefijo}{$factura->numero_factura} (editada)",
            ]);

            // Disminuir inventario
            $inventario = Inventario::where('id_bodega', $detalle->id_bodega)
                ->where('id_producto', $detalle->id_producto)
                ->first();

            if ($inventario) {
                $inventario->decrement('cantidad', $detalle->cantidad);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->before(function () {
                    // Restaurar inventario al eliminar factura
                    $factura = $this->record;
                    $salidas = SalidaInventario::where('numero_factura', $factura->prefijo . $factura->numero_factura)->get();
                    
                    foreach ($salidas as $salida) {
                        $inventario = Inventario::where('id_bodega', $salida->id_bodega)
                            ->where('id_producto', $salida->id_producto)
                            ->first();
                        
                        if ($inventario) {
                            $inventario->increment('cantidad', $salida->cantidad);
                        }
                    }

                    // Eliminar salidas
                    SalidaInventario::where('numero_factura', $factura->prefijo . $factura->numero_factura)->delete();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
