<?php


namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\EstadoFactura;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asigna el estado "Pendiente" por defecto
        $estadoPendiente = EstadoFactura::where('nombre', 'Pendiente')->first();
        $data['id_estado'] = $estadoPendiente ? $estadoPendiente->id : null;
        return $data;
    }
}