<?php

namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Models\EstadoFactura;
use App\Models\Inventario;
use App\Models\MotivoSalida;
use App\Models\SalidaInventario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asigna el estado "Pendiente" por defecto
        $estadoPendiente = EstadoFactura::where('nombre', 'Pendiente')->first();
        $data['id_estado'] = $estadoPendiente?->id ?? 1;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $factura = $this->record;
        $motivoVenta = MotivoSalida::where('nombre', 'Venta')->first();

        // Crear salidas de inventario para cada detalle de la factura
        foreach ($factura->detalles as $detalle) {
            // Crear registro de salida
            SalidaInventario::create([
                'id_bodega' => $detalle->id_bodega,
                'id_producto' => $detalle->id_producto,
                'id_motivo' => $motivoVenta?->id ?? 1,
                'cantidad' => $detalle->cantidad,
                'precio_costo' => 0, // Se podría obtener del inventario si es necesario
                'precio_venta' => $detalle->precio_venta,
                'numero_factura' => $factura->prefijo . $factura->numero_factura,
                'observacion' => "Salida por factura #{$factura->prefijo}{$factura->numero_factura}",
            ]);

            // Actualizar inventario (disminuir cantidad)
            $inventario = Inventario::where('id_bodega', $detalle->id_bodega)
                ->where('id_producto', $detalle->id_producto)
                ->first();

            if ($inventario) {
                $inventario->decrement('cantidad', $detalle->cantidad);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}