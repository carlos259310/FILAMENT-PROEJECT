<?php

namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewFactura extends ViewRecord
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        $estado = $this->record->estado->nombre;
        $esEditable = !in_array($estado, ['Pagada', 'Cancelada']);

        return array_filter([
            // Botón Editar - Solo visible si está pendiente
            $esEditable ? EditAction::make()
                ->icon('heroicon-o-pencil')
                ->color('primary') : null,

            // Mensaje informativo si no es editable
            !$esEditable ? Action::make('no_editable')
                ->label('Esta factura no es editable')
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->disabled()
                ->extraAttributes([
                    'class' => 'cursor-not-allowed opacity-60',
                ]) : null,

            // Botón volver siempre visible
            Action::make('back')
                ->label('Volver al Listado')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ]);
    }
}
