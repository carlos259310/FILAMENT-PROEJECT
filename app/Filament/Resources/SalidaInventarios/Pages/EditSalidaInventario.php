<?php

namespace App\Filament\Resources\SalidaInventarios\Pages;

use App\Filament\Resources\SalidaInventarios\SalidaInventarioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSalidaInventario extends EditRecord
{
    protected static string $resource = SalidaInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    //funcion redireccionar
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
