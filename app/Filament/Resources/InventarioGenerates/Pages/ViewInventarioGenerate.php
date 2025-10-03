<?php

namespace App\Filament\Resources\InventarioGenerates\Pages;

use App\Filament\Resources\InventarioGenerates\InventarioGenerateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInventarioGenerate extends ViewRecord
{
    protected static string $resource = InventarioGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
