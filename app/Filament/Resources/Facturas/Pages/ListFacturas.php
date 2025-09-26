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
                ->label('Anular')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->estado->nombre === 'Pagada')
                ->action(function ($record) {
                    $estadoCancelada = EstadoFactura::where('nombre', 'Cancelada')->first();
                    if ($estadoCancelada) {
                        $record->id_estado = $estadoCancelada->id;
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Factura anulada')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}