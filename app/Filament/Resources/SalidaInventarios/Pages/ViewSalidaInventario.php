<?php

namespace App\Filament\Resources\SalidaInventarios\Pages;

use App\Filament\Resources\SalidaInventarios\SalidaInventarioResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSalidaInventario extends ViewRecord
{
    protected static string $resource = SalidaInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
