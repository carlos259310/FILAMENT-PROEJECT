<?php

namespace App\Filament\Resources\InventarioGenerates\Pages;

use App\Filament\Resources\InventarioGenerates\InventarioGenerateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInventarioGenerate extends EditRecord
{
    protected static string $resource = InventarioGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
