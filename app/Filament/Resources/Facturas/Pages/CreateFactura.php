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
        
        // Generar número de factura automático si no existe
        if (empty($data['numero_factura'])) {
            $data['numero_factura'] = $this->generarNumeroFactura();
        }
        
        return $data;
    }

    /**
     * Generar número de factura automático
     * Formato: 00000000 (8 dígitos con ceros a la izquierda)
     */
    private function generarNumeroFactura(): string
    {
        $ultimaFactura = \App\Models\Factura::latest('id')->first();
        $nuevoId = $ultimaFactura ? $ultimaFactura->id + 1 : 1;
        return str_pad($nuevoId, 8, '0', STR_PAD_LEFT);
    }

    protected function afterCreate(): void
    {
        $factura = $this->record;
        
        // Buscar o crear el motivo "Venta"
        $motivoVenta = MotivoSalida::firstOrCreate(
            ['nombre' => 'Venta'],
            ['nombre' => 'Venta']
        );

        // Crear salidas de inventario para cada detalle de la factura
        foreach ($factura->detalles as $detalle) {
            // Obtener inventario para precio de costo
            $inventario = Inventario::where('id_bodega', $detalle->id_bodega)
                ->where('id_producto', $detalle->id_producto)
                ->first();

            if (!$inventario) {
                continue; // Saltar si no hay inventario
            }

            // Crear registro de salida
            SalidaInventario::create([
                'id_bodega' => $detalle->id_bodega,
                'id_producto' => $detalle->id_producto,
                'id_motivo' => $motivoVenta->id,
                'cantidad' => $detalle->cantidad,
                'precio_costo' => $inventario->precio_compra ?? 0,
                'precio_venta' => $detalle->precio_venta,
                'numero_factura' => $factura->prefijo . $factura->numero_factura,
                'observacion' => "Salida por factura #{$factura->prefijo}{$factura->numero_factura}",
            ]);

            // Actualizar inventario (disminuir cantidad)
            $inventario->decrement('cantidad', $detalle->cantidad);
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}